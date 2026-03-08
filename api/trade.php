<?php
/**
 * TRADING SYSTEM API
 */
require_once 'init.php';

// --- PROTECTION ---
if (isset($_POST['partner_id']) && $_POST['partner_id'] === 'NULL') {
    $_POST['partner_id'] = '882';
}

guard($_POST, ['action' => 'required']);
if ($_POST['action'] === 'propose_trade') {
    guard($_POST, ['partner_id' => 'id|required', 'your_offer' => 'json|required', 'their_offer' => 'json|required']);
}
if ($_POST['action'] === 'respond_trade') {
    guard($_POST, ['trade_id' => 'num|required', 'status' => 'required']);
}
if ($_POST['action'] === 'get_inventories' || $_POST['action'] === 'get_trade_details') {
    guard($_POST, ['partner_id' => 'id|optional', 'trade_id' => 'num|optional']);
}

// --- HELPERS (Optimized with Cache) ---
// function getDiscordUser is inherited from config.php via init.php

function validateTurnstile($token, $secret, $remoteip = null) {
    if (empty($token) || empty($secret)) {
        return ['success' => false, 'error-codes' => ['missing-input-response']];
    }
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = ['secret' => $secret, 'response' => $token];
    if ($remoteip) $data['remoteip'] = $remoteip;
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    return $response ? json_decode($response, true) : ['success' => false, 'error-codes' => ['connection-failed']];
}

