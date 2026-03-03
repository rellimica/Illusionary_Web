<?php
/**
 * MOBILE TRADING CENTER — GUIDED WIZARD
 * Step-by-step trade flow replacing desktop's 3-pane layout.
 * Views: Hub (default), Wizard (?partner=), Trade Detail (?trade_id=)
 */
$session_lifetime = 30 * 24 * 60 * 60;
ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);
session_name('ILLUSIONARY_SID');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$IMAGES_PATH = '/images/images/';

$env = loadEnv($ENV_PATH);
$DISCORD_TOKEN = $env['DISCORD_TOKEN'] ?? '';

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php?redirect=/trade.php");
    exit;
}

$my_id = (string)$_SESSION['user_data']['id'];

try {
    $dsn = "mysql:host=" . ($env['MYSQL_HOST'] ?? 'localhost') . ";dbname=" . ($env['MYSQL_DATABASE'] ?? '') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $env['MYSQL_USER'] ?? '', $env['MYSQL_PASSWORD'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => true
    ]);
} catch (Exception $e) { 
    header("Location: /db_error.php");
    exit;
}

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



$user_id = $my_id; 
$is_admin = isAdmin($user_id);

$partner_id = $_GET['partner'] ?? null;
$trade_view_id = $_GET['trade_id'] ?? null;
$partner_data = null;

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | Trading</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <link rel="stylesheet" href="/variations.css">
    <link rel="preload" href="/illusionary.png" as="image">
    <?php require_once __DIR__ . '/../theme-config.php'; injectTheme($THEME); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
    <div class="glass-bg"></div>
    <?php $nav_subtitle = 'Trading Center'; include 'nav.php'; ?>

    <main class="m-container" style="padding-bottom: 100px;">

    <?php if ($partner_id): ?>
        <!-- ===== GUIDED TRADE WIZARD ===== -->
        <div class="tw-wizard" id="tradeWizard">
            <!-- Partner Banner -->
            <div class="tw-partner-banner">
                <a href="trade.php" class="tw-back-link">← Exit</a>
                <div class="tw-partner-info">
                    <img src="<?php echo $partner_data['avatar']; ?>" class="tw-partner-avatar">
                    <div>
                        <div class="tw-partner-label">TRADING WITH</div>
                        <div class="tw-partner-name"><?php echo htmlspecialchars($partner_data['username']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Step Indicator -->
            <div class="tw-stepper">
                <div class="tw-step-track">
                    <div class="tw-step-fill" id="stepFill"></div>
                </div>
                <div class="tw-step-dots">
                    <div class="tw-dot active" data-step="1">
                        <span class="tw-dot-num">1</span>
                        <span class="tw-dot-label">Offer</span>
                    </div>
                    <div class="tw-dot" data-step="2">
                        <span class="tw-dot-num">2</span>
                        <span class="tw-dot-label">Request</span>
                    </div>
                    <div class="tw-dot" data-step="3">
                        <span class="tw-dot-num">3</span>
                        <span class="tw-dot-label">Review</span>
                    </div>
                    <div class="tw-dot" data-step="4">
                        <span class="tw-dot-num">4</span>
                        <span class="tw-dot-label">Confirm</span>
                    </div>
                </div>
            </div>

            <!-- Step Panels Container -->
            <div class="tw-panels" id="stepPanels">

                <!-- STEP 1: YOUR OFFER -->
                <div class="tw-panel active" data-step="1" id="panel1">
                    <div class="tw-panel-header">
                        <h2 class="tw-panel-title">What will you offer?</h2>
                        <p class="tw-panel-hint">Tap a card, then select individual serial numbers to include in your proposal.</p>
                    </div>
                    <!-- SN Picker (shared, moves between steps) -->
                    <div class="tw-sn-picker" id="snPicker1" style="display:none;">
                        <div class="tw-sn-header">
                            <span class="tw-sn-name" id="snName1">Card Name</span>
                            <span class="tw-sn-status" id="snStatus1"></span>
                        </div>
                        <div class="tw-sn-pills" id="snPills1"></div>
                    </div>
                    <div class="tw-card-grid" id="myCardGrid">
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                    </div>
                    <!-- Staged summary for this step -->
                    <div class="tw-staged" id="staged1" style="display:none;">
                        <div class="tw-staged-label">YOUR OFFER <span id="myCount">0</span></div>
                        <div class="tw-staged-row" id="myStaged"></div>
                    </div>
                </div>

                <!-- STEP 2: THEIR VAULT -->
                <div class="tw-panel" data-step="2" id="panel2">
                    <div class="tw-panel-header">
                        <h2 class="tw-panel-title">What do you want?</h2>
                        <p class="tw-panel-hint">Browse <?php echo htmlspecialchars($partner_data['username']); ?>'s vault and pick the cards you'd like to receive.</p>
                    </div>
                    <div class="tw-sn-picker" id="snPicker2" style="display:none;">
                        <div class="tw-sn-header">
                            <span class="tw-sn-name" id="snName2">Card Name</span>
                            <span class="tw-sn-status" id="snStatus2"></span>
                        </div>
                        <div class="tw-sn-pills" id="snPills2"></div>
                    </div>
                    <div class="tw-card-grid" id="partnerCardGrid">
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                        <div class="skeleton" style="aspect-ratio: 2/3; border-radius: 10px;"></div>
                    </div>
                    <div class="tw-staged" id="staged2" style="display:none;">
                        <div class="tw-staged-label">REQUESTING <span id="theirCount">0</span></div>
                        <div class="tw-staged-row" id="theirStaged"></div>
                    </div>
                </div>

                <!-- STEP 3: REVIEW -->
                <div class="tw-panel" data-step="3" id="panel3">
                    <div class="tw-panel-header">
                        <h2 class="tw-panel-title">Review Your Deal</h2>
                        <p class="tw-panel-hint">Make sure everything looks right before proceeding.</p>
                    </div>
                    <div class="tw-review" id="reviewContainer">
                        <!-- You Offer Section -->
                        <div class="tw-review-section">
                            <div class="tw-review-label">
                                <span class="tw-review-icon">↑</span>
                                YOU SEND
                                <span class="tw-review-count" id="reviewMyCount">0 cards</span>
                            </div>
                            <div class="tw-review-grid" id="reviewMyGrid"></div>
                            <button class="tw-edit-btn" onclick="goToStep(1)">✎ Edit Offer</button>
                        </div>
                        <div class="tw-review-divider">
                            <span>⇄</span>
                        </div>
                        <!-- You Receive Section -->
                        <div class="tw-review-section">
                            <div class="tw-review-label">
                                <span class="tw-review-icon">↓</span>
                                YOU RECEIVE
                                <span class="tw-review-count" id="reviewTheirCount">0 cards</span>
                            </div>
                            <div class="tw-review-grid" id="reviewTheirGrid"></div>
                            <button class="tw-edit-btn" onclick="goToStep(2)">✎ Edit Request</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: CONFIRM -->
                <div class="tw-panel" data-step="4" id="panel4">
                    <div class="tw-panel-header">
                        <h2 class="tw-panel-title">Confirm & Send</h2>
                        <p class="tw-panel-hint">Complete the security check below, then send your proposal to <?php echo htmlspecialchars($partner_data['username']); ?>.</p>
                    </div>
                    <div class="tw-confirm-box">
                        <!-- Quick summary -->
                        <div class="tw-confirm-summary">
                            <div class="tw-confirm-stat">
                                <span class="tw-confirm-num" id="confirmMyN">0</span>
                                <span class="tw-confirm-label">cards offered</span>
                            </div>
                            <div class="tw-confirm-arrow">⇄</div>
                            <div class="tw-confirm-stat">
                                <span class="tw-confirm-num" id="confirmTheirN">0</span>
                                <span class="tw-confirm-label">cards requested</span>
                            </div>
                        </div>
                        <!-- Turnstile -->
                        <div class="tw-turnstile-wrap">
                            <div class="tw-turnstile-label">IDENTITY VERIFICATION</div>
                            <div class="cf-turnstile" data-sitekey="<?php echo $env['TURNSTILE_SITE_KEY'] ?? ''; ?>" data-theme="dark"></div>
                        </div>
                        <button class="tw-submit-btn" id="submitBtn" onclick="proposeTrade()">
                            <span class="tw-submit-icon"></span>
                            Send Proposal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Nav Bar (Back / Next) -->
            <div class="tw-nav-bar" id="wizardNav">
                <button class="tw-nav-back" id="navBack" onclick="prevStep()">← Back</button>
                <div class="tw-nav-step" id="navStepLabel">Step 1 of 4</div>
                <button class="tw-nav-next" id="navNext" onclick="nextStep()">Next →</button>
            </div>
        </div>

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
        <!-- ===== TRADE DETAIL VIEW ===== -->
        <a href="trade.php" class="m-trade-back">← Back to Trading Floor</a>

        <?php if ($trade['status'] !== 'pending'): ?>
            <div class="m-trade-status m-status-<?php echo $trade['status']; ?>">
                TRANSACTION <?php echo strtoupper($trade['status']); ?> • <?php echo date('Y-m-d H:i', strtotime($trade['updated_at'])); ?>
            </div>
        <?php endif; ?>

        <div class="m-trade-detail-title">PROPOSAL #<?php echo str_pad($trade_view_id, 4, '0', STR_PAD_LEFT); ?></div>

        <div id="tradeSummaryStage">
            <div class="skeleton" style="height: 200px; border-radius: 15px; margin-bottom: 15px;"></div>
            <div class="skeleton" style="height: 200px; border-radius: 15px;"></div>
        </div>

        <?php if ($trade['status'] === 'pending'): ?>
            <div class="m-trade-actions">
                <?php if ($is_receiver): ?>
                    <button class="m-btn-accept" onclick="respondTrade(<?php echo $trade_view_id; ?>, 'accepted')">Accept</button>
                    <button class="m-btn-decline" onclick="respondTrade(<?php echo $trade_view_id; ?>, 'declined')">Decline</button>
                <?php else: ?>
                    <button class="m-btn-decline" onclick="respondTrade(<?php echo $trade_view_id; ?>, 'cancelled')" style="border-color: var(--text-muted); color: var(--text-muted);">Cancel Proposal</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- ===== TRADE HUB (Default) ===== -->
        <div class="tw-hub">
            <div class="tw-hub-header">
                <img src="/illusionary.png" class="tw-hub-logo" loading="lazy">
                <h2 class="gradient-text tw-hub-title">Trading Floor</h2>
                <p class="tw-hub-subtitle">Select a collector to begin a guided trade.</p>
            </div>

            <!-- Search -->
            <div class="tw-search">
                <form action="trade.php" method="GET">
                    <input type="text" name="partner" placeholder="Paste a Discord ID to trade directly..." required>
                </form>
            </div>

            <!-- Active Requests -->
            <div id="pendingTradesWrapper" style="display: none; margin-bottom: 25px;">
                <div class="tw-section-title">
                    <span>⚡ ACTIVE REQUESTS</span>
                </div>
                <div id="pendingTradesList" class="tw-request-list"></div>
            </div>

            <!-- Collector Pool -->
            <div class="tw-section-title">
                <span>👥 COLLECTOR POOL</span>
            </div>
            <div id="activeCollectorsList" class="tw-collector-pool"></div>
        </div>
    <?php endif; ?>

    </main>

    <script>
        const imagesPath = '<?php echo $IMAGES_PATH; ?>';
        const partnerID = '<?php echo $partner_id; ?>';
        const myID = '<?php echo $my_id; ?>';

        // === WIZARD STATE ===
        let currentStep = 1;
        let cardMeta = {};
        let myOffer = {};
        let theirOffer = {};
        let myInventoryMap = {};
        let theirInventoryMap = {};
        let activeSNCard = { 1: null, 2: null }; // Per-step active SN card

        window.addEventListener('load', () => {
            hydrateSelection();
            if (partnerID) hydrateWizard();
            <?php if ($trade_view_id): ?>
                hydrateTradeDetails(<?php echo $trade_view_id; ?>);
            <?php endif; ?>
        });

        // =====================
        //   WIZARD NAVIGATION
        // =====================
        function goToStep(n) {
            if (n < 1 || n > 4) return;
            currentStep = n;

            // Update panels
            document.querySelectorAll('.tw-panel').forEach(p => {
                p.classList.toggle('active', parseInt(p.dataset.step) === n);
            });

            // Update stepper dots
            document.querySelectorAll('.tw-dot').forEach(d => {
                const s = parseInt(d.dataset.step);
                d.classList.toggle('active', s === n);
                d.classList.toggle('done', s < n);
            });

            // Update fill bar
            const fill = document.getElementById('stepFill');
            if (fill) fill.style.width = `${((n - 1) / 3) * 100}%`;

            // Update nav bar
            const navBack = document.getElementById('navBack');
            const navNext = document.getElementById('navNext');
            const navLabel = document.getElementById('navStepLabel');

            if (navBack) navBack.style.visibility = n === 1 ? 'hidden' : 'visible';
            if (navLabel) navLabel.textContent = `Step ${n} of 4`;

            if (navNext) {
                if (n === 3) {
                    navNext.textContent = 'Confirm →';
                } else if (n === 4) {
                    navNext.style.display = 'none';
                } else {
                    navNext.textContent = 'Next →';
                    navNext.style.display = '';
                }
            }

            // Populate review on step 3
            if (n === 3) populateReview();
            // Populate confirm on step 4
            if (n === 4) populateConfirm();

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextStep() {
            // Validate before advancing
            if (currentStep === 1) {
                const count = Object.values(myOffer).reduce((s, ids) => s + ids.length, 0);
                if (count === 0) {
                    Swal.fire({ title: 'No Cards Selected', text: 'Tap a card and select at least one serial number to offer.', icon: 'info', confirmButtonColor: '#ff00ea' });
                    return;
                }
            }
            // Step 2 has no validation — requesting cards is optional (one-sided trades allowed)
            goToStep(currentStep + 1);
        }

        function prevStep() {
            if (currentStep === 1) {
                window.location.href = 'trade.php';
                return;
            }
            goToStep(currentStep - 1);
        }

        // =====================
        //    INVENTORY HYDRATION
        // =====================
        async function hydrateWizard() {
            const fd = new FormData();
            fd.append('action', 'get_inventories');
            fd.append('partner_id', partnerID);

            try {
                const d = await secureFetch('/api/trade.php', { method: 'POST', body: fd });
                if (d.success) {
                    [...d.my_cards, ...d.partner_cards].forEach(c => {
                        if (!cardMeta[c.card_id]) {
                            cardMeta[c.card_id] = JSON.parse(JSON.stringify(c));
                        } else {
                            c.sns.forEach(snObj => {
                                const iid = typeof snObj === 'object' ? snObj.id : snObj;
                                const exists = cardMeta[c.card_id].sns.some(s => (typeof s === 'object' ? s.id : s) == iid);
                                if (!exists) cardMeta[c.card_id].sns.push(snObj);
                            });
                        }
                    });

                    d.my_cards.forEach(c => myInventoryMap[c.card_id] = c);
                    d.partner_cards.forEach(c => theirInventoryMap[c.card_id] = c);
                    
                    renderCardGrid('mine', d.my_cards, 'myCardGrid');
                    renderCardGrid('theirs', d.partner_cards, 'partnerCardGrid');
                }
            } catch (e) { console.error("Wizard hydration failed", e); }
        }

        function renderCardGrid(side, cards, gridId) {
            const grid = document.getElementById(gridId);
            if (!grid) return;
            grid.innerHTML = '';
            
            if (cards.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted); font-size: 0.85rem;">No cards available.</div>';
                return;
            }

            cards.forEach(c => {
                const isTradeable = parseInt(c.is_trade) === 1;
                const offer = side === 'mine' ? myOffer : theirOffer;
                const selectedCount = offer[c.card_id] ? offer[c.card_id].length : 0;
                const stepN = side === 'mine' ? 1 : 2;

                const div = document.createElement('div');
                div.className = `tw-card-tile ${isTradeable ? '' : 'locked'} ${selectedCount > 0 ? 'selected' : ''}`;
                div.dataset.cardId = c.card_id;

                div.onclick = () => {
                    if (isTradeable || c.sns.length > 0) showSNPicker(stepN, side, c);
                };

                div.innerHTML = `
                    <img src="${imagesPath}${c.filename}" alt="${c.name}" loading="lazy">
                    ${!isTradeable ? '<div class="tw-lock-badge">🔒</div>' : ''}
                    ${selectedCount > 0 ? `<div class="tw-selected-badge">${selectedCount}</div>` : ''}
                    <div class="tw-card-label">${c.name.replace(/_/g, ' ')}</div>
                    <div class="tw-card-qty">x${c.count}</div>
                `;
                grid.appendChild(div);
            });
        }

        // =====================
        //    SN PICKER
        // =====================
        function showSNPicker(stepN, side, card) {
            const picker = document.getElementById(`snPicker${stepN}`);
            const nameEl = document.getElementById(`snName${stepN}`);
            const statusEl = document.getElementById(`snStatus${stepN}`);
            const pillsEl = document.getElementById(`snPills${stepN}`);
            const isTradeable = parseInt(card.is_trade) === 1;

            activeSNCard[stepN] = card;

            nameEl.textContent = card.name.replace(/_/g, ' ');
            statusEl.textContent = isTradeable ? 'Tap SN to select' : 'UNTRADEABLE';
            statusEl.style.color = isTradeable ? 'var(--accent-secondary)' : '#ff4e4e';

            const offer = side === 'mine' ? myOffer : theirOffer;
            const selectedIds = offer[card.card_id] || [];

            pillsEl.innerHTML = (card.sns || []).map(inst => {
                const iid = typeof inst === 'object' ? inst.id : inst;
                const sn = typeof inst === 'object' ? inst.sn : inst;
                const v = typeof inst === 'object' ? inst.variant : null;
                const vClass = v ? `variant-${v}` : '';
                const isActive = selectedIds.includes(iid);
                
                if (!isTradeable) {
                    return `<span class="tw-sn-pill disabled ${vClass}">#${sn}</span>`;
                }
                return `<span class="tw-sn-pill ${isActive ? 'active' : ''} ${vClass}" onclick="toggleSN(event, '${side}', ${card.card_id}, ${iid})">#${sn}</span>`;
            }).join('');

            picker.style.display = 'block';
            picker.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function toggleSN(e, side, cardId, instanceId) {
            e.stopPropagation();
            const offer = side === 'mine' ? myOffer : theirOffer;
            if (!offer[cardId]) offer[cardId] = [];
            
            const idx = offer[cardId].indexOf(instanceId);
            if (idx > -1) {
                offer[cardId].splice(idx, 1);
                if (offer[cardId].length === 0) delete offer[cardId];
            } else {
                offer[cardId].push(instanceId);
            }

            const stepN = side === 'mine' ? 1 : 2;
            const map = side === 'mine' ? myInventoryMap : theirInventoryMap;
            
            // Refresh SN picker
            if (activeSNCard[stepN] && activeSNCard[stepN].card_id == cardId) {
                showSNPicker(stepN, side, activeSNCard[stepN]);
            }

            // Update staged area
            updateStaged(side);

            // Refresh card grid to show selection badges
            const gridId = side === 'mine' ? 'myCardGrid' : 'partnerCardGrid';
            const cards = side === 'mine' ? Object.values(myInventoryMap) : Object.values(theirInventoryMap);
            renderCardGrid(side, cards, gridId);
        }

        function updateStaged(side) {
            const offer = side === 'mine' ? myOffer : theirOffer;
            const stagedEl = document.getElementById(side === 'mine' ? 'staged1' : 'staged2');
            const countEl = document.getElementById(side === 'mine' ? 'myCount' : 'theirCount');
            const rowEl = document.getElementById(side === 'mine' ? 'myStaged' : 'theirStaged');

            let total = 0;
            let html = '';

            for (const [id, ids] of Object.entries(offer)) {
                const meta = cardMeta[id];
                if (!meta) continue;
                total += ids.length;
                ids.forEach(iid => {
                    const inst = meta.sns.find(s => (typeof s === 'object' ? s.id : s) == iid);
                    const sn = inst ? (typeof inst === 'object' ? inst.sn : inst) : '?';
                    html += `
                        <div class="tw-staged-card ${inst && inst.variant ? 'variant-' + inst.variant : ''}" onclick="removeSN('${side}', ${id}, ${iid})">
                            <img src="${imagesPath}${meta.filename}" class="${inst && inst.variant ? 'variant-' + inst.variant : ''}" loading="lazy">
                            <span class="tw-staged-sn">#${sn}</span>
                            <span class="tw-staged-x">×</span>
                        </div>
                    `;
                });
            }

            if (countEl) countEl.textContent = total;
            if (rowEl) rowEl.innerHTML = html;
            if (stagedEl) stagedEl.style.display = total > 0 ? 'block' : 'none';
        }

        function removeSN(side, cardId, instanceId) {
            const offer = side === 'mine' ? myOffer : theirOffer;
            if (!offer[cardId]) return;
            const idx = offer[cardId].indexOf(instanceId);
            if (idx > -1) offer[cardId].splice(idx, 1);
            if (offer[cardId].length === 0) delete offer[cardId];

            updateStaged(side);
            const gridId = side === 'mine' ? 'myCardGrid' : 'partnerCardGrid';
            const cards = side === 'mine' ? Object.values(myInventoryMap) : Object.values(theirInventoryMap);
            renderCardGrid(side, cards, gridId);
        }

        // =====================
        //    REVIEW (Step 3)
        // =====================
        function populateReview() {
            renderReviewSide(myOffer, 'reviewMyGrid', 'reviewMyCount');
            renderReviewSide(theirOffer, 'reviewTheirGrid', 'reviewTheirCount');
        }

        function renderReviewSide(offer, gridId, countId) {
            const grid = document.getElementById(gridId);
            const countEl = document.getElementById(countId);
            if (!grid) return;

            let total = 0;
            let html = '';

            for (const [id, ids] of Object.entries(offer)) {
                const meta = cardMeta[id];
                if (!meta) continue;
                total += ids.length;
                ids.forEach(iid => {
                    const inst = meta.sns.find(s => (typeof s === 'object' ? s.id : s) == iid);
                    const sn = inst ? (typeof inst === 'object' ? inst.sn : inst) : '?';
                    html += `
                        <div class="tw-review-card ${inst && inst.variant ? 'variant-' + inst.variant : ''}">
                            <img src="${imagesPath}${meta.filename}" class="${inst && inst.variant ? 'variant-' + inst.variant : ''}">
                            <div class="tw-review-card-name">${meta.name.replace(/_/g, ' ')}</div>
                            <div class="tw-review-card-sn">#${sn}</div>
                        </div>
                    `;
                });
            }

            grid.innerHTML = html || '<div style="text-align: center; color: var(--text-muted); font-size: 0.8rem; padding: 20px;">No cards selected</div>';
            if (countEl) countEl.textContent = `${total} card${total !== 1 ? 's' : ''}`;
        }

        // =====================
        //    CONFIRM (Step 4)
        // =====================
        function populateConfirm() {
            const myN = Object.values(myOffer).reduce((s, ids) => s + ids.length, 0);
            const theirN = Object.values(theirOffer).reduce((s, ids) => s + ids.length, 0);
            document.getElementById('confirmMyN').textContent = myN;
            document.getElementById('confirmTheirN').textContent = theirN;
        }

        function proposeTrade() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Sending...';

            const fd = new FormData();
            fd.append('action', 'propose_trade');
            fd.append('partner_id', partnerID);
            fd.append('your_offer', JSON.stringify(myOffer));
            fd.append('their_offer', JSON.stringify(theirOffer));
            fd.append('turnstile_token', turnstile.getResponse());

            secureFetch('/api/trade.php', { method: 'POST', body: fd })
                .then(d => {
                    if (d.success) {
                        Swal.fire({ 
                            title: '📨 Proposal Sent!', 
                            text: 'Your trade proposal has been delivered. You\'ll be notified when they respond.', 
                            icon: 'success',
                            confirmButtonColor: '#ff00ea'
                        }).then(() => { window.location.href = 'trade.php'; });
                    }
                })
                .catch(e => {
                    Swal.fire({ title: 'Proposal Error', text: e.message, icon: 'error' });
                    btn.disabled = false;
                    btn.innerHTML = '<span class="tw-submit-icon">🚀</span> Send Proposal';
                    if (typeof turnstile !== 'undefined') turnstile.reset();
                });
        }

        // =====================
        //    HUB HYDRATION
        // =====================
        async function hydrateSelection() {
            const mainPending = document.getElementById('pendingTradesList');
            const mainCollectors = document.getElementById('activeCollectorsList');

            if (mainPending) mainPending.innerHTML = '<div class="skeleton" style="height: 60px; border-radius: 12px;"></div>';
            if (mainCollectors) mainCollectors.innerHTML = Array(4).fill('<div class="skeleton" style="height: 70px; border-radius: 14px;"></div>').join('');

            const fd = new FormData();
            fd.append('action', 'get_selection_data');

            try {
                const data = await secureFetch('/api/trade.php', { method: 'POST', body: fd });
                if (data.success) {
                    // Pending trades
                    if (data.pending.length > 0) {
                        const wrapper = document.getElementById('pendingTradesWrapper');
                        if (wrapper) wrapper.style.display = 'block';
                        if (mainPending) {
                            mainPending.innerHTML = data.pending.map(t => `
                                <a href="?trade_id=${t.id}" class="tw-request-item ${t.is_outgoing ? '' : 'incoming'}">
                                    <img src="${t.other_avatar}" class="tw-req-avatar">
                                    <div class="tw-req-info">
                                        <div class="tw-req-name">${t.other_username}</div>
                                        <div class="tw-req-status">${t.is_outgoing ? 'Awaiting Response' : '⚡ ACTION REQUIRED'}</div>
                                    </div>
                                    <span class="tw-req-arrow">VIEW →</span>
                                </a>
                            `).join('');
                        }
                    } else {
                        const wrapper = document.getElementById('pendingTradesWrapper');
                        if (wrapper) wrapper.style.display = 'none';
                    }

                    // Collectors
                    if (mainCollectors) {
                        if (data.others.length === 0) {
                            mainCollectors.innerHTML = '<div style="text-align: center; padding: 30px; color: var(--text-muted); font-size: 0.85rem;">No collectors available right now.</div>';
                        } else {
                            mainCollectors.innerHTML = data.others.map(o => `
                                <a href="?partner=${o.user_discord_id}" class="tw-collector-card">
                                    <div class="tw-collector-left">
                                        <img src="${o.avatar}" class="tw-collector-avatar">
                                        <div class="tw-collector-details">
                                            <span class="tw-collector-tag">${o.category || 'COLLECTOR'}</span>
                                            <div class="tw-collector-name">${o.username}</div>
                                            <div class="tw-collector-count">${o.card_count} cards</div>
                                        </div>
                                    </div>
                                    <span class="tw-collector-go">TRADE →</span>
                                </a>
                            `).join('');
                        }
                    }
                }
            } catch (e) { console.error("Selection hydration failed", e); }
        }

        // =====================
        //    TRADE DETAIL
        // =====================
        async function hydrateTradeDetails(tradeId) {
            const fd = new FormData();
            fd.append('action', 'get_trade_details');
            fd.append('trade_id', tradeId);

            try {
                const data = await secureFetch('/api/trade.php', { method: 'POST', body: fd });
                if (data.success) {
                    const stage = document.getElementById('tradeSummaryStage');

                    const renderSide = (user, items, accent, label) => `
                        <div class="m-summary-side">
                            <div class="m-summary-user">
                                <img src="${user.avatar}" style="width: 35px; height: 35px; border-radius: 50%; border: 2px solid var(--accent-${accent}); object-fit: cover;">
                                <div>
                                    <div style="font-size: 0.55rem; color: var(--text-muted); font-weight: 800; letter-spacing: 1px;">${label}</div>
                                    <div style="font-weight: 800; color: #fff;">${user.username.toUpperCase()}</div>
                                </div>
                            </div>
                            <div class="m-summary-cards">
                                ${items.map(itm => `
                                    <div class="m-summary-card ${itm.variations ? 'variant-' + itm.variations : ''}">
                                        <img src="${imagesPath}${itm.filename}" class="${itm.variations ? 'variant-' + itm.variations : ''}">
                                        <div style="font-size: 0.55rem; font-weight: 900; color: var(--accent-secondary);">${itm.sn_display ? "#" + itm.sn_display : "x" + itm.count}</div>
                                        <div style="font-size: 0.6rem; color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${itm.name.replace(/_/g, ' ')}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;

                    const senderItems = data.items.filter(i => i.user_id === data.trade.sender_id);
                    const receiverItems = data.items.filter(i => i.user_id === data.trade.receiver_id);

                    const senderLabel = (data.trade.sender_id === myID) ? 'YOUR ASSETS' : 'THEIR ASSETS';
                    const receiverLabel = (data.trade.receiver_id === myID) ? 'YOUR ASSETS' : 'THEIR ASSETS';

                    stage.innerHTML = renderSide(data.sender, senderItems, 'primary', senderLabel) +
                                     '<div class="m-exchange-divider" style="margin: 10px 0;">⇄</div>' +
                                     renderSide(data.receiver, receiverItems, 'secondary', receiverLabel);
                }
            } catch (e) { console.error("Trade hydration failed", e); }
        }

        function respondTrade(tid, status) {
            const fd = new FormData();
            fd.append('action', 'respond_trade');
            fd.append('trade_id', tid);
            fd.append('status', status);

            secureFetch('/api/trade.php', { method: 'POST', body: fd })
                .then(d => {
                    if (d.success) {
                        Swal.fire({ title: 'Updated', text: `Trade ${status}.`, icon: 'success', toast: true, position: 'top', showConfirmButton: false, timer: 2000 })
                            .then(() => { window.location.href = 'trade.php'; });
                    }
                })
                .catch(e => {
                    Swal.fire({ title: 'Failed', text: e.message, icon: 'error' });
                });
        }
    </script>
</body>
</html>
