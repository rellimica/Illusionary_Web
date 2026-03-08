<?php
/**
 * MOBILE COLLECTION VIEW
 * 2-column card grid, full-screen detail modal.
 */
$session_lifetime = 10 * 24 * 60 * 60;
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path'     => '/',
    'domain'   => '', 
    'secure'   => true, 
    'httponly'  => true,
    'samesite' => 'Lax'
]);

session_name('ILLUSIONARY_SID');
session_start();

require_once __DIR__ . '/../config.php';
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$IMAGES_PATH = '/images/images/';

$env = loadEnv($ENV_PATH);

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php");
    exit;
}

// Database connection for pagination meta
try {
    $host = $env['MYSQL_HOST'] ?? 'localhost';
    $db   = $env['MYSQL_DATABASE'] ?? '';
    $user = $env['MYSQL_USER'] ?? '';
    $pass = $env['MYSQL_PASSWORD'] ?? '';
    $port = $env['MYSQL_PORT'] ?? '3306';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4;port=$port";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_STRINGIFY_FETCHES => true
    ]);
} catch (\PDOException $e) {
    header("Location: /db_error.php");
    exit;
}



$my_id = $_SESSION['user_data']['id'];
$is_admin = isAdmin($my_id);

// Target User Logic
$target_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $my_id;
$viewing_self = ($target_user_id === $my_id);
$target_user_data = null;

