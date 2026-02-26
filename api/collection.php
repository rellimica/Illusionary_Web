<?php
/**
 * COLLECTION & STATS API
 */
require_once 'init.php';

// --- PROTECTION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    guard($_POST, ['action' => 'required']);
} else {
    guard($_GET, ['action' => 'required']);
}

if (isset($_GET['action']) && $_GET['action'] === 'hydrate_collection') {
    guard($_GET, ['page' => 'num']);
}

// --- ACTIONS ---

// 1. Claim Daily Mana
if (isset($_POST['action']) && $_POST['action'] === 'claim_mana') {
    checkScope('write');
    $stmt = $pdo->prepare("
        UPDATE users 
        SET tokens = tokens + 1, mana_available = 0 
        WHERE discord_id = ? AND (mana_available = 1 OR mana_available IS NULL)
    ");
    $stmt->execute([$my_id]);
    
    if ($stmt->rowcount() > 0) {
        $msg = "Mana Claimed!";
        $success = true;
    } else {
        // Double check if they just need a record created
        $check = $pdo->prepare("SELECT 1 FROM users WHERE discord_id = ?");
        $check->execute([$my_id]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO users (discord_id, tokens, mana_available) VALUES (?, 1, 0)")->execute([$my_id]);
            $msg = "Welcome! Mana Claimed!";
            $success = true;
        } else {
            $msg = "Mana already claimed today.";
            $success = false;
        }
    }
    
    echo json_encode(['success' => $success, 'message' => $msg]);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'hydrate_collection') {
    checkScope('read');
    
    // For bots/read-scoped keys, allow targeting a specific user via query param
    $target_id = $my_id;
    if (isset($_GET['user_id']) && $request_scope !== 'public') {
        $target_id = $_GET['user_id'];
    }

    $p = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $l = 10;
    $o = ($p - 1) * $l;

    // Determine visibility: Owners see hidden cards they own, others do not.
    $visibility_cond = ($target_id === $my_id) ? "(c.is_hidden = 0 OR uc.card_id IS NOT NULL)" : "c.is_hidden = 0";

    // Stats
    $user_stats = $pdo->prepare("SELECT SUM(count) as total_cards, COUNT(DISTINCT card_id) as unique_cards FROM user_cards WHERE user_discord_id = ?");
    $user_stats->execute([$target_id]);
    $st = $user_stats->fetch();

    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM cards c LEFT JOIN user_cards uc ON c.id = uc.card_id AND uc.user_discord_id = ? WHERE $visibility_cond");
    $total_stmt->execute([$target_id]);
    $tp = $total_stmt->fetchColumn();

    // Cards
    $cards_stmt = $pdo->prepare("
        SELECT c.*, uc.count, uc.timestamp 
        FROM cards c
        LEFT JOIN user_cards uc ON c.id = uc.card_id AND uc.user_discord_id = ?
        WHERE $visibility_cond
        ORDER BY c.card_order ASC
        LIMIT ? OFFSET ?
    ");
    $cards_stmt->bindValue(1, $target_id);
    $cards_stmt->bindValue(2, $l, PDO::PARAM_INT);
    $cards_stmt->bindValue(3, $o, PDO::PARAM_INT);
    $cards_stmt->execute();
    $cards = $cards_stmt->fetchAll();

    // 4. Fetch SNs for these cards
    if (!empty($cards)) {
        $inst_stmt = $pdo->prepare("SELECT id, card_id, message, variations FROM card_instances WHERE user_discord_id = ? ORDER BY id ASC");
        $inst_stmt->execute([$target_id]);
        $all_instances = $inst_stmt->fetchAll();

        foreach ($cards as &$card) {
            $card['sns'] = array_values(array_map(function($i) { 
                return [
                    'id' => (int)$i['id'],
                    'sn' => scrambleSN((int)$i['id']),
                    'message' => $i['message'] ?? null,
                    'variant' => $i['variations'] ?? null
                ];
            }, array_filter($all_instances, function($i) use ($card) {
                return $i['card_id'] == $card['id'];
            })));
        }
    }

    // 5. User Status
    $user_info = $pdo->prepare("SELECT tokens, (mana_available = 1 OR mana_available IS NULL) as can_claim FROM users WHERE discord_id = ?");
    $user_info->execute([$target_id]);
    $u_status = $user_info->fetch();

    echo json_encode([
        'stats' => [
            'total' => $st['total_cards'] ?: 0,
            'unique' => $st['unique_cards'] ?: 0,
            'possible' => $tp,
            'completion' => ($tp > 0) ? round(($st['unique_cards'] / $tp) * 100, 1) : 0
        ],
        'user' => [
            'tokens' => (int)($u_status['tokens'] ?? 0),
            'can_claim' => (bool)($u_status['can_claim'] ?? true)
        ],
        'cards' => $cards
    ]);
    exit;
}
