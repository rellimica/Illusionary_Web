<?php
/**
 * LEADERBOARDS
 * Standardized with Admin Dashboard 'Top 10' Theme.
 */
$session_lifetime = 10 * 24 * 60 * 60;
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path'     => '/',
    'domain'   => '', 
    'secure'   => true, 
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_name('ILLUSIONARY_SID');
session_start();

require_once 'config.php';
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$env = loadEnv($ENV_PATH);

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: auth.php");
    exit;
}

$page_title = 'Leaderboards';
$prefix = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Leaderboards</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        .leaderboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .leaderboard-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .hero-title { font-size: 2.5rem !important; }
        }

        .board-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            backdrop-filter: blur(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: visible;
        }

        .board-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent-secondary);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 229, 255, 0.05);
        }

        .board-header {
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .board-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tooltip Logic */
        .info-trigger {
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            color: var(--text-muted);
            cursor: help;
            transition: all 0.2s;
            position: relative;
        }

        .info-trigger:hover {
            background: var(--accent-secondary);
            color: #fff;
            border-color: var(--accent-secondary);
        }

        .tooltip-box {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translate(-50%, -100%);
            width: 220px;
            background: #0c0a15;
            border: 1px solid var(--glass-border);
            padding: 14px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 400;
            color: var(--text-muted);
            text-transform: none;
            letter-spacing: 0;
            line-height: 1.5;
            pointer-events: none;
            opacity: 0;
            transition: all 0.2s;
            box-shadow: 0 15px 35px rgba(0,0,0,0.8);
            z-index: 100;
            font-family: 'Inter', sans-serif;
        }

        .info-trigger:hover .tooltip-box {
            opacity: 1;
            top: -15px;
        }

        /* Leader Item - Admin Sync */
        .leader-item {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            margin-bottom: 0.8rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .leader-item:hover {
            transform: translateX(10px);
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--accent-secondary);
        }

        .leader-rank {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 0.85rem;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.4);
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.05);
            flex-shrink: 0;
        }

        /* Rank Medals - Admin Theme */
        .rank-1 .leader-rank { 
            background: linear-gradient(135deg, #ffd700, #b8860b); 
            color: #000; 
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.4); 
            border: none;
        }
        .rank-2 .leader-rank { 
            background: linear-gradient(135deg, #c0c0c0, #708090); 
            color: #000; 
            box-shadow: 0 0 20px rgba(192, 192, 192, 0.3); 
            border: none;
        }
        .rank-3 .leader-rank { 
            background: linear-gradient(135deg, #cd7f32, #8b4513); 
            color: #000; 
            box-shadow: 0 0 20px rgba(205, 127, 50, 0.3); 
            border: none;
        }

        /* Reset coloring for ranks 4+ (prevents 'nth-child' styles from global CSS) */
        .leader-item .leader-rank {
            background: rgba(0, 0, 0, 0.4) !important;
            color: var(--text-muted) !important;
            box-shadow: none !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        .rank-1 .leader-rank, .rank-2 .leader-rank, .rank-3 .leader-rank {
            /* Keep Top 3 styles protected from the reset */
        }
        
        /* Re-apply Top 3 specifics specifically if needed, 
           but since they use 'rank-X' classes on the container 
           and we reset '.leader-item .leader-rank', we need to be careful.
           Actually, podium items don't use .leader-item class! */
        
        .leader-item.rank-1 .leader-rank { background: linear-gradient(135deg, #ffd700, #b8860b) !important; color: #000 !important; }
        .leader-item.rank-2 .leader-rank { background: linear-gradient(135deg, #c0c0c0, #708090) !important; color: #000 !important; }
        .leader-item.rank-3 .leader-rank { background: linear-gradient(135deg, #cd7f32, #8b4513) !important; color: #000 !important; }

        .leader-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .leader-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .leader-tag {
            font-size: 0.6rem;
            color: var(--text-muted);
            opacity: 0.8;
        }

        .leader-score {
            font-size: 1.1rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            color: var(--accent-secondary);
            text-shadow: 0 0 10px rgba(0, 229, 255, 0.1);
            text-align: right;
        }

        .score-unit {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-left: 4px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .skeleton-loader {
            height: 58px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            margin-bottom: 0.8rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.3; }
        }

        /* Podium Styles - Desktop Card */
        .podium-container {
            display: grid;
            grid-template-columns: 1fr 1.1fr 1fr;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 2rem;
            padding: 1rem 0.5rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            background: linear-gradient(180deg, rgba(255,255,255,0.02) 0%, transparent 100%);
            border-radius: 12px 12px 0 0;
        }

        .podium-spot {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            animation: entry-up 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        .spot-1 { animation-delay: 0.2s; }
        .spot-2 { animation-delay: 0.1s; }
        .spot-3 { animation-delay: 0.3s; }

        @keyframes entry-up {
            from { opacity: 0; transform: translateY(15px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .podium-avatar-wrap {
            position: relative;
            margin-bottom: 10px;
        }

        .podium-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid var(--glass-border);
            object-fit: cover;
            background: #000;
        }

        .spot-1 .podium-avatar { width: 72px; height: 72px; border-color: #ffd700; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2); border-width: 3px; }
        .spot-2 .podium-avatar { width: 54px; height: 54px; border-color: #c0c0c0; }
        .spot-3 .podium-avatar { border-color: #cd7f32; }

        .podium-rank-badge {
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            background: #0c0a15;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 900;
            font-family: 'Outfit';
            border: 2px solid currentColor;
            z-index: 10;
        }

        .spot-1 .podium-rank-badge { color: #ffd700; width: 24px; height: 24px; font-size: 0.75rem; }
        .spot-2 .podium-rank-badge { color: #c0c0c0; }
        .spot-3 .podium-rank-badge { color: #cd7f32; }

        .podium-name {
            font-family: 'Outfit';
            font-weight: 800;
            font-size: 0.75rem;
            color: #fff;
            max-width: 90px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .podium-score {
            font-size: 0.7rem;
            color: var(--accent-secondary);
            font-weight: 800;
        }

        .hero-section {
            text-align: center;
            padding: 5rem 0 3rem;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -2px;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
            text-transform: uppercase;
            letter-spacing: 5px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    <?php include 'nav.php'; ?>

    <main class="container">
        <section class="hero-section">
            <h1 class="hero-title gradient-text">LEADERBOARDS</h1>
            <p class="hero-subtitle">Chronicles of the persistent</p>
        </section>

        <div class="leaderboard-grid">
            <!-- 1. VAULT WEALTH -->
            <div class="board-card" id="board-wealth">
                <div class="board-header">
                    <h2 class="board-title">
                        Top Vault Wealth
                        <span class="info-trigger">?
                            <span class="tooltip-box">Calculated by summing the value of all non-hidden cards, weighted by rarity and special variations.</span>
                        </span>
                    </h2>
                </div>
                <div class="board-content">
                    <div id="podium-wealth"></div>
                    <div id="list-wealth">
                        <?php for($i=0; $i<7; $i++): ?><div class="skeleton-loader"></div><?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- 2. MASTER ARCHIVISTS -->
            <div class="board-card" id="board-archivists">
                <div class="board-header">
                    <h2 class="board-title">
                        Master Archivists
                        <span class="info-trigger">?
                            <span class="tooltip-box">Ranked by the total number of unique cards discovered across the entire collection.</span>
                        </span>
                    </h2>
                </div>
                <div class="board-content">
                    <div id="podium-archivists"></div>
                    <div id="list-archivists">
                        <?php for($i=0; $i<7; $i++): ?><div class="skeleton-loader"></div><?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- 3. THE INSCRIBED -->
            <div class="board-card" id="board-inscribed">
                <div class="board-header">
                    <h2 class="board-title">
                        The Inscribed
                        <span class="info-trigger">?
                            <span class="tooltip-box">Users with the highest count of personalized cards containing custom notes or inscriptions.</span>
                        </span>
                    </h2>
                </div>
                <div class="board-content">
                    <div id="podium-inscribed"></div>
                    <div id="list-inscribed">
                        <?php for($i=0; $i<7; $i++): ?><div class="skeleton-loader"></div><?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- 4. ANOMALY HUNTERS -->
            <div class="board-card" id="board-hunters">
                <div class="board-header">
                    <h2 class="board-title">
                        Anomaly Hunters
                        <span class="info-trigger">?
                            <span class="tooltip-box">Recognition for collecting the most card variations (any card that isn't Standard or Static).</span>
                        </span>
                    </h2>
                </div>
                <div class="board-content">
                    <div id="podium-hunters"></div>
                    <div id="list-hunters">
                        <?php for($i=0; $i<7; $i++): ?><div class="skeleton-loader"></div><?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- 5. TOP TRADERS -->
            <div class="board-card" id="board-traders">
                <div class="board-header">
                    <h2 class="board-title">
                        Top Traders
                        <span class="info-trigger">?
                            <span class="tooltip-box">Ranked by the total number of successfully completed transactions within the community.</span>
                        </span>
                    </h2>
                </div>
                <div class="board-content">
                    <div id="podium-traders"></div>
                    <div id="list-traders">
                        <?php for($i=0; $i<7; $i++): ?><div class="skeleton-loader"></div><?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- 6. PROLIFIC DRAWERS -->
            <div class="board-card" id="board-drawers">
                <div class="board-header">
                    <h2 class="board-title">
                        Prolific Drawers
                        <span class="info-trigger">?
                            <span class="tooltip-box">Users who have performed the most card draws from the system.</span>
                        </span>
                    </h2>
                </div>
                <div class="board-content">
                    <div id="podium-drawers"></div>
                    <div id="list-drawers">
                        <?php for($i=0; $i<7; $i++): ?><div class="skeleton-loader"></div><?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        window.addEventListener('load', () => {
            fetchLeaderboard('get_wealthiest', 'list-wealth', 'WEALTH');
            fetchLeaderboard('get_top_archivists', 'list-archivists', 'UNIQUES');
            fetchLeaderboard('get_top_inscribed', 'list-inscribed', 'NOTES');
            fetchLeaderboard('get_top_hunters', 'list-hunters', 'ANOMALIES');
            fetchLeaderboard('get_top_traders', 'list-traders', 'TRADES');
            fetchLeaderboard('get_top_drawers', 'list-drawers', 'DRAWS');
        });

        async function fetchLeaderboard(action, listId, unit) {
            const listContainer = document.getElementById(listId);
            const podiumId = listId.replace('list-', 'podium-');
            const podiumContainer = document.getElementById(podiumId);

            try {
                const response = await fetch(`api/leaderboard.php?action=${action}`);
                const data = await response.json();
                
                if (data.success) {
                    const top3 = data.data.slice(0, 3);
                    const rest = data.data.slice(3);

                    // Render Podium (Order: 2, 1, 3)
                    const podiumOrder = [top3[1], top3[0], top3[2]];
                    podiumContainer.innerHTML = `
                        <div class="podium-container">
                            ${podiumOrder.map((p, i) => {
                                if (!p) return '<div></div>';
                                const rank = p === top3[0] ? 1 : (p === top3[1] ? 2 : 3);
                                return `
                                    <div class="podium-spot spot-${rank}">
                                        <div class="podium-avatar-wrap">
                                            <img src="${p.avatar}" class="podium-avatar">
                                            <div class="podium-rank-badge">${rank}</div>
                                        </div>
                                        <div class="podium-name">${p.username}</div>
                                        <div class="podium-score">${formatScore(p.score || p.wealth_score)} <span style="font-size:0.5rem">${unit}</span></div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    `;

                    // Render List
                    listContainer.innerHTML = rest.map((p, i) => `
                        <div class="leader-item rank-${i+4}">
                            <div class="leader-rank">${i+4}</div>
                            <img src="${p.avatar}" class="tiny-avatar">
                            <div class="leader-info">
                                <div class="leader-name">${p.username}</div>
                                <div class="leader-tag">${p.user_discord_id}</div>
                            </div>
                            <div class="leader-score">
                                ${formatScore(p.score || p.wealth_score)}
                                <span class="score-unit">${unit}</span>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (e) {
                container.innerHTML = `<div style="color: #ff4e4e; padding: 1rem; text-align: center;">Connection Terminated.</div>`;
            }
        }

        function formatScore(score) {
            return Math.round(score).toLocaleString();
        }
    </script>

    <?php include 'null-egg.php'; ?>
</body>
</html>