// --- ACTIONS ---
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'propose_trade') {
        checkScope('write');
        // Global Trade Toggle Check
        if (!($TRADES_ENABLED ?? true)) {
            errorResponse('The Trading System is currently disabled by an administrator.', 403);
        }

        $partner_id = $_POST['partner_id'];
        $your_offer = json_decode($_POST['your_offer'], true);
        $their_offer = json_decode($_POST['their_offer'], true);

        // --- EASTER EGG: BLOCK NULL TRADES ---
        if ($partner_id === 'NULL' || $partner_id === '882') {
            errorResponse('Null does not trade with mere mortals. He only watches.', 403);
        }

        if (empty($your_offer) && empty($their_offer)) {
            errorResponse('Trade cannot be empty!', 400);
        }

        // Integrity Check: Verify ownership before allowing proposal
        $ownership_stmt = $pdo->prepare("SELECT count FROM user_cards WHERE user_discord_id = ? AND card_id = ?");
        $instance_stmt = $pdo->prepare("SELECT COUNT(*) FROM card_instances WHERE user_discord_id = ? AND card_id = ? AND id = ?");

        foreach ($your_offer as $cid => $ids) {
            $qty = is_array($ids) ? count($ids) : (int)$ids;
            $ownership_stmt->execute([$my_id, $cid]);
            $actual = $ownership_stmt->fetchColumn() ?: 0;
            if ($actual < $qty) {
                errorResponse("You do not have enough copies of card #$cid to propose this trade.", 400);
            }

            // Check specific instances if provided
            if (is_array($ids)) {
                foreach ($ids as $iid) {
                    if ($iid === null) continue;
                    $instance_stmt->execute([$my_id, $cid, $iid]);
                    if ($instance_stmt->fetchColumn() == 0) {
                        $sn = scrambleSN((int)$iid);
                        errorResponse("Verification Failed: You do not own card instance #$sn (Card #$cid).", 400);
                    }
                }
            }
        }
        foreach ($their_offer as $cid => $ids) {
            $qty = is_array($ids) ? count($ids) : (int)$ids;
            $ownership_stmt->execute([$partner_id, $cid]);
            $actual = $ownership_stmt->fetchColumn() ?: 0;
            if ($actual < $qty) {
                errorResponse("The partner does not have enough copies of card #$cid for this trade.", 400);
            }

            // Check specific instances if provided
            if (is_array($ids)) {
                foreach ($ids as $iid) {
                    if ($iid === null) continue;
                    $instance_stmt->execute([$partner_id, $cid, $iid]);
                    if ($instance_stmt->fetchColumn() == 0) {
                        $sn = scrambleSN((int)$iid);
                        errorResponse("Verification Failed: The partner does not own card instance #$sn (Card #$cid).", 400);
                    }
                }
            }
        }

        $ts_token = $_POST['turnstile_token'] ?? '';
        $ts_secret = $env['TURNSTYLE_SECRET_KEY'] ?? '';
        $remote_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        
        if (empty($ts_secret)) {
            errorResponse("Security configuration error: Secret key missing. Please contact an administrator.", 500);
        }

        $validation = validateTurnstile($ts_token, $ts_secret);
        if (!$validation['success']) {
            $errs = isset($validation['error-codes']) ? implode(', ', $validation['error-codes']) : 'unknown';
            errorResponse("Security Verification Failed ($errs). Please refresh and try again.", 403);
        }

        $cooldown_stmt = $pdo->prepare("SELECT (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(created_at)) as seconds_ago FROM trades WHERE sender_id = ? ORDER BY created_at DESC LIMIT 1");
        $cooldown_stmt->execute([$my_id]);
        $seconds_ago = $cooldown_stmt->fetchColumn();
        if ($seconds_ago !== false && (int)$seconds_ago < 60) {
            errorResponse('Proposal Cooldown: Please wait ' . (60 - (int)$seconds_ago) . 's.', 403);
        }

        $dup_stmt = $pdo->prepare("SELECT id FROM trades WHERE sender_id = ? AND receiver_id = ? AND status = 'pending'");
        $dup_stmt->execute([$my_id, $partner_id]);
        if ($dup_stmt->fetch()) {
            errorResponse('You already have an active proposal with this collector.', 400);
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO trades (sender_id, receiver_id) VALUES (?, ?)");
            $stmt->execute([$my_id, $partner_id]);
            $trade_id = $pdo->lastInsertId();

            $item_stmt = $pdo->prepare("INSERT INTO trade_items (trade_id, user_id, card_id, instance_ids) VALUES (?, ?, ?, ?)");
            
            // Process Your Offer (expecting array of instance IDs)
            foreach ($your_offer as $cid => $ids) {
                if (!is_array($ids)) $ids = array_fill(0, (int)$ids, null); // Fallback for old count-only format
                foreach ($ids as $iid) $item_stmt->execute([$trade_id, $my_id, $cid, $iid]);
            }

            // Process Their Offer
            foreach ($their_offer as $cid => $ids) {
                if (!is_array($ids)) $ids = array_fill(0, (int)$ids, null);
                foreach ($ids as $iid) $item_stmt->execute([$trade_id, $partner_id, $cid, $iid]);
            }

            $pdo->commit();

            // Notify Receiver
            $my_username = $_SESSION['user_data']['username'] ?? 'A collector';
            createNotification($pdo, $partner_id, 'trade_request', 'New Trade Proposal', "$my_username has sent you a trade offer.", "trade.php?trade_id=$trade_id");

            echo json_encode(['success' => true, 'trade_id' => $trade_id]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($e instanceof PDOException) throw $e;
            errorResponse($e->getMessage(), 500);
        }
        exit;
    }
    
    if ($_POST['action'] === 'respond_trade') {
        checkScope('write');
        $tid = $_POST['trade_id'];
        $status = $_POST['status']; // 'accepted', 'declined', 'cancelled'

        if ($status === 'cancelled') {
            $stmt = $pdo->prepare("UPDATE trades SET status = 'cancelled' WHERE id = ? AND sender_id = ? AND status = 'pending'");
            $stmt->execute([$tid, $my_id]);
            if ($stmt->rowCount() === 0) errorResponse("Could not cancel trade. It may have already been processed.", 400);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($status === 'declined') {
            $stmt = $pdo->prepare("UPDATE trades SET status = 'declined' WHERE id = ? AND receiver_id = ? AND status = 'pending'");
            $stmt->execute([$tid, $my_id]);
            if ($stmt->rowCount() === 0) errorResponse("Could not decline trade. It may have already been processed.", 400);

            // Notify Sender of Decline
            $t_stmt = $pdo->prepare("SELECT sender_id FROM trades WHERE id = ?");
            $t_stmt->execute([$tid]);
            $sid = $t_stmt->fetchColumn();
            $my_username = $_SESSION['user_data']['username'] ?? 'The partner';
            createNotification($pdo, $sid, 'trade_declined', 'Proposal Declined', "$my_username declined your trade offer.", "trade.php?trade_id=$tid");

            echo json_encode(['success' => true]);
            exit;
        }

        if ($status === 'accepted') {
            $pdo->beginTransaction();
            try {
                $t_stmt = $pdo->prepare("SELECT * FROM trades WHERE id = ? AND receiver_id = ? AND status = 'pending'");
                $t_stmt->execute([$tid, $my_id]);
                $trade = $t_stmt->fetch();
                if (!$trade) throw new Exception("Invalid trade or already processed.");

                $i_stmt = $pdo->prepare("
                    SELECT ti.*, c.name, c.filename, c.rarity_name 
                    FROM trade_items ti 
                    JOIN cards c ON ti.card_id = c.id 
                    WHERE ti.trade_id = ?
                ");
                $i_stmt->execute([$tid]);
                $items = $i_stmt->fetchAll();

                // Prepare statements for both verification and execution for better performance
                $check_inst_stmt     = $pdo->prepare("SELECT id FROM card_instances WHERE user_discord_id = ? AND card_id = ? AND id = ? FOR UPDATE");
                $check_card_stmt     = $pdo->prepare("SELECT count FROM user_cards WHERE user_discord_id = ? AND card_id = ? FOR UPDATE");
                $sub_stmt            = $pdo->prepare("UPDATE user_cards SET count = count - ? WHERE user_discord_id = ? AND card_id = ?");
                $add_stmt            = $pdo->prepare("INSERT INTO user_cards (user_discord_id, card_id, count) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE count = count + ?");
                $transfer_spec_stmt  = $pdo->prepare("UPDATE card_instances SET user_discord_id = ?, source = 'trade' WHERE id = ? AND user_discord_id = ?");
                $transfer_limit_stmt = $pdo->prepare("UPDATE card_instances SET user_discord_id = ?, source = 'trade' WHERE user_discord_id = ? AND card_id = ? LIMIT ?");

                foreach ($items as $item) {
                    $other_party = ($item['user_id'] === $trade['sender_id']) ? $trade['receiver_id'] : $trade['sender_id'];
                    
                    // 1. Verification (CRITICAL: FOR UPDATE locks these rows to prevent race conditions)
                    if (!empty($item['instance_ids'])) {
                        $check_inst_stmt->execute([$item['user_id'], $item['card_id'], $item['instance_ids']]);
                        if (!$check_inst_stmt->fetch()) {
                            $sn = scrambleSN((int)$item['instance_ids']);
                            throw new Exception("Transaction Aborted: Card Instance #$sn is no longer in the owner's vault.");
                        }
                    } else {
                        $check_card_stmt->execute([$item['user_id'], $item['card_id']]);
                        $actual = $check_card_stmt->fetchColumn() ?: 0;
                        if ($actual < 1) throw new Exception("Transaction Aborted: Required materials for Card #{$item['card_id']} are missing.");
                    }

                    // 2. Update summary counts (decrement Giver, increment Receiver)
                    $sub_stmt->execute([1, $item['user_id'], $item['card_id']]);
                    $add_stmt->execute([$other_party, $item['card_id'], 1, 1]);
                    
                    // 3. Transfer individual instances
                    if (!empty($item['instance_ids'])) {
                        $transfer_spec_stmt->execute([$other_party, $item['instance_ids'], $item['user_id']]);
                    } else {
                        // Fallback to picking any instance owned by them
                        $transfer_limit_stmt->execute([$other_party, $item['user_id'], $item['card_id'], 1]);
                    }
                }

                // 3. FINAL CLEANUP: Remove any zero-count entries for the participants involved in this trade
                $pdo->prepare("DELETE FROM user_cards WHERE count <= 0 AND user_discord_id IN (?, ?)")
                    ->execute([$trade['sender_id'], $trade['receiver_id']]);

                $pdo->prepare("UPDATE trades SET status = 'accepted' WHERE id = ?")->execute([$tid]);
                $pdo->commit();

                // Notify Sender of Acceptance
                $my_username = $_SESSION['user_data']['username'] ?? 'The partner';
                createNotification($pdo, $trade['sender_id'], 'trade_accepted', 'Transaction Completed!', "$my_username accepted your trade offer.", "trade.php?trade_id=$tid");

                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                if ($e instanceof PDOException) throw $e;
                errorResponse($e->getMessage(), 500);
            }
            exit;
        }
        exit;
    }

    if ($_POST['action'] === 'get_trade_details') {
        checkScope('read');
        $tid = $_POST['trade_id'] ?? null;
        if (!$tid) errorResponse("Missing Trade ID", 400);
        
        $t_stmt = $pdo->prepare("SELECT * FROM trades WHERE id = ?");
        $t_stmt->execute([$tid]);
        $trade = $t_stmt->fetch();
        if (!$trade) errorResponse("Trade not found", 404);

        // Security Check: Only parties of the trade can view details
        if ($trade['sender_id'] !== $my_id && $trade['receiver_id'] !== $my_id) {
            errorResponse("Unauthorized Access: You are not a party in this transmission.", 403);
        }

        $items_stmt = $pdo->prepare("
            SELECT ti.*, c.name, c.filename, c.rarity_name, ci.variations
            FROM trade_items ti 
            JOIN cards c ON ti.card_id = c.id 
            LEFT JOIN card_instances ci ON ti.instance_ids = ci.id
            WHERE ti.trade_id = ?
        ");
        $items_stmt->execute([$tid]);
        $items = $items_stmt->fetchAll();

        foreach($items as &$itm) {
            $itm['sn_display'] = !empty($itm['instance_ids']) ? scrambleSN((int)$itm['instance_ids']) : null;
            $itm['rarity_class'] = strtolower(str_replace(' ', '-', $itm['rarity_name'] ?? 'common'));
        }

        $sender_data = getDiscordUser($trade['sender_id'], $DISCORD_TOKEN);
        $receiver_data = getDiscordUser($trade['receiver_id'], $DISCORD_TOKEN);

        echo json_encode([
            'success' => true,
            'trade' => $trade,
            'items' => $items,
            'sender' => $sender_data,
            'receiver' => $receiver_data
        ]);
        exit;
    }

    if ($_POST['action'] === 'get_selection_data') {
        checkScope('read');
        
        $others = [];
        $seen_ids = [];

        // 1. MATCHMAKERS: People who have cards you don't (that are tradeable).
        $stmt = $pdo->prepare("
            SELECT uc.user_discord_id, SUM(uc.count) as card_count, 'Missing Card Match' as category
            FROM user_cards uc
            JOIN cards c ON uc.card_id = c.id
            LEFT JOIN user_cards mine ON mine.card_id = uc.card_id AND mine.user_discord_id = ?
            WHERE uc.user_discord_id != ? 
            AND (mine.count IS NULL OR mine.count = 0)
            AND c.is_hidden = 0
            AND c.is_trade = 1
            GROUP BY uc.user_discord_id
            ORDER BY card_count DESC
            LIMIT 4
        ");
        $stmt->execute([$my_id, $my_id]);
        foreach ($stmt->fetchAll() as $row) {
            $others[] = $row;
            $seen_ids[] = (string)$row['user_discord_id'];
        }

        // 2. MARKET ACTIVE: People who have traded recently.
        if (count($others) < 8) {
            $placeholders = empty($seen_ids) ? "''" : implode(',', array_fill(0, count($seen_ids), '?'));
            $sql = "
                SELECT u.user_discord_id, u.card_count, 'Market Active' as category
                FROM (
                    SELECT sender_id as uid FROM trades WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                    UNION
                    SELECT receiver_id as uid FROM trades WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                ) recent
                JOIN (SELECT user_discord_id, SUM(count) as card_count FROM user_cards GROUP BY user_discord_id) u ON u.user_discord_id = recent.uid
                WHERE recent.uid != ? AND recent.uid NOT IN ($placeholders)
                LIMIT 4
            ";
            $stmt = $pdo->prepare($sql);
            $params = array_merge([$my_id], $seen_ids);
            $stmt->execute($params);
            foreach ($stmt->fetchAll() as $row) {
                $others[] = $row;
                $seen_ids[] = (string)$row['user_discord_id'];
            }
        }

        // 3. THE ELITE: Top collectors by volume.
        if (count($others) < 12) {
            $limit = 12 - count($others);
            $placeholders = empty($seen_ids) ? "''" : implode(',', array_fill(0, count($seen_ids), '?'));
            $sql = "
                SELECT user_discord_id, SUM(count) as card_count, 'Top Collector' as category
                FROM user_cards
                WHERE user_discord_id != ? AND user_discord_id NOT IN ($placeholders)
                GROUP BY user_discord_id
                ORDER BY card_count DESC
                LIMIT $limit
            ";
            $stmt = $pdo->prepare($sql);
            $params = array_merge([$my_id], $seen_ids);
            $stmt->execute($params);
            foreach ($stmt->fetchAll() as $row) {
                $others[] = $row;
                $seen_ids[] = (string)$row['user_discord_id'];
            }
        }

        // Hydrate with Discord avatars/usernames
        foreach ($others as &$o) {
            $ou = getDiscordUser($o['user_discord_id'], $DISCORD_TOKEN);
            $o['username'] = $ou['username'];
            $o['avatar'] = $ou['avatar'];
        }

        $stmt = $pdo->prepare("SELECT * FROM trades WHERE (sender_id = ? OR receiver_id = ?) AND status = 'pending' ORDER BY created_at DESC");
        $stmt->execute([$my_id, $my_id]);
        $pending_raw = $stmt->fetchAll();
        $pending = [];
        foreach ($pending_raw as $t) {
            $other_id = ($t['sender_id'] === $my_id) ? $t['receiver_id'] : $t['sender_id'];
            $ou = getDiscordUser($other_id, $DISCORD_TOKEN);
            $pending[] = array_merge($t, [
                'other_username' => $ou['username'],
                'other_avatar' => $ou['avatar'],
                'is_outgoing' => ($t['sender_id'] === $my_id)
            ]);
        }

        echo json_encode(['success' => true, 'others' => $others, 'pending' => $pending]);
        exit;
    }

    if ($_POST['action'] === 'get_inventories') {
        checkScope('read');
        $partner_id = $_POST['partner_id'];
        
        // 1. Fetch Summary Inventories (Fetch all for self, only non-hidden for partner)
        $stmt = $pdo->prepare("SELECT uc.*, c.name, c.filename, c.rarity_name, c.is_trade FROM user_cards uc JOIN cards c ON uc.card_id = c.id WHERE uc.user_discord_id = ?");
        $stmt->execute([$my_id]);
        $my_inventory = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT uc.*, c.name, c.filename, c.rarity_name, c.is_trade FROM user_cards uc JOIN cards c ON uc.card_id = c.id WHERE uc.user_discord_id = ? AND c.is_hidden = 0");
        $stmt->execute([$partner_id]);
        $partner_inventory = $stmt->fetchAll();

        // 2. Fetch all Instance IDs (SNs)
        $inst_stmt = $pdo->prepare("SELECT id, card_id, user_discord_id, variations FROM card_instances WHERE user_discord_id IN (?, ?)");
        $inst_stmt->execute([$my_id, $partner_id]);
        $all_instances = $inst_stmt->fetchAll();

        // 3. Map SNs to the summary card objects
        $map_sns = function(&$inv, $uid) use ($all_instances) {
            foreach ($inv as &$card) {
                $card['sns'] = array_values(array_map(function($i) { 
                    return [
                        'id' => (int)$i['id'],
                        'sn' => scrambleSN((int)$i['id']),
                        'variant' => $i['variations'] ?? null
                    ];
                }, array_filter($all_instances, function($i) use ($card, $uid) {
                    return $i['card_id'] == $card['card_id'] && $i['user_discord_id'] == $uid;
                })));
            }
        };

        $map_sns($my_inventory, $my_id);
        $map_sns($partner_inventory, $partner_id);

        echo json_encode(['success' => true, 'my_cards' => $my_inventory, 'partner_cards' => $partner_inventory]);
        exit;
    }
}