if (!$viewing_self) {
    $env_bot = loadEnv($BOT_DIR . '/.env');
    $target_user_data = getDiscordUser($target_user_id, $env_bot['DISCORD_TOKEN'] ?? '');
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;

// Determine visibility mirroring api/collection.php logic
$visibility_cond = $viewing_self ? "(c.is_hidden = 0 OR uc.card_id IS NOT NULL)" : "c.is_hidden = 0";

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM cards c LEFT JOIN user_cards uc ON c.id = uc.card_id AND uc.user_discord_id = ? WHERE $visibility_cond");
$total_stmt->execute([$target_user_id]);
$filtered_possible = $total_stmt->fetchColumn();
$total_pages = ceil($filtered_possible / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | <?php echo $viewing_self ? 'Collection View' : ($target_user_data['username'] . "'s Collection"); ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <link rel="stylesheet" href="/variations.css">
    <link rel="preload" href="/illusionary.png" as="image">
    <?php 
    require_once __DIR__ . '/../theme-config.php';
    injectTheme($THEME);
    ?>
</head>
<body>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Collection View';
    include 'nav.php'; 
    ?>

    <main class="m-container">
        <!-- Stats Grid -->
        <section class="m-stats-grid">
            <div style="grid-column: span 4; display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 0 5px;">
                <div style="font-family: 'Outfit'; font-weight: 800; font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 6px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: <?php echo $viewing_self ? 'var(--accent-primary)' : 'var(--accent-secondary)'; ?>;"></span>
                    Viewing: <span style="color: #fff;"><?php echo $viewing_self ? 'You' : $target_user_data['username']; ?></span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <?php if (!$viewing_self): ?>
                        <a href="index.php" style="padding: 4px 10px; font-size: 0.6rem; text-decoration: none; background: rgba(0, 229, 255, 0.05); border: 1px solid rgba(0, 229, 255, 0.2); color: var(--accent-secondary); border-radius: 6px; font-weight: 700;">Home</a>
                    <?php endif; ?>
                    <button onclick="switchCollector()" style="padding: 4px 10px; font-size: 0.6rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); color: var(--text-muted); border-radius: 6px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                        <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg>
                        Switch
                    </button>
                </div>
            </div>
            <div class="m-stat-card">
                <div id="stat-total" class="m-stat-value skeleton">&nbsp;</div>
                <div class="m-stat-label">Total Cards</div>
            </div>
            <div class="m-stat-card">
                <div id="stat-unique" class="m-stat-value skeleton">&nbsp;</div>
                <div class="m-stat-label">Unique</div>
            </div>
            <div class="m-stat-card">
                <div id="stat-completion" class="m-stat-value skeleton">&nbsp;</div>
                <div class="m-stat-label">Completion</div>
            </div>
            <div class="m-stat-card">
                <div id="stat-possible" class="m-stat-value skeleton">&nbsp;</div>
                <div class="m-stat-label">Possible</div>
            </div>
        </section>

        <!-- Card Grid (2-column) -->
        <section id="userCardGrid" class="m-card-grid">
            <?php for ($i = 0; $i < 10; $i++): ?>
                <div class="m-card-item empty skeleton"></div>
            <?php endfor; ?>
        </section>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="m-pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?<?php echo isset($_GET['user_id']) ? 'user_id=' . htmlspecialchars($_GET['user_id']) . '&' : ''; ?>page=<?php echo $i; ?>" class="m-page-link <?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Card Detail Modal (Full-screen) -->
    <div class="m-detail-modal" id="detailModal">
        <div class="m-detail-inner">
            <button class="m-detail-close" onclick="closeDetail()">&times;</button>
            <div class="m-detail-visual" id="detailVisualWrap" style="position: relative;">
                <img src="" id="detailImg" class="m-detail-img" alt="">
            </div>
            <div id="detailRarity" class="m-detail-rarity">RARITY</div>
            <div id="detailName" class="m-detail-name">Card Name</div>
            <div class="m-detail-stats">
                <div class="m-detail-stat-box">
                    <span class="m-detail-stat-label">Copies</span>
                    <div class="m-detail-stat-val" id="detailCount">0</div>
                </div>
                <div class="m-detail-stat-box">
                    <span class="m-detail-stat-label">Acquired</span>
                    <div class="m-detail-stat-val" id="detailDate" style="font-size: 0.9rem;">-</div>
                </div>
            </div>
            <div id="detailSNRow" style="display: none; margin-top: 1.5rem;">
                <div style="font-family: 'Outfit'; font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Registry</div>
                <div id="detailSNList" class="m-detail-sn-list"></div>
            </div>
            
            <div id="instanceMessage" class="m-instance-message" style="display: none;">
                <div style="font-family: 'Outfit'; font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Registry Note</div>
                <div id="messageText" style="font-style: italic; color: #fff; font-size: 0.85rem; line-height: 1.4; padding: 10px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    <script>
        const IMAGES_PATH = '<?php echo $IMAGES_PATH; ?>';
        const currentPage = <?php echo $page; ?>;
        const targetUserId = '<?php echo $target_user_id; ?>';
        const isViewingSelf = <?php echo $viewing_self ? 'true' : 'false'; ?>;
        let currentModalSNS = [];

        window.addEventListener('load', hydrateCollection);

        async function hydrateCollection() {
            try {
                const data = await secureFetch(`/api/collection.php?action=hydrate_collection&page=${currentPage}&user_id=${targetUserId}`);
                
                // Stats
                const stats = [
                    { id: 'stat-total', val: data.stats.total },
                    { id: 'stat-unique', val: data.stats.unique },
                    { id: 'stat-completion', val: data.stats.completion + '%' },
                    { id: 'stat-possible', val: data.stats.possible }
                ];
                
                stats.forEach(s => {
                    const el = document.getElementById(s.id);
                    if (el) {
                        el.innerText = typeof s.val === 'number' ? s.val.toLocaleString() : s.val;
                        el.classList.remove('skeleton');
                    }
                });

                // Mana (Only update if viewing self)
                if (isViewingSelf) {
                    const manaVal = document.getElementById('nav-mana-value');
                    if (manaVal) manaVal.innerText = data.user.tokens.toLocaleString();
                    
                    const claimBtn = document.getElementById('mana-claim-container');
                    const claimedBadge = document.getElementById('mana-claimed-badge');
                    if (claimBtn && claimedBadge) {
                        claimBtn.style.display = data.user.can_claim ? 'block' : 'none';
                        claimedBadge.style.display = data.user.can_claim ? 'none' : 'block';
                    }
                }

                // Card Grid
                const grid = document.getElementById('userCardGrid');
                grid.innerHTML = '';
                
                for (let i = 0; i < 10; i++) {
                    const card = data.cards[i] || null;
                    const div = document.createElement('div');
                    
                    if (card) {
                        const isOwned = card.count > 0;
                        if (isOwned) {
                            const cleanName = card.name.replace(/_/g, ' ');
                            const rawTs = card.timestamp ? Math.floor(new Date(card.timestamp).getTime() / 1000) : 0;
                            div.className = `m-card-item owned rarity-${card.rarity_name.toLowerCase().replace(/[^a-z0-9]/g, '-')}`;
                            div.onclick = () => openDetail(cleanName, card.rarity_name, `${IMAGES_PATH}${card.filename}`, card.count, rawTs, card.sns);
                            
                            const img = document.createElement('img');
                            img.src = IMAGES_PATH + card.filename;
                            img.alt = card.name;
                            img.loading = "lazy";

                            div.appendChild(img);

                            // Physical Stamp Integration (Mobile Grid)
                            if (card.sns && card.sns.length > 0) {
                                const snVal = card.sns[0].sn;
                                div.innerHTML += `<div class="m-card-stamp">#${snVal}</div>`;
                            }

                            if (card.count > 1) {
                                div.innerHTML += `<div class="m-card-count">x${card.count}</div>`;
                            }
                        } else {
                            div.className = "m-card-item unowned";
                            div.onclick = openLockedDetail;
                            div.innerHTML = `<img src="${IMAGES_PATH}back.png" alt="Locked Card" style="opacity:0.3; filter:grayscale(1);" loading="lazy">`;
                        }
                    } else {
                        div.className = "m-card-item empty";
                    }
                    grid.appendChild(div);
                }

            } catch (e) { 
                Swal.fire({
                    title: 'Sync Interrupted',
                    text: 'Unable to establish a link with your collection database.',
                    icon: 'error'
                });
                console.error("Hydration failed", e); 
            }
        }

        function openDetail(name, rarity, img, count, ts, sns = []) {
            currentModalSNS = sns;
            let dateStr = "Legendary Origin";
            if (ts > 0) {
                const date = new Date(ts * 1000);
                dateStr = date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
            }
            document.getElementById('detailName').innerText = name;
            document.getElementById('detailRarity').innerText = rarity;
            document.getElementById('detailRarity').style.color = getRarityColor(rarity);
            const detailImg = document.getElementById('detailImg');
            const visualWrap = document.getElementById('detailVisualWrap');
            detailImg.src = img;

            document.getElementById('detailCount').innerText = count;
            document.getElementById('detailDate').innerText = dateStr;

            const snRow = document.getElementById('detailSNRow');
            const snList = document.getElementById('detailSNList');
            const msgRow = document.getElementById('instanceMessage');
            
            if (sns && sns.length > 0) {
                snRow.style.display = 'block';
                
                // Set initial instance view (first copy)
                updateInstanceView(0);
                
                const limit = 20; 
                const shown = sns.slice(0, limit);
                
                snList.innerHTML = shown.map((inst, idx) => {
                    const sn = typeof inst === 'object' ? inst.sn : inst;
                    const v = inst.variant;
                    const vClass = v ? `variant-${v}` : '';
                    return `<span class="m-detail-sn-pill ${vClass}" onclick="updateInstanceView(${idx})">#${sn}</span>`;
                }).join('');
            } else {
                snRow.style.display = 'none';
                if (msgRow) msgRow.style.display = 'none';
            }

            document.getElementById('detailModal').style.display = 'flex';
        }

        function applyModalVariant(variant) {
            const detailImg = document.getElementById('detailImg');
            if (!detailImg) return;

            Array.from(detailImg.classList).forEach(c => {
                if (c.startsWith('variant-')) detailImg.classList.remove(c);
            });
            if (variant) detailImg.classList.add(`variant-${variant}`);
        }

        function updateInstanceView(index) {
            const inst = currentModalSNS[index];
            if (!inst) return;

            const detailImg = document.getElementById('detailImg');
            const visualWrap = document.getElementById('detailVisualWrap');
            const msgRow = document.getElementById('instanceMessage');
            const msgText = document.getElementById('messageText');

            // Apply variant
            const v = inst.variant;
            applyModalVariant(v);

            // Update message
            if (inst.message) {
                msgText.innerText = inst.message;
                msgRow.style.display = 'block';
            } else {
                msgRow.style.display = 'none';
            }
            
            // Highlight active pill
            const pills = document.querySelectorAll('.m-detail-sn-pill');
            pills.forEach((p, i) => {
                if (i === index) p.classList.add('active');
                else p.classList.remove('active');
            });
        }

        function openLockedDetail() {
            document.getElementById('detailName').innerText = "Undiscovered Entity";
            document.getElementById('detailRarity').innerText = "CALCULATING...";
            document.getElementById('detailRarity').style.color = "#666";
            document.getElementById('detailImg').src = `${IMAGES_PATH}back.png`;
            document.getElementById('detailCount').innerText = "0";
            document.getElementById('detailDate').innerText = "N/A";
            document.getElementById('detailSNRow').style.display = 'none';
            document.getElementById('detailSNList').innerHTML = '';
            
            // Explicitly clear variants applied from previously viewed cards
            applyModalVariant(null);
            
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeDetail() { document.getElementById('detailModal').style.display = 'none'; }
        
        async function switchCollector() {
            const { value: userId } = await Swal.fire({
                title: 'Target Search',
                text: 'Enter a Discord User ID:',
                input: 'text',
                inputPlaceholder: 'Discord ID',
                showCancelButton: true,
                confirmButtonText: 'Search',
                background: 'rgba(12, 10, 21, 0.95)',
                color: '#fff'
            });

            if (userId) {
                if (/^\d+$/.test(userId)) {
                    window.location.href = `index.php?user_id=${userId}`;
                } else {
                    Swal.fire('Error', 'Invalid Discord ID', 'error');
                }
            }
        }
        
        function getRarityColor(rarity) {
            return { 
                'Common': 'var(--common)', 
                'Uncommon': 'var(--uncommon)',
                'Rare': 'var(--rare)', 
                'Epic': 'var(--epic)', 
                'Legendary': 'var(--legendary)',
                'Unique': 'var(--unique)',
                'Relic': 'var(--relic)'
            }[rarity] || '#fff';
        }

        // Close modal on background tap
        window.addEventListener('click', e => {
            if (e.target === document.getElementById('detailModal')) closeDetail();
        });
    </script>
</body>
</html>
