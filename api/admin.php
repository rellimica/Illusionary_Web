<?php
/**
 * ADMINISTRATIVE API
 */
require_once 'init.php';

// --- PROTECTION ---
if (isset($_POST['admin_action'])) guard($_POST, ['admin_action' => 'required']);
if (isset($_POST['ajax_action'])) guard($_POST, ['ajax_action' => 'required']);
if (isset($_GET['action'])) guard($_GET, ['action' => 'required']);

// Additional Admin Check
checkScope('admin');
if (!isAdmin($my_id) && $request_scope !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized Administrator Access.']);
    exit;
}

// Block write operations for read-only administrators
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isReadOnlyAdmin($my_id)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Permission Denied: You are in Read-Only mode.']);
    exit;
}

// Additional Helpers from admin.php
$CACHE_FILE = '../users_cache.json';
function getDiscordUserCached($id, $bot_token) {
    global $CACHE_FILE;
    static $cache = null;
    $seven_days = 7 * 24 * 60 * 60;
    if ($cache === null) { $cache = file_exists($CACHE_FILE) ? json_decode(file_get_contents($CACHE_FILE), true) : []; }
    if (isset($cache[$id]) && isset($cache[$id]['timestamp']) && (time() - $cache[$id]['timestamp'] < $seven_days)) { return $cache[$id]; }
    $url = "https://discord.com/api/v10/users/$id";
    $options = ['http' => ['header' => "Authorization: Bot $bot_token\r\nUser-Agent: IllusionaryDashboard (1.0)\r\n", 'method' => 'GET', 'ignore_errors' => true]];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['username'])) {
            $user_info = ['username' => $data['username'], 'avatar' => $data['avatar'] ? "https://cdn.discordapp.com/avatars/$id/{$data['avatar']}.png?size=64" : "https://www.gravatar.com/avatar/000000000000?d=mp", 'timestamp' => time()];
            $cache[$id] = $user_info;
            file_put_contents($CACHE_FILE, json_encode($cache));
            return $user_info;
        }
    }
    return $cache[$id] ?? ['username' => 'Unknown User', 'avatar' => "https://www.gravatar.com/avatar/000000000000?d=mp", 'timestamp' => 0];
}

// --- ACTIONS ---

// A. Hydrate Admin Data
if (isset($_GET['action']) && $_GET['action'] === 'hydrate_admin') {
    $total_cards = $pdo->query("SELECT COUNT(*) FROM cards")->fetchColumn();
    $total_supply = $pdo->query("SELECT SUM(count) FROM user_cards")->fetchColumn() ?: 0;
    $unique_owners = $pdo->query("SELECT COUNT(DISTINCT user_discord_id) FROM user_cards")->fetchColumn();
    $global_uniques = $pdo->query("SELECT COUNT(DISTINCT card_id) FROM user_cards")->fetchColumn();
    $rarity_dist = $pdo->query("SELECT c.rarity_name, SUM(uc.count) as total FROM user_cards uc JOIN cards c ON uc.card_id = c.id GROUP BY c.rarity_name")->fetchAll();
    $recent_draws = $pdo->query("SELECT uc.*, c.name as card_name, c.filename, c.rarity_name, UNIX_TIMESTAMP(uc.timestamp) as unix_ts FROM user_cards uc JOIN cards c ON uc.card_id = c.id ORDER BY (CASE WHEN uc.timestamp IS NOT NULL THEN uc.timestamp ELSE 0 END) DESC LIMIT 15")->fetchAll();
    $feed = [];
    foreach ($recent_draws as $draw) {
        $u = getDiscordUserCached($draw['user_discord_id'], $DISCORD_TOKEN);
        $feed[] = array_merge($draw, ['username' => $u['username'], 'avatar' => $u['avatar']]);
    }
    $top_collectors = $pdo->query("SELECT user_discord_id, SUM(count) as total_cards, COUNT(DISTINCT card_id) as unique_cards FROM user_cards GROUP BY user_discord_id ORDER BY total_cards DESC LIMIT 10")->fetchAll();
    $leaderboard = [];
    foreach ($top_collectors as $player) {
        $u = getDiscordUserCached($player['user_discord_id'], $DISCORD_TOKEN);
        $leaderboard[] = array_merge($player, ['username' => $u['username'], 'avatar' => $u['avatar']]);
    }
    $lucky_collectors = $pdo->query("SELECT uc.user_discord_id, SUM(uc.count) as total_cards, AVG(c.rarity_tier) as luck_score FROM user_cards uc JOIN cards c ON uc.card_id = c.id GROUP BY uc.user_discord_id HAVING total_cards >= 3 ORDER BY luck_score ASC LIMIT 10")->fetchAll();
    $luckiest = [];
    foreach ($lucky_collectors as $player) {
        $u = getDiscordUserCached($player['user_discord_id'], $DISCORD_TOKEN);
        $luckiest[] = array_merge($player, ['username' => $u['username'], 'avatar' => $u['avatar']]);
    }
    $card_breakdown = $pdo->query("SELECT c.*, COALESCE(SUM(uc.count), 0) as current_supply, COUNT(DISTINCT uc.user_discord_id) as owner_count FROM cards c LEFT JOIN user_cards uc ON c.id = uc.card_id GROUP BY c.id ORDER BY c.card_order ASC")->fetchAll();

    echo json_encode([
        'stats' => ['total_cards' => $total_cards, 'total_supply' => $total_supply, 'unique_owners' => $unique_owners, 'global_uniques' => $global_uniques],
        'rarity_dist' => $rarity_dist, 'feed' => $feed, 'leaderboard' => $leaderboard, 'luckiest' => $luckiest, 'cards' => $card_breakdown
    ]);
    exit;
}

