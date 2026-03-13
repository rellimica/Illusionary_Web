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

    // 2. Wealthiest (Total Vault Value)
    if ($_GET['action'] === 'get_luckiest' || $_GET['action'] === 'get_wealthiest') {
        $stmt = $pdo->query("
            SELECT 
                ci.user_discord_id, 
                SUM(
                    (CASE 
                        WHEN LOWER(c.rarity_name) = 'common' THEN 1
                        WHEN LOWER(c.rarity_name) = 'uncommon' THEN 1
                        WHEN LOWER(c.rarity_name) = 'rare' THEN 2
                        WHEN LOWER(c.rarity_name) = 'epic' THEN 3
                        WHEN LOWER(c.rarity_name) = 'legendary' THEN 4
                        WHEN LOWER(c.rarity_name) = 'unique' THEN 5
                        WHEN LOWER(c.rarity_name) = 'relic' THEN 6
                        ELSE 1
                    END) * 
                    (1 + CASE 
                        WHEN ci.variations IN ('static', 'legacy', 'phase', 'inverted') THEN 0.1
                        WHEN ci.variations IN ('toxic', 'blueprint') THEN 0.15
                        WHEN ci.variations IN ('gold', 'shimmer', 'pulse') THEN 0.3
                        WHEN ci.variations = 'ghost' THEN 0.35
                        WHEN ci.variations = 'glitch' THEN 0.6
                        WHEN ci.variations = 'holo' THEN 0.75
                        WHEN ci.variations = 'corrupted' THEN 0.85
                        WHEN ci.variations = 'blood-moon' THEN 1.0
                        ELSE 0
                    END) + 
                    (CASE WHEN ci.message IS NOT NULL AND ci.message != '' THEN 0.5 ELSE 0 END)
                ) as wealth_score,
                COUNT(*) as total_cards
            FROM card_instances ci
            JOIN cards c ON ci.card_id = c.id
            WHERE c.is_hidden = 0
            GROUP BY ci.user_discord_id
            ORDER BY wealth_score DESC
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

    // 4. Master Archivists (Unique Cards)
    if ($_GET['action'] === 'get_top_archivists') {
        $stmt = $pdo->query("
            SELECT user_discord_id, COUNT(DISTINCT card_id) as score, SUM(count) as total_cards
            FROM user_cards uc
            JOIN cards c ON uc.card_id = c.id
            WHERE c.is_hidden = 0
            GROUP BY user_discord_id
            ORDER BY score DESC
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

    // 5. The Inscribed (Personalized Cards)
    if ($_GET['action'] === 'get_top_inscribed') {
        $stmt = $pdo->query("
            SELECT user_discord_id, COUNT(*) as score
            FROM card_instances
            WHERE message IS NOT NULL AND message != ''
            GROUP BY user_discord_id
            ORDER BY score DESC
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

    // 6. Anomaly Hunters (Variation Collectors)
    if ($_GET['action'] === 'get_top_hunters') {
        $stmt = $pdo->query("
            SELECT user_discord_id, COUNT(*) as score
            FROM card_instances
            WHERE variations NOT IN ('standard', 'static', 'legacy')
            GROUP BY user_discord_id
            ORDER BY score DESC
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

    // 7. Top Traders (Completed Trades)
    if ($_GET['action'] === 'get_top_traders') {
        $stmt = $pdo->query("
            SELECT uid as user_discord_id, COUNT(*) as score
            FROM (
                SELECT sender_id as uid FROM trades WHERE status = 'accepted'
                UNION ALL
                SELECT receiver_id as uid FROM trades WHERE status = 'accepted'
            ) t
            GROUP BY uid
            ORDER BY score DESC
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

    // 8. Prolific Drawers (Total Draws)
    if ($_GET['action'] === 'get_top_drawers') {
        $stmt = $pdo->query("
            SELECT user_discord_id, COUNT(*) as score
            FROM card_instances
            WHERE source = 'draw'
            GROUP BY user_discord_id
            ORDER BY score DESC
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
