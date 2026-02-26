<?php

/**
 * SECURE SYSTEM WARNING: 
 * NEVER share your session ID or cookies (like ILLUSIONARY_SID) with anyone. 
 * Sharing this ID is like giving someone your house keys; they can access 
 * your account and steal your cards.
 */
// Set secure session parameters for a 10-day lifetime
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

/**
 * 1. INITIAL SETUP & ENVIRONMENT
 * We load environmental variables (.env) to handle sensitive credentials 
 * such as Discord API keys and Database connection strings.
 */
require_once 'config.php';
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$IMAGES_PATH = 'images/images/';

$env = loadEnv($ENV_PATH);

if (isset($_GET['logout'])) {
    session_destroy();
    $kick = isset($_GET['kick']) ? '&kick=1' : '';
    header("Location: auth.php?logout=1$kick"); 
    exit;
}

/**
 * 2. AUTHENTICATION CHECK
 * Redirect to dedicated auth page if not logged in.
 */
if (!isset($_SESSION['user_authenticated'])) {
    $request = $_SERVER['REQUEST_URI'];
    // Only attach redirect if we're not already at the root/index
    if ($request !== '/' && $request !== '/index.php') {
        $redirect = urlencode($request);
        header("Location: auth.php?redirect=$redirect");
    } else {
        header("Location: auth.php");
    }
    exit;
}

/**
 * 3. DATABASE CONNECTION
 */
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
    header("Location: db_error.php");
    exit;
}



// 5. USER CONTEXT & PERMISSIONS
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

// --- HYDRATION ENDPOINT (MIGRATED TO api/collection.php) ---
// Expensive database queries for collection data have been moved to the centralized API directory.

/**
 * 7. PAGINATION META
 * We fetch just enough data to determine how many pages exist.
 * This ensures the navigation bars and UI structure are correct on the first paint.
 */

// Pagination Meta (Needed for structural skeleton rendering)
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | <?php echo $viewing_self ? 'Collection View' : ($target_user_data['username'] . "'s Collection"); ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="variations.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>