// A.1 User Lookup
if (isset($_GET['action']) && $_GET['action'] === 'lookup_user') {
    $uid = $_GET['uid'];
    $u = getDiscordUserCached($uid, $DISCORD_TOKEN);
    
    // Fetch local database stats (Mana/Tokens)
    $local = $pdo->prepare("SELECT tokens FROM users WHERE discord_id = ?");
    $local->execute([$uid]);
    $local_user = $local->fetch();
    $tokens = $local_user ? (int)$local_user['tokens'] : 0;

    $collection_raw = $pdo->prepare("SELECT uc.*, c.name, c.filename, c.rarity_name FROM user_cards uc JOIN cards c ON uc.card_id = c.id WHERE uc.user_discord_id = ?");
    $collection_raw->execute([$uid]);
    $collection = $collection_raw->fetchAll();

    // Fetch all instance IDs (SNs) for this user
    $inst_stmt = $pdo->prepare("SELECT id, card_id FROM card_instances WHERE user_discord_id = ? ORDER BY id ASC");
    $inst_stmt->execute([$uid]);
    $all_instances = $inst_stmt->fetchAll();

    foreach ($collection as &$card) {
        $card['sns'] = array_values(array_map(function($i) { return scrambleSN($i['id']); },
            array_filter($all_instances, function($i) use ($card) {
                return $i['card_id'] == $card['card_id'];
            })
        ));
    }

    echo json_encode([
        'user' => array_merge(['user_discord_id' => $uid, 'tokens' => $tokens], $u), 
        'collection' => $collection
    ]);
    exit;
}

