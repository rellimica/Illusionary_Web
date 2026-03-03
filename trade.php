<?php
/**
 * SECURE SYSTEM WARNING: 
 * NEVER share your session ID or cookies (like ILLUSIONARY_SID) with anyone. 
 * Sharing this ID is like giving someone your house keys; they can access 
 * your account and steal your cards.
 */
// Increase session lifetime to 30 days for a more seamless experience
$session_lifetime = 30 * 24 * 60 * 60;
ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);
session_name('ILLUSIONARY_SID');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mobile Detection
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua)) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header("Location: mobile/trade.php" . $qs);
    exit;
}

// 1. CONFIGURATION
require_once 'config.php';
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$IMAGES_PATH = 'images/images/';

$env = loadEnv($ENV_PATH);
$DISCORD_TOKEN = $env['DISCORD_TOKEN'] ?? '';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php?logout=1"); 
    exit;
}

// 4. DATABASE
try {
    $dsn = "mysql:host=" . ($env['MYSQL_HOST'] ?? 'localhost') . ";dbname=" . ($env['MYSQL_DATABASE'] ?? '') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $env['MYSQL_USER'] ?? '', $env['MYSQL_PASSWORD'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => true
    ]);
} catch (Exception $e) { 
    header("Location: db_error.php");
    exit;
}

/**
 * 4. AUTHENTICATION GATING
 * Users must be logged in via index.php to access the Trading Center.
 * We store their verified Discord ID in $my_id for all trade lookups.
 */
