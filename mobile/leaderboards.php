<?php
/**
 * MOBILE LEADERBOARDS
 * Simplified vertical list of top collectors.
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

require_once __DIR__ . '/../config.php';
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$env = loadEnv($ENV_PATH);

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php");
    exit;
}

$page_title = 'Leaderboards';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | Leaderboards</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <?php 
    require_once __DIR__ . '/../theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        .m-board-section {
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .m-board-header {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .m-board-title {
            font-family: 'Outfit';
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
        }

        .m-board-desc {
            font-size: 0.65rem;
            color: var(--text-muted);
            padding: 0.75rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            line-height: 1.4;
        }

        .m-leader-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .m-leader-item:last-child { border-bottom: none; }

        .m-leader-rank {
            font-family: 'Outfit';
            font-weight: 800;
            font-size: 0.75rem;
            min-width: 28px;
            color: var(--text-muted);
        }

        .rank-1 .m-leader-rank { color: #ffd700; text-shadow: 0 0 10px rgba(255, 215, 0, 0.4); }
        .rank-2 .m-leader-rank { color: #c0c0c0; text-shadow: 0 0 10px rgba(192, 192, 192, 0.3); }
        .rank-3 .m-leader-rank { color: #cd7f32; text-shadow: 0 0 10px rgba(205, 127, 50, 0.3); }

        .m-leader-item:active {
            background: rgba(255, 255, 255, 0.08);
        }

        .m-leader-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--glass-border);
        }

        .m-leader-name {
            flex-grow: 1;
            font-family: 'Outfit';
            font-weight: 700;
            font-size: 0.85rem;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .m-leader-score {
            font-family: 'Outfit';
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--accent-secondary);
        }

        .m-score-unit {
            font-size: 0.5rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-left: 3px;
        }

        .m-skeleton-item {
            height: 48px;
            margin: 0 1rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Leaderboards';
    include 'nav.php'; 
    ?>

    <main class="m-container" style="padding-top: 1rem;">
        <!-- Category Picker -->
        <div class="m-category-picker" style="overflow-x: auto; white-space: nowrap; gap: 8px;">
            <button class="m-cat-btn active" onclick="switchCategory('wealth', this)">Wealth</button>
            <button class="m-cat-btn" onclick="switchCategory('archivists', this)">Uniques</button>
            <button class="m-cat-btn" onclick="switchCategory('inscribed', this)">Notes</button>
            <button class="m-cat-btn" onclick="switchCategory('hunters', this)">Anomalies</button>
            <button class="m-cat-btn" onclick="switchCategory('traders', this)">Traders</button>
            <button class="m-cat-btn" onclick="switchCategory('draws', this)">Draws</button>
        </div>

        <div id="m-leaderboard-display">
            <!-- Podium -->
            <div class="m-podium" id="m-podium-container">
                <!-- Top 3 rendered here -->
                <div class="m-skeleton-item" style="flex:1; height:120px;"></div>
                <div class="m-skeleton-item" style="flex:1; height:160px;"></div>
                <div class="m-skeleton-item" style="flex:1; height:100px;"></div>
            </div>

            <!-- List -->
            <div class="m-board-section">
                <div id="m-leader-list">
                    <?php for($i=0; $i<7; $i++): ?><div class="m-skeleton-item"></div><?php endfor; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        let currentAction = 'get_wealthiest';
        let currentUnit = 'WEALTH';

        const categoryMap = {
            'wealth': { action: 'get_wealthiest', unit: 'WEALTH' },
            'archivists': { action: 'get_top_archivists', unit: 'UNIQUES' },
            'inscribed': { action: 'get_top_inscribed', unit: 'NOTES' },
            'hunters': { action: 'get_top_hunters', unit: 'ANOMALIES' },
            'traders': { action: 'get_top_traders', unit: 'TRADES' },
            'draws': { action: 'get_top_drawers', unit: 'DRAWS' }
        };

        window.addEventListener('load', () => {
            loadLeaderboard();
        });

        function switchCategory(cat, btn) {
            document.querySelectorAll('.m-cat-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentAction = categoryMap[cat].action;
            currentUnit = categoryMap[cat].unit;
            loadLeaderboard();
        }

        async function loadLeaderboard() {
            const listContainer = document.getElementById('m-leader-list');
            const podiumContainer = document.getElementById('m-podium-container');
            
            // Skeletons
            listContainer.innerHTML = '<?php for($i=0; $i<7; $i++): ?><div class="m-skeleton-item"></div><?php endfor; ?>';
            podiumContainer.innerHTML = `
                <div class="m-skeleton-item" style="flex:1; height:120px; border-radius:12px;"></div>
                <div class="m-skeleton-item" style="flex:1; height:160px; border-radius:12px;"></div>
                <div class="m-skeleton-item" style="flex:1; height:100px; border-radius:12px;"></div>
            `;

            try {
                const response = await fetch(`/api/leaderboard.php?action=${currentAction}`);
                const data = await response.json();
                
                if (data.success) {
                    const top3 = data.data.slice(0, 3);
                    const rest = data.data.slice(3);

                    // Render Podium
                    // Order for visual: 2nd, 1st, 3rd
                    const podiumOrder = [top3[1], top3[0], top3[2]];
                    podiumContainer.innerHTML = podiumOrder.map((p, i) => {
                        if (!p) return '<div style="flex:1"></div>';
                        const rank = p === top3[0] ? 1 : (p === top3[1] ? 2 : 3);
                        return `
                            <div class="m-podium-spot spot-${rank}">
                                <div class="m-podium-avatar-wrap">
                                    <img src="${p.avatar}" class="m-podium-avatar">
                                    <div class="m-podium-rank">${rank}</div>
                                </div>
                                <div class="m-podium-name">${p.username}</div>
                                <div class="m-podium-score">${formatMScore(p.score || p.wealth_score)} <span style="font-size:0.5rem">${currentUnit}</span></div>
                            </div>
                        `;
                    }).join('');

                    // Render List
                    listContainer.innerHTML = rest.map((p, i) => `
                        <div class="m-leader-item">
                            <div class="m-leader-rank">#${i+4}</div>
                            <img src="${p.avatar}" class="m-leader-avatar">
                            <div class="m-leader-name">${p.username}</div>
                            <div class="m-leader-score">
                                ${formatMScore(p.score || p.wealth_score)}
                                <span class="m-score-unit">${currentUnit}</span>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (e) {
                listContainer.innerHTML = `<div style="color:var(--error-color);padding:1rem;font-size:0.7rem;text-align:center;">Link Severed.</div>`;
            }
        }

        function formatMScore(score) {
            return Math.round(score).toLocaleString();
        }
    </script>
</body>
</html>