</head>
<body>
    <?php 
    // Mobile Detection
    $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");

    if ($iphone || $android || $palmpre || $ipod || $berry) {
        header("Location: mobile/index.php");
        exit;
    }
    ?>
    <?php include 'mobile-block.php'; ?>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Collection View';
    include 'nav.php'; 
    ?>



    <main class="container">
        <section id="statsContainer" class="stats-grid">
            <div class="stat-card">
                <div id="stat-total" class="stat-value skeleton"></div>
                <div class="stat-label">Total Cards</div>
            </div>
            <div class="stat-card">
                <div id="stat-unique" class="stat-value skeleton"></div>
                <div class="stat-label">Unique Collected</div>
            </div>
            <div class="stat-card">
                <div id="stat-completion" class="stat-value skeleton"></div>
                <div class="stat-label">Set Completion</div>
            </div>
            <div class="stat-card">
                <div id="stat-possible" class="stat-value skeleton"></div>
                <div class="stat-label">Possible Cards</div>
            </div>
        </section>

        <section class="collection-frame">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 0 10px;">
                <div style="font-family: 'Outfit'; font-weight: 800; font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo $viewing_self ? 'var(--accent-primary)' : 'var(--accent-secondary)'; ?>; box-shadow: 0 0 10px <?php echo $viewing_self ? 'var(--accent-primary)' : 'var(--accent-secondary)'; ?>;"></span>
                    Viewing: <span style="color: #fff;"><?php echo $viewing_self ? 'Your Collection' : $target_user_data['username']; ?></span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <?php if (!$viewing_self): ?>
                        <a href="index.php" style="padding: 6px 15px; font-size: 0.7rem; text-decoration: none; display: flex; align-items: center; gap: 6px; background: rgba(0, 229, 255, 0.03); border: 1px solid rgba(0, 229, 255, 0.15); color: var(--accent-secondary); border-radius: 8px; font-weight: 700; transition: all 0.2s;" onmouseover="this.style.background='rgba(0, 229, 255, 0.08)'; this.style.borderColor='rgba(0, 229, 255, 0.3)';" onmouseout="this.style.background='rgba(0, 229, 255, 0.03)'; this.style.borderColor='rgba(0, 229, 255, 0.15)';">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            My Collection
                        </a>
                    <?php endif; ?>
                    <button onclick="switchCollector()" style="padding: 6px 15px; font-size: 0.7rem; display: flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border); color: var(--text-muted); border-radius: 8px; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='rgba(255, 255, 255, 0.15)'; this.style.color='#fff';" onmouseout="this.style.background='rgba(255, 255, 255, 0.02)'; this.style.borderColor='var(--glass-border)'; this.style.color='var(--text-muted)';">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"></polyline><line x1="4" y1="20" x2="21" y2="3"></line><polyline points="21 16 21 21 16 21"></polyline><line x1="15" y1="15" x2="21" y2="21"></line><line x1="4" y1="4" x2="9" y2="9"></line></svg>
                        Switch Collector
                    </button>
                </div>
            </div>

            <div class="grid-labels-x">
                <div>1</div><div>2</div><div>3</div><div>4</div><div>5</div>
            </div>
            
            <div class="grid-body">
                <div class="grid-labels-y">
                    <div>A</div>
                    <div>B</div>
                </div>
                
                <div id="userCardGrid" class="user-grid">
                    <?php for ($i = 0; $i < 10; $i++): ?>
                        <div class="user-card-item empty skeleton"></div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?<?php echo isset($_GET['user_id']) ? 'user_id=' . htmlspecialchars($_GET['user_id']) . '&' : ''; ?>page=<?php echo $i; ?>" class="page-link <?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Card Detail Modal -->
    <div class="detail-modal" id="detailModal">
        <div class="detail-content">
            <span class="close-detail" onclick="closeDetail()">&times;</span>
            <div class="detail-visual" id="detailVisualWrap">
                <div id="modalCardStamp" class="modal-stamp-overlay">#000</div>
                <img src="" id="detailImg" alt="">
            </div>
            <div class="detail-info">
                <div class="detail-rarity" id="detailRarity">RARITY</div>
                <h1 class="detail-name" id="detailName">Card Name</h1>
                
                <div class="detail-stats" style="margin-top: 2rem;">
                    <div class="detail-stat-box">
                        <span class="detail-stat-label">Copies Owned</span>
                        <div class="detail-stat-value" id="detailCount">0</div>
                    </div>
                    <div class="detail-stat-box">
                        <span class="detail-stat-label">Acquired On</span>
                        <div class="detail-stat-value" id="detailDate" style="font-size: 1.1rem;">-</div>
                    </div>
                </div>

                <div id="instanceRegistry" class="sn-registry-box" style="display: none;">
                    <span class="registry-label">Card Registry</span>
                    <div class="sn-scroll-viewport">
                        <div id="snRegistryGrid" class="sn-grid-layout"></div>
                    </div>
                </div>

                <div id="instanceMessage" class="instance-message">
                    <span class="message-label">Registry Note</span>
                    <div id="messageText"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // SECURITY WARNING
        console.log('%cSTOP!', 'color: #ff4e4e; font-size: 60px; font-weight: 800; text-shadow: 0 0 20px rgba(255, 78, 78, 0.5);');
        console.log('%cThis is a browser feature intended for developers. If someone told you to copy-paste something here to modify your Illusionary account or get free cards, it is a SCAM and will give them full access to your collection!', 'font-size: 16px; color: #fff;');
        console.log('%cNEVER share your ILLUSIONARY_SID with anyone.', 'font-size: 16px; color: #ff00ea; font-weight: bold;');

        const IMAGES_PATH = '<?php echo $IMAGES_PATH; ?>';
        const currentPage = <?php echo $page; ?>;
        const targetUserId = '<?php echo $target_user_id; ?>';
        const isViewingSelf = <?php echo $viewing_self ? 'true' : 'false'; ?>;

        window.addEventListener('load', hydrateCollection);

        /**
         * HYDRATION FUNCTION
         * Triggered on page load. Fetches card data, stats, and completion percentage.
         * Replaces the '.skeleton' CSS classes with actual data once the request finishes.
         */
        async function hydrateCollection() {
            try {
                const data = await secureFetch(`api/collection.php?action=hydrate_collection&page=${currentPage}&user_id=${targetUserId}`);
                
                // 1. Stats
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

                // 2. User Status (Only update mana UI if viewing self)
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

                // 3. Card Grid
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
                            div.className = `user-card-item owned rarity-${card.rarity_name.toLowerCase().replace(/[^a-z0-9]/g, '-')}`;
                            div.onclick = () => openDetail(cleanName, card.rarity_name, `${IMAGES_PATH}${card.filename}`, card.count, rawTs, card.sns);
                            
                            const img = document.createElement('img');
                            img.src = IMAGES_PATH + card.filename;
                            img.alt = card.name;

                            div.appendChild(img);
                            
                            // Physical Stamp Integration
                            if (card.sns && card.sns.length > 0) {
                                const snVal = card.sns[0].sn;
                                div.innerHTML += `<div class="card-stamp">#${snVal}</div>`;
                            }

                            if (card.count > 1) {
                                div.innerHTML += `<div class="user-card-count">x${card.count}</div>`;
                            }
                        } else {
                            div.className = "user-card-item unowned";
                            div.onclick = openLockedDetail;
                            div.innerHTML = `<img src="${IMAGES_PATH}back.png" alt="Locked Card" class="unowned-icon">`;
                        }
                    } else {
                        div.className = "user-card-item empty";
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

        function applyModalVariant(variant) {
            const detailImg = document.getElementById('detailImg');
            const modalStamp = document.getElementById('modalCardStamp');
            const visualWrap = document.getElementById('detailVisualWrap');
            
            [detailImg, modalStamp, visualWrap].forEach(el => {
                if (!el) return;
                Array.from(el.classList).forEach(c => {
                    if (c.startsWith('variant-')) el.classList.remove(c);
                });
                if (variant) el.classList.add(`variant-${variant}`);
            });
        }

        let currentModalSNS = [];

        function openDetail(name, rarity, img, count, ts, sns = []) {
            currentModalSNS = sns; // Store for switching
            let dateStr = "Legendary Origin";
            if (ts > 0) {
                const date = new Date(ts * 1000);
                dateStr = date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
            }
            document.getElementById('detailName').innerText = name;
            document.getElementById('detailRarity').innerText = rarity;
            document.getElementById('detailRarity').style.color = getRarityColor(rarity);
            document.getElementById('detailImg').src = img;
            document.getElementById('detailCount').innerText = count;
            document.getElementById('detailDate').innerText = dateStr;
            document.getElementById('detailVisualWrap').style.filter = "none";

            document.getElementById('detailModal').style.display = 'flex';

            // Consolidated Registry Logic
            const registry = document.getElementById('instanceRegistry');
            const grid = document.getElementById('snRegistryGrid');
            const modalStamp = document.getElementById('modalCardStamp');
            const msgBox = document.getElementById('instanceMessage');

            if (sns && sns.length > 0) {
                const firstInst = sns[0];
                const firstSN = typeof firstInst === 'object' ? firstInst.sn : firstInst;
                modalStamp.innerText = `#${firstSN}`;
                
                // Clear and Apply Variant
                const v = firstInst ? firstInst.variant : null;
                applyModalVariant(v);

                // Show/Hide Registry
                registry.style.display = 'block';
                grid.innerHTML = sns.map((inst, index) => {
                    const snVal = typeof inst === 'object' ? inst.sn : inst;
                    const v = (inst && inst.variant) ? inst.variant : '';
                    const vClass = v ? `variant-${v}` : '';
                    return `<div class="sn-chip ${index === 0 ? 'active' : ''} ${vClass}" onclick="selectInstance('${snVal}', this, ${index})">#${snVal}</div>`;
                }).join('');

                // Display first instance message if present
                updateInstanceMessage(firstInst);
            } else {
                registry.style.display = 'none';
                msgBox.style.display = 'none';
                modalStamp.innerText = '';
                applyModalVariant(null);
            }
        }

        /**
         * Switch visible SN instance via consolidated registry
         */
        function selectInstance(sn, el, index) {
            const modalStamp = document.getElementById('modalCardStamp');
            const detailImg = document.getElementById('detailImg');
            modalStamp.innerText = `#${sn}`;
            
            // Sync Visuals
            const inst = currentModalSNS[index];
            const v = inst ? inst.variant : null;
            applyModalVariant(v);

            document.querySelectorAll('.sn-chip').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            
            // Update Message
            if (currentModalSNS && currentModalSNS[index]) {
                updateInstanceMessage(currentModalSNS[index]);
            }
            // Removed manual filter flash that was conflicting with CSS filters.
        }

        function updateInstanceMessage(inst) {
            const msgBox = document.getElementById('instanceMessage');
            const msgText = document.getElementById('messageText');
            
            if (inst && typeof inst === 'object' && inst.message && inst.message.trim() !== "") {
                msgText.innerText = inst.message;
                msgBox.style.display = 'block';
            } else {
                msgBox.style.display = 'none';
            }
        }

        function openLockedDetail() {
            document.getElementById('detailName').innerText = "Undiscovered Entity";
            document.getElementById('detailRarity').innerText = "CALCULATING...";
            document.getElementById('detailRarity').style.color = "#666";
            document.getElementById('detailImg').src = `${IMAGES_PATH}back.png`;
            document.getElementById('detailCount').innerText = "0";
            document.getElementById('detailDate').innerText = "N/A";
            document.getElementById('detailVisualWrap').style.filter = "grayscale(1) brightness(0.5) blur(5px)";
            
            // Clean UI
            if (document.getElementById('instanceRegistry')) {
                document.getElementById('instanceRegistry').style.display = 'none';
            }
            if (document.getElementById('instanceMessage')) {
                document.getElementById('instanceMessage').style.display = 'none';
            }
            document.getElementById('modalCardStamp').innerText = '';
            
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeDetail() { document.getElementById('detailModal').style.display = 'none'; }

        async function switchCollector() {
            const { value: userId } = await Swal.fire({
                title: 'Collector Search',
                text: 'Enter a Discord User ID to view their collection.',
                input: 'text',
                inputPlaceholder: 'Discord ID (e.g. 332684782888550410)',
                showCancelButton: true,
                confirmButtonText: 'Search',
                background: 'rgba(12, 10, 21, 0.95)',
                color: '#fff',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                customClass: {
                    input: 'swal2-input-custom'
                }
            });

            if (userId) {
                if (/^\d+$/.test(userId)) {
                    window.location.href = `index.php?user_id=${userId}`;
                } else {
                    Swal.fire({
                        title: 'Invalid ID',
                        text: 'Discord IDs must be numeric strings.',
                        icon: 'error'
                    });
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
                'Mythic': 'var(--mythic)',
                'Relic': 'var(--relic)'
            }[rarity] || '#fff';
        }

        window.onclick = e => { if (e.target == document.getElementById('detailModal')) closeDetail(); }


    </script>

    <?php include 'null-egg.php'; ?>
</body>
</html>
