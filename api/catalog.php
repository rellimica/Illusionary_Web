<?php
/**
 * CARD CATALOG & LIBRARY API
 */
require_once 'init.php';

// --- PROTECTION ---
guard($_GET, ['action' => 'required']);
if ($_GET['action'] === 'get_card_details') {
    guard($_GET, ['card_id' => 'num|required']);
}

// --- ACTIONS ---
checkScope('read');

if (isset($_GET['action'])) {
    // 1. Get Global Catalog (Discoverable cards)
    if ($_GET['action'] === 'get_catalog') {
        // We only show cards that is_hidden = 0
        $stmt = $pdo->prepare("
            SELECT c.*, 
            COALESCE(SUM(uc.count), 0) as global_count, 
            COUNT(DISTINCT uc.user_discord_id) as global_owners,
            (SELECT 1 FROM user_cards WHERE user_discord_id = ? AND card_id = c.id LIMIT 1) as is_owned_by_me
            FROM cards c
            LEFT JOIN user_cards uc ON c.id = uc.card_id
            WHERE c.is_hidden = 0
            GROUP BY c.id
            ORDER BY c.card_order ASC
        ");
        $stmt->execute([$my_id]);
        $cards = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $cards]);
        exit;
    }

    // 2. Get Card Details (Public view)
    if ($_GET['action'] === 'get_card_details') {
        $cid = $_GET['card_id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM cards WHERE id = ? AND is_hidden = 0");
        $stmt->execute([$cid]);
        $card = $stmt->fetch();
        
        if (!$card) {
            echo json_encode(['success' => false, 'error' => 'Card not found or classified.']);
            exit;
        }

        // Fetch top holders for this specific card
        $stmt = $pdo->prepare("SELECT user_discord_id, count FROM user_cards WHERE card_id = ? ORDER BY count DESC LIMIT 5");
        $stmt->execute([$cid]);
        $holders_raw = $stmt->fetchAll();
        $holders = [];
        // Note: For privacy, we might want to anonymize this in a real public API, 
        // but for this project's aesthetic, showing the Discord names is usually preferred.
        $BOT_DIR = '/root/Illusionary'; // Re-defined just in case or use init vars
        foreach ($holders_raw as $h) {
            $url = "https://discord.com/api/v10/users/" . $h['user_discord_id'];
            $options = ['http' => ['header' => "Authorization: Bot $DISCORD_TOKEN\r\n", 'method' => 'GET']];
            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
            $u_name = "Collector#".$h['user_discord_id'];
            if ($result) {
                $d = json_decode($result, true);
                $u_name = $d['username'] ?? $u_name;
            }
            $holders[] = ['username' => $u_name, 'count' => $h['count']];
        }

        echo json_encode([
            'success' => true, 
            'data' => [
                'card' => $card,
                'distribution' => $holders
            ]
        ]);
        exit;
    }
}
