<?php
/**
 * GLOBAL CONFIGURATION
 */

// --- SHARED UTILITY FUNCTIONS ---
// Centralized here so every page that includes config.php gets them.

if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) return [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) continue;
            list($name, $value) = explode('=', $line, 2);
            $env[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
        }
        return $env;
    }
}

if (!function_exists('getDiscordUser')) {
    function getDiscordUser($id, $bot_token) {
        if (!$id) return ['username' => 'Unknown', 'avatar' => ''];

        $sid = (string)$id;
        // --- EASTER EGG: NULL OVERRIDE ---
        if ($sid === '882' || $sid === 'NULL') {
            return [
                'username' => 'Null',
                'avatar'   => '/happynull.png'
            ];
        }

        // Check local cache first
        $cache_path = __DIR__ . '/users_cache.json';
        if (file_exists($cache_path)) {
            $cache = json_decode(file_get_contents($cache_path), true);
            if (isset($cache[$id])) {
                return [
                    'username' => $cache[$id]['username'] ?? 'Unknown',
                    'avatar'   => $cache[$id]['avatar'] ?? "https://www.gravatar.com/avatar/000000000000?d=mp"
                ];
            }
        }

        // Fallback to Discord API
        $url = "https://discord.com/api/v10/users/$id";
        $options = ['http' => ['header' => "Authorization: Bot $bot_token\r\n", 'method' => 'GET', 'ignore_errors' => true]];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result) {
            $data = json_decode($result, true);
            return [
                'username' => $data['username'] ?? 'Unknown',
                'avatar'   => isset($data['avatar']) ? "https://cdn.discordapp.com/avatars/$id/{$data['avatar']}.png" : "https://www.gravatar.com/avatar/000000000000?d=mp"
            ];
        }
        return ['username' => 'Unknown', 'avatar' => ''];
    }
}

// Whitelist of Discord IDs recognized as Administrators
$ALLOWED_DISCORD_IDS = [
    '332684782888550410',
    '613130816809336842',
    '701867923358351511'
];

/**
 * Whitelist of Discord IDs recognized as Read-Only Administrators.
 * These users can view the dashboard but cannot perform write operations.
 */
$READ_ONLY_ADMIN_IDS = [
    '701867923358351511'
];

// Reveal / Countdown Settings
$ENABLE_REVEAL_COUNTDOWN = false;          // Master toggle for the entire countdown feature
$REVEAL_START_TIME    = "2026-02-11T14:45:00-05:00"; // When the "Transmission" started (for % calculation)
$REVEAL_TARGET_TIME   = "2026-03-08T12:00:00-05:00"; // Format: YYYY-MM-DDTHH:MM:SS-Offset
$REVEAL_EMERGENCY_MSG = "Testing..."; // centered message

// Trading System Toggle
// Set to false to disable the creation of new trades.
$TRADES_ENABLED = true;

// --- GLOBAL NOTICE BANNER ---
$GLOBAL_BANNER_ENABLED = false; // Set to true to show the banner
$GLOBAL_BANNER_TEXT    = "I like trains.";
$GLOBAL_BANNER_TYPE    = "info"; // info, warning, critical,danger

// Reveal Timestamp Calculation
$reveal_timestamp = strtotime($REVEAL_TARGET_TIME);

// (Maintenance logic removed - controlled via .htaccess)

// Redirect to wait.php if reveal is active and we are NOT on wait.php/auth.php/server.php
if (isset($ENABLE_REVEAL_COUNTDOWN) && $ENABLE_REVEAL_COUNTDOWN && $reveal_timestamp !== false && time() < $reveal_timestamp) {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ILLUSIONARY_SID');
        session_start();
    }
    $user_id = $_SESSION['user_data']['id'] ?? null;
    $is_admin = $user_id ? isAdmin($user_id) : false;

    $current_page = basename($_SERVER['PHP_SELF']);
    $is_callback = strpos($_SERVER['PHP_SELF'], '/callback/') !== false;
    if ($current_page !== 'wait.php' && $current_page !== 'auth.php' && $current_page !== 'server.php' && !$is_callback && !$is_admin) {
        if (defined('IS_API')) {
            http_response_code(503);
            header('Content-Type: application/json');
            die(json_encode(['success' => false, 'error' => 'System offline until reveal.']));
        }
        header("Location: /wait.php");
        exit;
    }
}

// --- TERMS OF SERVICE GATE ---
if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
    $current_page = basename($_SERVER['PHP_SELF']);
    // Avoid redirect loop and allow logout/auth pages
    $allowed_pages = ['tos.php', 'auth.php', 'server.php', 'logout.php'];
    $is_callback = strpos($_SERVER['PHP_SELF'], '/callback/') !== false;
    $is_api = defined('IS_API');

    if (!in_array($current_page, $allowed_pages) && !$is_callback && !$is_api) {
        // Fetch ToS status if not in session or to ensure fresh data
        if (!isset($_SESSION['tos_accepted'])) {
            // We need a DB connection here if not already present
            // However, most pages establish their own. For the gate, we'll check if we can query.
            // A more performant way is to store it in the session upon login.
        }

        if (isset($_SESSION['tos_accepted']) && $_SESSION['tos_accepted'] == 0) {
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $is_mobile = preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua);
            $tos_path = $is_mobile ? "/mobile/tos.php" : "/tos.php";
            header("Location: $tos_path");
            exit;
        }
    }
}


// --- PAWNSHOP CONFIGURATION ---
$PAWNSHOP_ROTATION_OFFSET = 4; // Increment this to manually force a rotation of daily cards.
$CARD_RARITY_VALUES = [
    'common'    => 1,
    'uncommon'  => 1,
    'rare'      => 2,
    'epic'      => 3,
    'legendary' => 4,
    'unique'    => 5,
    'relic'     => 6
];

$VARIATION_BONUSES = [
    // Tier 1: Filter/Style Variants (+10%)
    'static'     => 0.10,
    'legacy'     => 0.10,
    'phase'      => 0.10,
    'inverted'   => 0.10,
    'toxic'      => 0.15,
    'blueprint'  => 0.15,

    // Tier 2: Animated/Premium Variants (+30%)
    'gold'       => 0.30,
    'shimmer'    => 0.30,
    'pulse'      => 0.30,
    'ghost'      => 0.35,

    // Tier 3: Rare/Corrupted/Holo Variants (+60-100%)
    'glitch'     => 0.60,
    'holo'       => 0.75,
    'corrupted'  => 0.85,
    'blood-moon' => 1.00
];

// Helper to check if a user is an admin
function isAdmin($userId) {
    global $ALLOWED_DISCORD_IDS;
    return in_array($userId, $ALLOWED_DISCORD_IDS);
}

// Helper to check if an admin is restricted to Read-Only mode
function isReadOnlyAdmin($userId) {
    global $READ_ONLY_ADMIN_IDS;
    return in_array($userId, $READ_ONLY_ADMIN_IDS);
}

// --- SECURE SERIAL NUMBER SCRAMBLER ---
// Deterministically turns a sequential ID into a unique 9-digit code
function scrambleSN($id) {
    return (($id * 15485863) + 12345) % 1000000000;
}

// Internal version: Only used if you need to find an ID from a code
function unscrambleSN($code) {
    // Modular Multiplicative Inverse of 15485863 modulo 1,000,000,000 is 723835927
    $val = ($code - 12345);
    while ($val < 0) $val += 1000000000;
    
    // We use integer math as 64-bit PHP handles this product (1.1 * 10^16) 
    // without losing precision, unlike floating point doubles.
    return ($val * 723835927) % 1000000000;
}
?>
