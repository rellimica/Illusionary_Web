<?php
/**
 * LEADERBOARD & PUBLIC STATS API
 */
require_once 'init.php';

// --- PROTECTION ---
guard($_GET, ['action' => 'required']);

// --- HELPERS ---
// Reusing cached Discord user logic for performance
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
checkScope('read');

if (isset($_GET['action'])) {
    // 1. Top Collectors By Card Count
    if ($_GET['action'] === 'get_top_collectors') {
        $stmt = $pdo->query("
            SELECT uc.user_discord_id, SUM(uc.count) as total_cards, COUNT(DISTINCT uc.card_id) as unique_cards 
            FROM user_cards uc 
            JOIN cards c ON uc.card_id = c.id 
            WHERE c.is_hidden = 0 
            GROUP BY uc.user_discord_id 
            ORDER BY total_cards DESC 
            LIMIT 10
        ");
        $rows = $stmt->fetchAll();
        $leaderboard = [];
        foreach ($rows as $player) {
            $u = getDiscordUserCached($player['user_discord_id'], $DISCORD_TOKEN);
            $leaderboard[] = array_merge($player, ['username' => $u['username'], 'avatar' => $u['avatar']]);
        }
        echo json_encode(['success' => true, 'data' => $leaderboard]);
        exit;
    }

    // 2. Luckiest (Highest Average Rarity)
    if ($_GET['action'] === 'get_luckiest') {
        $stmt = $pdo->query("
            SELECT uc.user_discord_id, SUM(uc.count) as total_cards, AVG(c.rarity_tier) as luck_score 
            FROM user_cards uc 
            JOIN cards c ON uc.card_id = c.id 
            WHERE c.is_hidden = 0 
            GROUP BY uc.user_discord_id 
            HAVING total_cards >= 3 
            ORDER BY luck_score ASC 
            LIMIT 10
        ");
        $rows = $stmt->fetchAll();
        $luckiest = [];
        foreach ($rows as $player) {
            $u = getDiscordUserCached($player['user_discord_id'], $DISCORD_TOKEN);
            $luckiest[] = array_merge($player, ['username' => $u['username'], 'avatar' => $u['avatar']]);
        }
        echo json_encode(['success' => true, 'data' => $luckiest]);
        exit;
    }

    // 3. Global Stats
    if ($_GET['action'] === 'get_global_stats') {
        $total_cards = $pdo->query("SELECT COUNT(*) FROM cards")->fetchColumn();
        $total_supply = $pdo->query("SELECT SUM(count) FROM user_cards")->fetchColumn() ?: 0;
        $unique_owners = $pdo->query("SELECT COUNT(DISTINCT user_discord_id) FROM user_cards")->fetchColumn();
        $global_uniques = $pdo->query("SELECT COUNT(DISTINCT card_id) FROM user_cards")->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'card_types' => $total_cards,
                'total_circulating' => $total_supply,
                'active_collectors' => $unique_owners,
                'uniques_found' => $global_uniques
            ]
        ]);
        exit;
    }
}