if (!isset($_SESSION['user_authenticated'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: auth.php?redirect=$redirect");
    exit;
}
$my_id = (string)$_SESSION['user_data']['id']; // Cast to string for safety with large 64-bit IDs

// Ensure tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id VARCHAR(64) NOT NULL,
    receiver_id VARCHAR(64) NOT NULL,
    status ENUM('pending', 'accepted', 'declined', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
$pdo->exec("CREATE TABLE IF NOT EXISTS trade_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trade_id INT NOT NULL,
    user_id VARCHAR(64) NOT NULL,
    card_id INT NOT NULL,
    count INT DEFAULT 1,
    FOREIGN KEY (trade_id) REFERENCES trades(id) ON DELETE CASCADE
)");

// --- ACTIONS (MIGRATED TO api/trade.php) ---
// The trading logic and AJAX handlers have been moved to the centralized API directory.



// 5. DATA FETCHING (Minimal for load)
$user_id = $my_id; 
$is_admin = isAdmin($user_id);

$partner_id = $_GET['partner'] ?? null;
$trade_view_id = $_GET['trade_id'] ?? null;
$partner_data = null;
$partner_cards = [];
$card_meta = [];
$pending_trades = [];
$others = [];

if ($partner_id || $trade_view_id) {
    $my_user = getDiscordUser($my_id, $DISCORD_TOKEN);
    
    if ($partner_id) {
        if ($partner_id === $my_id) { header("Location: trade.php"); exit; }
        $partner_data = getDiscordUser($partner_id, $DISCORD_TOKEN);
    } elseif ($trade_view_id) {
        $t_stmt = $pdo->prepare("SELECT * FROM trades WHERE id = ?");
        $t_stmt->execute([$trade_view_id]);
        $trade = $t_stmt->fetch();
        if ($trade) {
            // Security Check: Only parties of the trade can view details
            if ($trade['sender_id'] !== $my_id && $trade['receiver_id'] !== $my_id) {
                header("Location: trade.php");
                exit;
            }
            $other_party_id = ($trade['sender_id'] == $my_id) ? $trade['receiver_id'] : $trade['sender_id'];
            $other_party_data = getDiscordUser($other_party_id, $DISCORD_TOKEN);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Trading</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="variations.css">
    <link rel="preload" href="illusionary.png" as="image">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        /* Unified Layout */
        .trade-container {
            display: flex;
            height: calc(100vh - 80px);
            overflow: hidden;
            background: var(--bg-color);
        }

        /* Far Left: Trade Hub (Sidebar) */
        .trade-hub {
            width: 320px;
            background: rgba(0,0,0,0.3);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
        }
        .hub-section {
            padding: 20px;
            border-bottom: 1px solid var(--glass-border);
        }
        .hub-title {
            font-size: 0.65rem;
            color: var(--accent-secondary);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .hub-search input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            padding: 10px 15px;
            border-radius: 10px;
            color: white;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.3s;
        }
        .hub-search input:focus {
            border-color: var(--accent-secondary);
            background: rgba(255,255,255,0.08);
        }
        .hub-scroll-area {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .hub-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.02);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .hub-item:hover {
            background: rgba(255,255,255,0.03);
        }
        .hub-item.active {
            background: rgba(0, 229, 255, 0.05);
            border-left: 3px solid var(--accent-secondary);
        }
        .hub-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 1px solid var(--glass-border);
        }
        .hub-info { flex: 1; min-width: 0; }
        .hub-name { font-size: 0.8rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .hub-meta { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; }

        /* Main Interaction Area (The 3-Pane Trading Desk) */
        .trading-desk {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 340px 1fr;
            overflow: hidden;
            background: radial-gradient(circle at center, rgba(12, 10, 21, 0.5) 0%, var(--bg-color) 100%);
        }
        
        /* Sub-columns for Desk */
        .desk-column {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-right: 1px solid var(--glass-border);
        }
        .desk-column:last-child { border-right: none; }
        
        .column-header {
            padding: 15px 20px;
            background: rgba(12, 10, 21, 0.6);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .inventory-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
            gap: 10px;
        }

        .card-tile {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 6px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .card-tile:hover { transform: scale(1.05); border-color: var(--accent-secondary); }
        .card-tile img { width: 100%; border-radius: 6px; margin-bottom: 5px; }
        .card-tile .name { font-size: 0.7rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-tile .qty { font-size: 9px; color: var(--accent-secondary); font-weight: 900; }
        .card-tile.untradeable { opacity: 0.3; filter: grayscale(1); cursor: not-allowed; }

        /* Center Stage */
        .negotiation-stage {
            display: flex;
            flex-direction: column;
            background: rgba(0,0,0,0.2);
        }
        .stage-scroll { flex: 1; overflow-y: auto; padding: 25px; }
        .stage-title { font-size: 0.6rem; font-weight: 900; color: var(--text-muted); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 15px; display: flex; justify-content: space-between; }
        .staged-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 10px; min-height: 100px; background: rgba(255,255,255,0.02); border-radius: 15px; padding: 15px; border: 1px dashed var(--glass-border); margin-bottom: 25px; }
        .staged-card { position: relative; cursor: pointer; }
        .staged-card img { width: 100%; border-radius: 5px; }
        .staged-card .count-badge { position: absolute; top: -5px; right: -5px; background: var(--accent-primary); color: white; font-size: 0.6rem; font-weight: 900; padding: 2px 6px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); }

        /* Empty State */
        .empty-desk {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-align: center;
            padding: 40px;
        }
        .empty-desk h2 { font-family: 'Outfit'; color: #fff; margin-bottom: 10px; font-size: 2rem; }

        .lock-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: #ff4e4e;
            font-size: 0.5rem;
            font-weight: 900;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #ff4e4e;
        }

        .divider-label { display: flex; align-items: center; gap: 15px; margin: 15px 0; color: var(--text-muted); font-size: 0.55rem; font-weight: 900; letter-spacing: 3px; }
        .divider-label::before, .divider-label::after { content: ''; flex: 1; height: 1px; background: var(--glass-border); }

        @media (max-width: 1400px) {
            .trade-hub { width: 260px; }
            .trading-desk { grid-template-columns: 1fr 300px 1fr; }
        }

        /* Skeleton Loaders */
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 8px;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-hub-item { 
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
        }
        .skeleton-avatar { width: 35px; height: 35px; border-radius: 50%; }
        .skeleton-text-wrap { flex: 1; display: flex; flex-direction: column; gap: 8px; }
        .skeleton-text-line { height: 10px; border-radius: 4px; }
        .skeleton-text-line.short { width: 60%; }
        /* Dedicated Vault Intel Panel */
        .vault-intel-panel {
            background: rgba(12, 10, 21, 0.4);
            border-bottom: 1px solid var(--glass-border);
            padding: 20px;
            min-height: 100px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .vault-intel-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-secondary), transparent);
            opacity: 0.3;
        }

        .intel-placeholder {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
            opacity: 0.5;
            text-align: center;
            font-family: 'Outfit';
        }

        .intel-content {
            display: none; /* Shown via JS */
            animation: fadeInIntel 0.3s ease-out;
        }
        @keyframes fadeInIntel { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        .intel-name {
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 0.8rem;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .intel-name::after {
            content: '';
            flex-grow: 1;
            height: 1px;
            background: rgba(255,255,255,0.05);
        }

        .intel-sn-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            max-height: 85px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-secondary) transparent;
            padding: 5px 0;
        }
        .intel-sn-list::-webkit-scrollbar { display: none; }

        .sn-pill { 
            background: rgba(0, 0, 0, 0.3); 
            border: 1px solid rgba(0, 229, 255, 0.1); 
            padding: 4px 10px; 
            border-radius: 6px; 
            font-size: 10px; 
            color: var(--accent-secondary); 
            font-weight: 800; 
            font-family: 'Outfit';
            transition: all 0.2s;
            cursor: pointer;
            user-select: none;
        }
        .sn-pill:hover {
            border-color: var(--accent-secondary);
            background: rgba(0, 229, 255, 0.1);
            transform: translateY(-1px);
        }
        .sn-pill.active {
            background: var(--accent-secondary);
            color: #000;
            border-color: #fff;
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.3);
        }
        .sn-pill.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
            filter: grayscale(1);
        }

        .card-tile { position: relative; cursor: pointer; transition: all 0.2s ease; border: 2px solid transparent; border-radius: 12px; }
        .card-tile.locked-card {
            border-color: var(--accent-secondary) !important;
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.2);
            transform: scale(1.02);
            z-index: 5;
        }
        .card-tile:hover {
            z-index: 100 !important;
            transform: translateY(-5px);
            border-color: var(--accent-secondary);
            box-shadow: 0 10px 20px rgba(0,0,0,0.5), 0 0 15px rgba(0, 229, 255, 0.1);
        }
        /* Removed .card-info-overlay styles */

        .skeleton-card { aspect-ratio: 2/3; border-radius: 12px; }

        .help-btn {
            background: rgba(0, 229, 255, 0.05);
            border: 1px solid var(--accent-secondary);
            color: var(--accent-secondary);
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        .help-btn:hover { background: var(--accent-secondary); color: #000; box-shadow: 0 0 15px var(--accent-secondary); }

        /* Summary View Enhancements */
        .summary-overlay-header {
            grid-column: 1 / -1;
            padding: 30px;
            background: rgba(12, 10, 21, 0.8);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .verified-badge {
            background: rgba(0, 229, 255, 0.1);
            border: 1px solid var(--accent-secondary);
            color: var(--accent-secondary);
            font-size: 0.6rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .summary-title {
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 1.5rem;
            color: #fff;
            letter-spacing: 1px;
        }
        .summary-stage {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            background: var(--glass-border);
            overflow: hidden;
        }
        .summary-side {
            background: rgba(12, 10, 21, 0.5);
            padding: 40px;
            overflow-y: auto;
            position: relative;
        }
        .summary-side::after {
            content: attr(data-label);
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 3rem;
            font-weight: 900;
            color: rgba(255,255,255,0.03);
            text-transform: uppercase;
            font-family: 'Outfit';
            pointer-events: none;
        }
        .summary-item-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .summary-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 8px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s;
        }
        .summary-card:hover { transform: translateY(-5px); border-color: var(--accent-secondary); background: rgba(0, 229, 255, 0.05); }
        .summary-card img { width: 100%; border-radius: 8px; margin-bottom: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .summary-card .sn-label { font-size: 0.6rem; font-weight: 900; color: var(--accent-secondary); }
        .summary-card .name { font-size: 0.7rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .decision-footer {
            grid-column: 1 / -1;
            background: rgba(12, 10, 21, 0.9);
            padding: 30px;
            border-top: 1px solid var(--glass-border);
            display: flex;
            justify-content: center;
            gap: 20px;
            backdrop-filter: blur(20px);
        }
        .decision-btn {
            padding: 15px 40px;
            border-radius: 12px;
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .btn-accept {
            background: var(--accent-secondary);
            color: #000;
            border: none;
            box-shadow: 0 0 20px rgba(0, 229, 255, 0.2);
        }
        .btn-accept:hover { transform: scale(1.05); box-shadow: 0 0 30px rgba(0, 229, 255, 0.4); }
        .btn-decline {
            background: rgba(255, 78, 78, 0.1);
            border: 1px solid #ff4e4e;
            color: #ff4e4e;
        }
        .btn-decline:hover { background: #ff4e4e; color: #fff; box-shadow: 0 0 20px rgba(255, 78, 78, 0.3); }

        .status-banner {
            padding: 10px 30px;
            text-align: center;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-size: 0.8rem;
        }
        .status-accepted { background: rgba(77, 250, 124, 0.1); color: #4dfa7c; }
        .status-declined { background: rgba(255, 78, 78, 0.1); color: #ff4e4e; }
        .status-cancelled { background: rgba(255, 255, 255, 0.05); color: var(--text-muted); }

        .category-tag {
            font-size: 0.55rem;
            color: var(--accent-secondary);
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
            display: inline-block;
            background: rgba(0, 229, 255, 0.05);
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid rgba(0, 229, 255, 0.1);
        }
    </style>
</head>
<body class="dark-mode">
    <?php include 'mobile-block.php'; ?>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Trading Center';
    include 'nav.php'; 
    ?>


    <main>
        <?php 
        /**
         * VIEW ROUTER
         * 1. Selection View (Default): List of collectors & active requests.
         * 2. Active Trade View: View a specific pending proposal.
         * 3. Negotiation View: A side-by-side inventory comparison to build a deal.
         */
        ?>
        <div class="trade-container">
            <!-- FAR LEFT: TRADE HUB -->
            <aside class="trade-hub">
                <div class="hub-section">
                    <div class="hub-title">Search Partner</div>
                    <form action="trade.php" method="GET" class="hub-search">
                        <input type="text" name="partner" placeholder="Enter Discord ID..." required>
                        <div style="font-size: 0.6rem; margin-top: 8px; color: var(--accent-secondary); cursor: pointer; text-decoration: underline; opacity: 0.8;" onclick="startDiscordIdTour()">How to find a Discord ID?</div>
                    </form>
                </div>

                <div class="hub-scroll-area">
                    <!-- Inbox: Pending Trades -->
                    <div class="hub-section" style="border-bottom:none;">
                        <div class="hub-title" id="hubPendingTitle">Active Requests</div>
                        <div id="hubPendingList">
                            <!-- Hydrated via AJAX -->
                        </div>
                    </div>

                    <!-- Frequent: Recently Active Users -->
                    <div class="hub-section" style="border-top: 1px solid var(--glass-border);">
                        <div class="hub-title">Collector Pool</div>
                        <div id="hubCollectorsList">
                            <!-- Hydrated via AJAX -->
                        </div>
                    </div>
                </div>
            </aside>

            <!-- MAIN TRADING DESK -->
            <?php if ($partner_id): ?>
                <main class="trading-desk">
                    <!-- Desk Pane 1: Your Cards -->
                    <div class="desk-column">
                        <div class="column-header">
                            <img src="<?php echo $my_user['avatar']; ?>" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--accent-primary);">
                            <div style="font-family: 'Outfit'; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                                YOUR VAULT
                                <!-- Public Access Enabled -->

                            </div>
                        </div>
                        <!-- New Vault Intel Display -->
                        <div class="vault-intel-panel" id="mineVaultIntel">
                            <div class="intel-placeholder">Hover over a card to view Serial Numbers</div>
                            <div class="intel-content" id="mineIntelContent"></div>
                        </div>
                        <div class="inventory-scroll">
                            <div class="inventory-grid" id="myInventoryGrid">
                                <!-- Hydrated -->
                            </div>
                        </div>
                    </div>

                    <!-- Desk Pane 2: Negotiation Stage -->
                    <div class="negotiation-stage">
                        <div class="column-header" style="justify-content: space-between; border-bottom: 1px solid var(--glass-border);">
                            <span class="gradient-text" style="font-size: 0.9rem; letter-spacing: 3px; font-weight: 800;">THE EXCHANGE</span>
                            <div style="display: flex; gap: 10px;">
                                <button class="help-btn" style="padding: 5px 15px; margin: 0; transform: scale(0.9);" onclick="if(typeof startTradeTour === 'function') startTradeTour(); else startTutorial();">?</button>
                                <?php if ($TRADES_ENABLED ?? true): ?>
                                    <button class="claim-btn" style="padding: 8px 20px; font-size: 0.7rem; height: auto; border-radius: 8px;" onclick="proposeTrade()">Initiate Proposal</button>
                                <?php else: ?>
                                    <button class="claim-btn" style="padding: 8px 20px; font-size: 0.7rem; height: auto; border-radius: 8px; opacity: 0.6; cursor: not-allowed; filter: grayscale(1);" disabled title="Trading is temporarily disabled.">System Offline</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="stage-scroll">
                            <div class="stage-title"><span>YOU OFFER</span> <span id="myOfferCount" style="color: var(--accent-primary);">0 CARDS</span></div>
                            <div class="staged-grid" id="myOfferGrid"></div>

                            <div class="divider-label">FOR</div>

                            <div class="stage-title"><span>ACQUIRING</span> <span id="theirOfferCount" style="color: var(--accent-secondary);">0 CARDS</span></div>
                            <div class="staged-grid" id="theirOfferGrid"></div>
                        </div>

                        <div style="padding: 15px; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; gap: 8px;">
                            <div style="font-size: 0.5rem; color: var(--text-muted); font-weight: 900; letter-spacing: 2px; text-transform: uppercase;">Identity Verification</div>
                            <div class="cf-turnstile" data-sitekey="<?php echo $env['TURNSTILE_SITE_KEY'] ?? ''; ?>" data-theme="dark"></div>
                        </div>
                    </div>

                    <!-- Desk Pane 3: Their Cards -->
                    <div class="desk-column">
                        <div class="column-header">
                            <img src="<?php echo $partner_data['avatar']; ?>" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid var(--accent-secondary);">
                            <div style="font-family: 'Outfit'; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                                <?php echo strtoupper(htmlspecialchars($partner_data['username'])); ?>
                                <?php if ($partner_id === '332684782888550410'): ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- New Vault Intel Display -->
                        <div class="vault-intel-panel" id="theirsVaultIntel">
                            <div class="intel-placeholder">Hover over a card to view Serial Numbers</div>
                            <div class="intel-content" id="theirsIntelContent"></div>
                        </div>
                        <div class="inventory-scroll">
                            <div class="inventory-grid" id="partnerInventoryGrid">
                                <!-- Hydrated -->
                            </div>
                        </div>
                    </div>
                </main>

            <?php elseif ($trade_view_id): 
                $t_stmt = $pdo->prepare("SELECT * FROM trades WHERE id = ?");
                $t_stmt->execute([$trade_view_id]);
                $trade = $t_stmt->fetch();
                if (!$trade) { echo '<script>window.location.href="trade.php";</script>'; exit; }
                
                $items_stmt = $pdo->prepare("SELECT ti.*, c.name, c.filename, c.rarity_name FROM trade_items ti JOIN cards c ON ti.card_id = c.id WHERE ti.trade_id = ?");
                $items_stmt->execute([$trade_view_id]);
                $items = $items_stmt->fetchAll();
                
                foreach($items as &$itm) {
                    $itm['sn_display'] = !empty($itm['instance_ids']) ? scrambleSN((int)$itm['instance_ids']) : null;
                    $itm['rarity_class'] = strtolower(str_replace(' ', '-', $itm['rarity_name'] ?? 'common'));
                }

                $s_items = array_filter($items, function($i) use ($trade) { return $i['user_id'] == $trade['sender_id']; });
                $r_items = array_filter($items, function($i) use ($trade) { return $i['user_id'] == $trade['receiver_id']; });
                
                $sender_data = getDiscordUser($trade['sender_id'], $DISCORD_TOKEN);
                $receiver_data = getDiscordUser($trade['receiver_id'], $DISCORD_TOKEN);
                $is_receiver = ($trade['receiver_id'] == $my_id);
            ?>
                <!-- Premium Summary View -->
                <main style="flex: 1; display: flex; flex-direction: column; background: var(--bg-color); position: relative;">
                    <!-- Top Status Bar -->
                    <?php if ($trade['status'] !== 'pending'): ?>
                        <div class="status-banner status-<?php echo $trade['status']; ?>">
                            TRANSACTION <?php echo strtoupper($trade['status']); ?> • RECORDED <?php echo date('Y-m-d H:i', strtotime($trade['updated_at'])); ?>
                        </div>
                    <?php endif; ?>

                    <div class="summary-overlay-header">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <a href="trade.php" class="help-btn" style="margin:0; padding: 5px 15px; border-radius: 8px;">← BACK</a>
                            <div class="summary-title">PROPOSAL #<?php echo str_pad($trade_view_id, 4, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        <div class="verified-badge" title="Pessimistic Row Locking Active">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color:#4dfa7c"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Hardened Node
                        </div>
                    </div>

                    <div class="summary-stage" id="tradeSummaryStage">
                        <!-- Skeletons while hydrating -->
                        <div class="summary-side" data-label="LOADING">
                             <div class="skeleton" style="height: 100%; border-radius: 20px;"></div>
                        </div>
                        <div class="summary-side" data-label="LOADING">
                             <div class="skeleton" style="height: 100%; border-radius: 20px;"></div>
                        </div>
                    </div>

                    <?php if ($trade['status'] === 'pending'): ?>
                        <div class="decision-footer">
                            <?php if ($is_receiver): ?>
                                <button class="decision-btn btn-accept" onclick="respondTrade(<?php echo $trade_view_id; ?>, 'accepted')">Accept Proposal</button>
                                <button class="decision-btn btn-decline" onclick="respondTrade(<?php echo $trade_view_id; ?>, 'declined')">Decline</button>
                            <?php else: ?>
                                <button class="decision-btn btn-decline" style="border-color: var(--text-muted); color: var(--text-muted);" onclick="respondTrade(<?php echo $trade_view_id; ?>, 'cancelled')">Cancel Proposal</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </main>

            <?php else: ?>
                <!-- EMPTY STATE: WELCOME -->
                <main class="empty-desk" style="display: block; overflow-y: auto;">
                    <div style="max-width: 1000px; margin: 0 auto; width: 100%;">
                        <div style="text-align: center; margin-bottom: 50px; padding-top: 40px;">
                            <img src="illusionary.png" style="width: 100px; opacity: 0.5; margin-bottom: 1rem;" loading="lazy">
                            <h2 style="font-size: 2.5rem; margin-bottom: 5px;">Trading Floor</h2>
                            <p style="font-size: 0.9rem; opacity: 0.7;">Select a collector from the hub or browse active requests below.</p>
                        </div>

                        <!-- Main Active Requests -->
                        <div id="pendingTradesWrapper" style="display: none; margin-bottom: 50px;">
                            <div class="stage-title"><span>ACTIVE REQUESTS</span></div>
                            <div id="pendingTradesList" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <!-- Hydrated -->
                            </div>
                        </div>

                        <!-- Main Collector Pool -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; margin-bottom: 25px;">
                            <div class="stage-title" style="margin:0; border:none;"><span>COLLECTOR POOL</span></div>
                            <button class="help-btn" onclick="if(typeof startTradeTour === 'function') startTradeTour(); else startTutorial();" style="margin:0;">HOW TO TRADE</button>
                        </div>
                        <div id="activeCollectorsList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding-bottom: 50px;">
                            <!-- Hydrated -->
                        </div>
                    </div>
                </main>
            <?php endif; ?>
        </div>
    </main>

    </main>

    <script>
        // SECURITY WARNING
        console.log('%cSTOP!', 'color: #ff4e4e; font-size: 60px; font-weight: 800; text-shadow: 0 0 20px rgba(255, 78, 78, 0.5);');
        console.log('%cThis is a browser feature intended for developers. If someone told you to copy-paste something here to modify your Illusionary account or get free cards, it is a SCAM and will give them full access to your collection!', 'font-size: 16px; color: #fff;');
        console.log('%cNEVER share your ILLUSIONARY_SID with anyone.', 'font-size: 16px; color: #ff00ea; font-weight: bold;');

        /**
         * GLOBAL STATE
         * cardMeta: Stores details (names, filenames) of all cards in the current view.
         * myOffer / theirOffer: Track the cards currently on the 'Negotiation Stage'.
         */
        let cardMeta = {}; 
        const imagesPath = '<?php echo $IMAGES_PATH; ?>';
        const partnerID = '<?php echo $partner_id; ?>';
        const myID = '<?php echo $my_id; ?>';
        
        let myOffer = {}; // Format: { card_id: [instance_ids] }
        let theirOffer = {};
        
        let myInventoryMap = {};
        let theirInventoryMap = {};
        
        // Active picker state (Sticky selection)
        let stickyCard = { side: null, id: null };
        let hoverDebounce = null;
        let currentIntelId = { mine: null, theirs: null };

        /**
         * Vault Intel Logic: Static header info updates
         */
        function updateVaultIntel(side, card) {
            if (!card) return;
            // Prevent redundant re-renders of the same card
            // Removed cache check to allow forced updates (e.g. lock state change, SN toggling)
            currentIntelId[side] = card.card_id;

            // Priority: show the sticky card if it belongs to this side, else show hover card
            const isSticky = stickyCard.side === side && stickyCard.id === card.card_id;
            const isTradeable = parseInt(card.is_trade) === 1;
            
            const panel = document.getElementById(side + 'VaultIntel');
            const content = document.getElementById(side + 'IntelContent');
            const placeholder = panel.querySelector('.intel-placeholder');

            const offer = side === 'mine' ? myOffer : theirOffer;
            const selectedIds = offer[card.card_id] || [];

            const snPills = (card.sns || []).map(inst => {
                const iid = typeof inst === 'object' ? inst.id : inst;
                const sn = typeof inst === 'object' ? inst.sn : inst;
                const v = typeof inst === 'object' ? inst.variant : null;
                const vClass = v ? `variant-${v}` : '';
                const isActive = selectedIds.includes(iid);
                
                if (!isTradeable) {
                    return `<span class="sn-pill disabled ${vClass}">#${sn}</span>`;
                }
                return `<span class="sn-pill ${isActive ? 'active' : ''} ${vClass}" onclick="toggleSNInTrade('${side}', ${card.card_id}, ${iid})">#${sn}</span>`;
            }).join('');

            content.innerHTML = `
                <div class="intel-name" style="${isSticky ? 'color:var(--accent-secondary)' : ''}">
                    ${isSticky ? '🔒 ' : ''}${card.name.replace(/_/g, ' ')}
                </div>
                <div class="overlay-sn-title">
                    <span>${side === 'mine' ? 'YOUR' : 'THEIR'} VAULT</span>
                    <span style="color:var(--accent-secondary)">${isTradeable ? (isSticky ? 'Card locked' : 'Click card to lock') : 'UNTRADEABLE'}</span>
                </div>
                <div class="intel-sn-list">${snPills}</div>
            `;

            placeholder.style.display = 'none';
            content.style.display = 'block';
            panel.style.background = isSticky ? 'rgba(0, 229, 255, 0.1)' : (isTradeable ? 'rgba(0, 229, 255, 0.05)' : 'rgba(255, 0, 0, 0.05)');
        }

        function selectCard(side, cardId) {
            // Remove previous lock outline
            if (stickyCard.id !== null) {
                const old = document.querySelector(`.card-tile.locked-card`);
                if (old) old.classList.remove('locked-card');
            }

            if (stickyCard.side === side && stickyCard.id === cardId) {
                stickyCard = { side: null, id: null };
            } else {
                stickyCard = { side: side, id: cardId };
                // Add new lock outline
                const target = document.querySelector(`[data-side="${side}"][data-id="${cardId}"]`);
                if (target) target.classList.add('locked-card');
            }
            
            // Refresh visuals
            const map = side === 'mine' ? myInventoryMap : theirInventoryMap;
            if (map[cardId]) updateVaultIntel(side, map[cardId]);
        }

        function toggleSNInTrade(side, cardId, instanceId) {
            event.stopPropagation(); // Don't trigger the card select click
            const offer = side === 'mine' ? myOffer : theirOffer;
            if (!offer[cardId]) offer[cardId] = [];
            
            const idx = offer[cardId].indexOf(instanceId);
            if (idx > -1) {
                offer[cardId].splice(idx, 1);
                if (offer[cardId].length === 0) delete offer[cardId];
            } else {
                offer[cardId].push(instanceId);
            }
            
            renderStage();
            const map = side === 'mine' ? myInventoryMap : theirInventoryMap;
            updateVaultIntel(side, map[cardId]);
        }

        function clearVaultIntel(side) {
            currentIntelId[side] = null;
            const panel = document.getElementById(side + 'VaultIntel');
            const content = document.getElementById(side + 'IntelContent');
            const placeholder = panel.querySelector('.intel-placeholder');

            placeholder.style.display = 'flex';
            content.style.display = 'none';
            panel.style.background = 'rgba(12, 10, 21, 0.4)';
        }



        // Hydration Logic
        window.addEventListener('load', () => {
            hydrateSelection();
            if (partnerID) hydrateInventories();
            <?php if ($trade_view_id): ?>
                hydrateTradeDetails(<?php echo $trade_view_id; ?>);
            <?php endif; ?>
        });

        async function hydrateSelection() {
            const sidebarPending = document.getElementById('hubPendingList');
            const sidebarCollectors = document.getElementById('hubCollectorsList');
            const mainPending = document.getElementById('pendingTradesList');
            const mainCollectors = document.getElementById('activeCollectorsList');
            
            // Show Skeletons in Sidebar
            const skeletonHtml = Array(5).fill(`
                <div class="skeleton-hub-item">
                    <div class="skeleton skeleton-avatar"></div>
                    <div class="skeleton-text-wrap">
                        <div class="skeleton skeleton-text-line"></div>
                        <div class="skeleton skeleton-text-line short"></div>
                    </div>
                </div>
            `).join('');

            if (sidebarPending) sidebarPending.innerHTML = skeletonHtml;
            if (sidebarCollectors) sidebarCollectors.innerHTML = skeletonHtml;

            // Show Skeletons in Main View
            if (mainPending) mainPending.innerHTML = '<div class="skeleton" style="height: 120px; border-radius: 15px;"></div>';
            if (mainCollectors) mainCollectors.innerHTML = Array(6).fill('<div class="skeleton" style="height: 90px; border-radius: 20px;"></div>').join('');

            const fd = new FormData();
            fd.append('action', 'get_selection_data');

            try {
                const data = await secureFetch('api/trade.php', { method: 'POST', body: fd });
                if (data.success) {
                    // Render Pending Trades
                    if (data.pending.length > 0) {
                        const sidebarPendingHtml = data.pending.map(t => `
                            <a href="?trade_id=${t.id}" class="hub-item ${window.location.search.includes('trade_id='+t.id) ? 'active' : ''}">
                                <img src="${t.other_avatar}" class="hub-avatar" style="border-color:${t.is_outgoing ? 'var(--glass-border)' : 'var(--accent-primary)'}">
                                <div class="hub-info">
                                    <div class="hub-name">${t.other_username}</div>
                                    <div class="hub-meta">${t.is_outgoing ? 'Pending Reponse' : 'ACTION REQUIRED'}</div>
                                </div>
                            </a>`).join('');

                        if (sidebarPending) {
                            const title = document.getElementById('hubPendingTitle');
                            if (title) title.innerHTML = `Active Requests <span style="background:var(--accent-primary); color:white; padding:2px 6px; border-radius:4px; margin-left:5px;">${data.pending.length}</span>`;
                            sidebarPending.innerHTML = sidebarPendingHtml;
                        }

                        if (mainPending) {
                            document.getElementById('pendingTradesWrapper').style.display = 'block';
                            mainPending.innerHTML = data.pending.map(t => `
                                <div class="stat-card" style="display: flex; align-items: center; gap: 20px; border: 1px solid ${t.is_outgoing ? 'rgba(255,255,255,0.05)' : 'var(--accent-primary)'};">
                                    <img src="${t.other_avatar}" class="owner-avatar" style="width: 40px; height: 40px;">
                                    <div style="flex-grow: 1; text-align: left;">
                                        <div style="font-weight: 700; color: #fff;">${t.other_username}</div>
                                        <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase;">${t.is_outgoing ? 'Pending Response' : 'Waiting for you'}</div>
                                    </div>
                                    <a href="?trade_id=${t.id}" class="claim-btn" style="width: auto; height: 35px; border-radius: 8px; font-size: 0.7rem; padding: 0 20px; text-decoration: none; display: flex; align-items: center;">View Deal</a>
                                </div>`).join('');
                        }
                    } else {
                        if (sidebarPending) sidebarPending.innerHTML = '<div style="padding:15px; font-size:0.7rem; color:var(--text-muted);">No active trades.</div>';
                        if (mainPending) document.getElementById('pendingTradesWrapper').style.display = 'none';
                    }

                    // Render Active Collectors (Sidebar)
                    const sidebarCollectorsHtml = data.others.map(o => `
                        <a href="?partner=${o.user_discord_id}" class="hub-item ${window.location.search.includes('partner='+o.user_discord_id) ? 'active' : ''}" data-category="${o.category}">
                            <img src="${o.avatar}" class="hub-avatar">
                            <div class="hub-info">
                                <div class="hub-name">${o.username}</div>
                                <div class="hub-meta">${o.category || o.card_count + ' Cards'}</div>
                            </div>
                        </a>`).join('');
                    
                    if (sidebarCollectors) sidebarCollectors.innerHTML = sidebarCollectorsHtml;

                    // Render Active Collectors (Main Dashboard)
                    if (mainCollectors) {
                        mainCollectors.innerHTML = data.others.map(o => `
                            <a href="?partner=${o.user_discord_id}" class="stat-card" data-category="${o.category}" style="text-decoration: none; display: flex; align-items: center; gap: 20px; text-align: left; position: relative; overflow: hidden;">
                                <img src="${o.avatar}" class="owner-avatar" style="width: 50px; height: 50px; border: 2px solid var(--glass-border);">
                                <div style="flex-grow: 1;">
                                    <div class="category-tag">${o.category || 'COLLECTOR'}</div>
                                    <div style="font-weight: 700; font-size: 1.1rem; color: #fff; margin-bottom: 2px;">${o.username}</div>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); font-weight: 800; letter-spacing: 1.5px;">${o.card_count} CARDS IN VAULT</div>
                                </div>
                            </a>`).join('');
                    }
                }
            } catch (e) { console.error("Selection hydration failed", e); }
        }

        async function hydrateInventories() {
            const myGrid = document.getElementById('myInventoryGrid');
            const partnerGrid = document.getElementById('partnerInventoryGrid');

            // Show Skeletons
            if (myGrid) myGrid.innerHTML = Array(12).fill('<div class="skeleton skeleton-card"></div>').join('');
            if (partnerGrid) partnerGrid.innerHTML = Array(12).fill('<div class="skeleton skeleton-card"></div>').join('');

            const fd = new FormData();
            fd.append('action', 'get_inventories');
            fd.append('partner_id', partnerID);

            try {
                const d = await secureFetch('api/trade.php', { method: 'POST', body: fd });
                if (d.success) {
                    // Update Meta
                    // Update Meta - Merge SNs instead of overwriting to handle cards owned by both users
                    // Update Meta - Merge SNs instead of overwriting to handle cards owned by both users
                    // We also populate side-specific maps for correct SN display
                    [...d.my_cards, ...d.partner_cards].forEach(c => {
                        if (!cardMeta[c.card_id]) {
                            cardMeta[c.card_id] = JSON.parse(JSON.stringify(c)); // Deep copy
                        } else {
                            // Merge SNs list if they don't exist (legacy support for offer grid)
                            c.sns.forEach(snObj => {
                                const iid = typeof snObj === 'object' ? snObj.id : snObj;
                                const exists = cardMeta[c.card_id].sns.some(s => (typeof s === 'object' ? s.id : s) == iid);
                                if (!exists) cardMeta[c.card_id].sns.push(snObj);
                            });
                        }
                    });

                    // Populate specific inventory maps
                    d.my_cards.forEach(c => myInventoryMap[c.card_id] = c);
                    d.partner_cards.forEach(c => theirInventoryMap[c.card_id] = c);
                    
                    renderInventory('mine', d.my_cards, 'myInventoryGrid');
                    renderInventory('theirs', d.partner_cards, 'partnerInventoryGrid');
                }
            } catch (e) { console.error("Hydration failed", e); }
        }

        function renderInventory(side, cards, gridId) {
            const grid = document.getElementById(gridId);
            if (!grid) return;
            grid.innerHTML = '';
            
            if (cards.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 100px 20px; color: var(--text-muted);">No cards available.</div>';
                return;
            }

            cards.forEach(c => {
                const isTradeable = parseInt(c.is_trade) === 1;
                const div = document.createElement('div');
                div.className = `card-tile ${isTradeable ? '' : 'untradeable'}`;
                div.dataset.side = side;
                div.dataset.id = c.card_id;
                
                if (isTradeable) {
                    div.onclick = () => selectCard(side, c.card_id);
                }

                div.onmouseenter = () => {
                    if (hoverDebounce) clearTimeout(hoverDebounce);
                    hoverDebounce = setTimeout(() => {
                        // Only update on hover if we don't have a sticky card logic...
                        // If there IS a sticky card on this side, do NOTHING.
                        if (stickyCard.side === side && stickyCard.id !== null) {
                            return;
                        }
                        updateVaultIntel(side, c);
                    }, 60); // 60ms "dwell" delay for smoothing
                };
                
                div.onmouseleave = () => {
                    if (hoverDebounce) clearTimeout(hoverDebounce);
                    hoverDebounce = setTimeout(() => {
                        if (stickyCard.side === side && stickyCard.id !== null) {
                            const map = side === 'mine' ? myInventoryMap : theirInventoryMap;
                            updateVaultIntel(side, map[stickyCard.id]);
                        } else {
                            clearVaultIntel(side);
                        }
                    }, 30);
                };
                
                div.innerHTML = `
                    <img src="${imagesPath}${c.filename}" loading="lazy">
                    ${isTradeable ? '' : '<div class="lock-overlay">UNTRADEABLE</div>'}
                    <div class="name">${c.name.replace(/_/g, ' ')}</div>
                    <div class="qty">x${c.count} Vaulted</div>
                `;
                grid.appendChild(div);
            });
        }

        function removeFromTrade(side, id, instanceId) {
            const offer = side === 'mine' ? myOffer : theirOffer;
            const idx = offer[id].indexOf(instanceId);
            if (idx > -1) offer[id].splice(idx, 1);
            if (offer[id].length === 0) delete offer[id];
            renderStage();
            
            // If the card being removed is currently being hovered/previewed in the panel, refresh it
            // If the card being removed is currently being hovered/previewed in the panel, refresh it
            if (cardMeta[id]) {
                const checkMine = document.getElementById('mineIntelContent').innerHTML;
                if (checkMine.includes(cardMeta[id].name.replace(/_/g, ' ')) && myInventoryMap[id]) updateVaultIntel('mine', myInventoryMap[id]);
                
                const checkTheirs = document.getElementById('theirsIntelContent').innerHTML;
                if (checkTheirs.includes(cardMeta[id].name.replace(/_/g, ' ')) && theirInventoryMap[id]) updateVaultIntel('theirs', theirInventoryMap[id]);
            }
        }

        function renderStage() {
            updateSubGrid('mine', myOffer, 'myOfferGrid', 'myOfferCount');
            updateSubGrid('theirs', theirOffer, 'theirOfferGrid', 'theirOfferCount');
        }

        function updateSubGrid(side, offer, gridId, countId) {
            const grid = document.getElementById(gridId);
            const countEl = document.getElementById(countId);
            if (!grid || !countEl) return;
            grid.innerHTML = '';
            
            let total = 0;
            for (const [id, ids] of Object.entries(offer)) {
                const meta = cardMeta[id];
                total += ids.length;

                ids.forEach(iid => {
                    const inst = meta.sns.find(s => (typeof s === 'object' ? s.id : s) == iid);
                    const sn = typeof inst === 'object' ? inst.sn : inst;
                    
                    const div = document.createElement('div');
                    div.className = 'staged-card';
                    div.onclick = () => removeFromTrade(side, id, iid);
                    div.innerHTML = `
                        <img src="${imagesPath}${meta.filename}" loading="lazy">
                        <div class="count-badge" style="font-size:0.55rem; padding: 2px 4px;">#${sn || '?'}</div>
                    `;
                    div.title = meta.name.replace(/_/g, ' ') + ` (SN #${sn || '?'})`;
                    grid.appendChild(div);
                });
            }
            countEl.innerText = `${total} cards`;
        }

        function proposeTrade() {
            if (Object.keys(myOffer).length === 0 && Object.keys(theirOffer).length === 0) {
                Swal.fire({
                    title: 'Empty Proposal',
                    text: 'You cannot broadcast an empty trade request. Please select at least one card.',
                    icon: 'warning'
                });
                return;
            }

            const fd = new FormData();
            fd.append('action', 'propose_trade');
            fd.append('partner_id', partnerID);
            fd.append('your_offer', JSON.stringify(myOffer));
            fd.append('their_offer', JSON.stringify(theirOffer));
            fd.append('turnstile_token', turnstile.getResponse());

            secureFetch('api/trade.php', { method: 'POST', body: fd })
                .then(d => {
                    if (d.success) {
                        Swal.fire({
                            title: 'Proposal Sent',
                            text: 'Your trade proposal has been beamed to the recipient.',
                            icon: 'success'
                        }).then(() => {
                            window.location.href = 'trade.php';
                        });
                    }
                })
                .catch(e => {
                    Swal.fire({
                        title: 'Proposal Error',
                        text: e.message,
                        icon: 'error'
                    });
                    if (typeof turnstile !== 'undefined') turnstile.reset();
                });
        }

        async function hydrateTradeDetails(tradeId) {
            const fd = new FormData();
            fd.append('action', 'get_trade_details');
            fd.append('trade_id', tradeId);

            try {
                const data = await secureFetch('api/trade.php', { method: 'POST', body: fd });
                if (data.success) {
                    const stage = document.getElementById('tradeSummaryStage');
                    const isSender = data.trade.sender_id === myID;

                    const renderSummarySide = (sideType, user, items, accent) => {
                        const isLeft = sideType === 'SENDER';
                        // Perspective: If I am sender, left is what I SEND. 
                        // If I am receiver, left is what I RECEIVE from them.
                        const perspectiveLabel = (isSender === isLeft) ? 'SENDING' : 'RECEIVING';
                        const ownerLabel = (user.id === myID) ? 'YOUR' : 'THEIR';
                        
                        return `
                            <div class="summary-side" data-label="${perspectiveLabel}" style="${!isLeft ? 'background: rgba(0, 229, 255, 0.02);' : ''}">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; position: relative; z-index: 2;">
                                    <img src="${user.avatar}" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid var(--accent-${accent}); box-shadow: 0 0 15px rgba(${accent === 'primary' ? '255, 0, 234' : '0, 229, 255'}, 0.3);">
                                    <div>
                                        <div style="font-size: 0.6rem; color: var(--text-muted); font-weight: 800; letter-spacing: 2px;">${ownerLabel} ASSETS</div>
                                        <div style="font-family: 'Outfit'; font-weight: 900; font-size: 1.1rem; color: #fff;">${user.username.toUpperCase()}</div>
                                    </div>
                                </div>
                                <div class="summary-item-grid">
                                    ${items.map(itm => `
                                        <div class="summary-card ${itm.rarity_class} ${itm.variations ? 'variant-' + itm.variations : ''}" style="border-top: 3px solid var(--${itm.rarity_class});">
                                            <img src="${imagesPath}${itm.filename}" class="${itm.variations ? 'variant-' + itm.variations : ''}" loading="lazy">
                                            <div class="sn-label">${itm.sn_display ? "#" + itm.sn_display : "x" + itm.count}</div>
                                            <div class="name" style="color: var(--${itm.rarity_class}); font-weight: 800;">${itm.rarity_name.toUpperCase()}</div>
                                            <div class="name" style="font-size: 0.65rem; opacity: 0.8;">${itm.name.replace(/_/g, ' ')}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    };

                    const senderItems = data.items.filter(i => i.user_id === data.trade.sender_id);
                    const receiverItems = data.items.filter(i => i.user_id === data.trade.receiver_id);

                    stage.innerHTML = renderSummarySide('SENDER', data.sender, senderItems, 'primary') + 
                                     renderSummarySide('RECEIVER', data.receiver, receiverItems, 'secondary');
                }
            } catch (e) { console.error("Trade hydration failed", e); }
        }

        function respondTrade(tid, status) {
            const fd = new FormData();
            fd.append('action', 'respond_trade');
            fd.append('trade_id', tid);
            fd.append('status', status);

            secureFetch('api/trade.php', { method: 'POST', body: fd })
                .then(d => {
                    if (d.success) {
                        Swal.fire({
                            title: 'Status Updated',
                            text: `Trade ${status} successfully.`,
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = 'trade.php';
                        });
                    }
                })
                .catch(e => {
                    Swal.fire({
                        title: 'Response Failed',
                        text: e.message,
                        icon: 'error'
                    });
                });
        }



    </script>
    <?php include 'trade-tutorial.php'; ?>
    <?php include 'discord-id-tutorial.php'; ?>

    <?php 
    if ($my_id === '332684782888550410' || ($partner_id && $partner_id === '332684782888550410') || (isset($other_party_id) && $other_party_id == '332684782888550410')) {
        include 'reports/subject-null.php';
        include 'reports/subject-vyper.php';
        include 'reports/subject-zeke.php';
        include 'reports/genesis-archive.php';
    }
    ?>
    <?php include 'null-egg.php'; ?>
</body>
</html>