// B. Card Management Actions
if (isset($_POST['admin_action'])) {
    if ($_POST['admin_action'] === 'save_card') {
        $is_hidden = isset($_POST['c_hidden']) ? 1 : 0;
        $is_trade  = isset($_POST['c_trade']) ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE cards SET name = ?, rarity_name = ?, rarity_tier = ?, filename = ?, card_order = ?, is_hidden = ?, is_trade = ? WHERE id = ?");
        $success = $stmt->execute([$_POST['c_name'], $_POST['c_rarity'], $_POST['c_tier'], $_POST['c_file'], $_POST['c_order'], $is_hidden, $is_trade, $_POST['c_id']]);
        echo json_encode(['success' => $success]);
        exit;
    }
    
    if ($_POST['admin_action'] === 'add_owner') {
        $pdo->beginTransaction();
        try {
            // 1. Update summary
            $stmt = $pdo->prepare("INSERT INTO user_cards (user_discord_id, card_id, count) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE count = count + 1");
            $stmt->execute([$_POST['new_owner_id'], $_POST['c_id']]);
            
            // 2. Create specific instance (SN)
            $stmt = $pdo->prepare("INSERT INTO card_instances (card_id, user_discord_id, source) VALUES (?, ?, 'admin')");
            $stmt->execute([$_POST['c_id'], $_POST['new_owner_id']]);
            $sn = $pdo->lastInsertId();

            $pdo->commit();
            echo json_encode(['success' => true, 'sn' => $sn]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            errorResponse($e->getMessage(), 500);
        }
        exit;
    }

    if ($_POST['admin_action'] === 'save_system_config') {
        $config_path = '../config.php';
        $content = file_get_contents($config_path);

        $updates = [
            'GLOBAL_MAINTENANCE_LOCK' => $_POST['maint_lock'] === '1' ? 'true' : 'false',
            'TRADES_ENABLED'          => $_POST['trades_enabled'] === '1' ? 'true' : 'false',
            'ENABLE_REVEAL_COUNTDOWN' => $_POST['reveal_countdown'] === '1' ? 'true' : 'false',
            'REVEAL_START_TIME'       => $_POST['reveal_start'],
            'REVEAL_TARGET_TIME'      => $_POST['reveal_target'],
            'REVEAL_EMERGENCY_MSG'    => $_POST['reveal_msg']
        ];

        foreach ($updates as $key => $value) {
            if ($value === 'true' || $value === 'false') {
                // Handle Booleans
                $pattern = '/(\$' . $key . '\s*=\s*)(true|false)(\s*;)/';
                $content = preg_replace($pattern, '${1}' . $value . '${3}', $content);
            } else {
                // Handle Strings
                $pattern = '/(\$' . $key . '\s*=\s*")([^"]*)(")/';
                $content = preg_replace($pattern, '${1}' . addslashes($value) . '${3}', $content);
            }
        }

        if (file_put_contents($config_path, $content) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to write to config.php.']);
        }
        exit;
    }
}

// C. Fetch Owners
if (isset($_GET['fetch_owners'])) {
    $cid = $_GET['fetch_owners'];
    $stmt = $pdo->prepare("SELECT user_discord_id, count FROM user_cards WHERE card_id = ?");
    $stmt->execute([$cid]);
    $rows = $stmt->fetchAll();
    
    // Fetch all instances for these owners to match them up
    $inst_stmt = $pdo->prepare("SELECT id, user_discord_id FROM card_instances WHERE card_id = ? ORDER BY id ASC");
    $inst_stmt->execute([$cid]);
    $all_instances = $inst_stmt->fetchAll();
    
    $enriched = [];
    foreach ($rows as $row) {
        $u = getDiscordUserCached($row['user_discord_id'], $DISCORD_TOKEN);
        
        // Filter instances for this specific user
        $user_sns = array_values(array_map(function($i) { return scrambleSN($i['id']); }, 
            array_filter($all_instances, function($i) use ($row) { 
                return $i['user_discord_id'] == $row['user_discord_id']; 
            })
        ));

        $enriched[] = array_merge($row, [
            'username' => $u['username'], 
            'avatar' => $u['avatar'],
            'sns' => $user_sns
        ]);
    }
    echo json_encode($enriched);
    exit;
}

// D. AJAX Ownership Updates
if (isset($_POST['ajax_action'])) {
    if ($_POST['ajax_action'] === 'update_owner_count') {
        $new_qty = (int)$_POST['count'];
        $uid = $_POST['user_id'];
        $cid = $_POST['card_id'];

        $pdo->beginTransaction();
        try {
            // 1. Get current count
            $stmt = $pdo->prepare("SELECT count FROM user_cards WHERE user_discord_id = ? AND card_id = ?");
            $stmt->execute([$uid, $cid]);
            $old_qty = (int)($stmt->fetchColumn() ?: 0);

            // 2. Update summary
            $stmt = $pdo->prepare("UPDATE user_cards SET count = ? WHERE user_discord_id = ? AND card_id = ?");
            $stmt->execute([$new_qty, $uid, $cid]);

            // 3. Balance the instances
            if ($new_qty > $old_qty) {
                // Add instances
                $diff = $new_qty - $old_qty;
                $stmt = $pdo->prepare("INSERT INTO card_instances (card_id, user_discord_id, source) VALUES (?, ?, 'admin_sync')");
                for ($i = 0; $i < $diff; $i++) $stmt->execute([$cid, $uid]);
            } elseif ($new_qty < $old_qty) {
                // Delete instances (removes the most recent ones first)
                $diff = $old_qty - $new_qty;
                $stmt = $pdo->prepare("DELETE FROM card_instances WHERE user_discord_id = ? AND card_id = ? ORDER BY id DESC LIMIT ?");
                $stmt->bindValue(1, $uid);
                $stmt->bindValue(2, $cid);
                $stmt->bindValue(3, $diff, PDO::PARAM_INT);
                $stmt->execute();
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            errorResponse($e->getMessage(), 500);
        }
        exit;
    }
    if ($_POST['ajax_action'] === 'delete_owner') {
        $pdo->beginTransaction();
        try {
            // 1. Delete from summary
            $stmt = $pdo->prepare("DELETE FROM user_cards WHERE user_discord_id = ? AND card_id = ?");
            $stmt->execute([$_POST['user_id'], $_POST['card_id']]);

            // 2. Delete all instances
            $stmt = $pdo->prepare("DELETE FROM card_instances WHERE user_discord_id = ? AND card_id = ?");
            $stmt->execute([$_POST['user_id'], $_POST['card_id']]);

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            errorResponse($e->getMessage(), 500);
        }
        exit;
    }
    if ($_POST['ajax_action'] === 'update_mana') {
        $stmt = $pdo->prepare("INSERT INTO users (discord_id, tokens) VALUES (?, ?) ON DUPLICATE KEY UPDATE tokens = ?");
        $success = $stmt->execute([$_POST['user_id'], $_POST['tokens'], $_POST['tokens']]);
        echo json_encode(['success' => $success]);
        exit;
    }

    if ($_POST['ajax_action'] === 'unscramble_sn') {
        $code = (int)$_POST['code'];
        $db_id = unscrambleSN($code);
        
        $stmt = $pdo->prepare("SELECT ci.*, c.name, c.rarity_name, c.rarity_tier FROM card_instances ci JOIN cards c ON ci.card_id = c.id WHERE ci.id = ?");
        $stmt->execute([$db_id]);
        $details = $stmt->fetch();
        
        if ($details) {
            $u = getDiscordUserCached($details['user_discord_id'], $DISCORD_TOKEN);
            echo json_encode([
                'success' => true, 
                'db_id' => $db_id,
                'owner' => $u['username'],
                'owner_id' => $details['user_discord_id'],
                'card_name' => $details['name'],
                'rarity' => $details['rarity_name'],
                'tier' => $details['rarity_tier'],
                'acquired' => $details['obtained_at'],
                'source' => $details['source']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No active instance matches this scrambled code.']);
        }
        exit;
    }
}
