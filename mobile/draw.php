<?php
/**
 * MOBILE CARD FORGE
 * Touch-optimized draw system with flip animation.
 */
session_name('ILLUSIONARY_SID');
session_start();

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php?redirect=/draw.php");
    exit;
}

require_once __DIR__ . '/../config.php';
define('FORGE_LOCKED', false);
$my_id = (string)$_SESSION['user_data']['id'];
$IMAGES_PATH = '/images/images/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | Card Forge</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <link rel="stylesheet" href="/variations.css">
    <?php require_once __DIR__ . '/../theme-config.php'; injectTheme($THEME); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <style>
        .m-variant-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .m-variant-badge.active {
            opacity: 1;
            transform: scale(1);
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    <!-- Fake Apache Error Container -->
    <div id="fakeApacheError" style="display:none; position:fixed; inset:0; background:white; color:black; font-family:serif; padding:30px; z-index:99999; text-align: left;">
        <h1 style="font-family: serif; color: black; font-size: 1.5em; margin-bottom: 0.5em;">Forbidden</h1>
        <p style="font-family: serif; font-size: 1em;">You don't have permission to access /draw.php on this server.</p>
        <hr style="border: 0; border-top: 1px solid #ccc; margin: 1em 0;">
        <address style="font-family: serif; font-style: italic; font-size: 0.85em;">Apache/2.4.41 (Ubuntu) Server at illusionary.cards Port 80</address>
    </div>

    <?php $nav_subtitle = 'Card Forge'; include 'nav.php'; ?>

    <main class="m-draw-stage">
        <div class="m-card-container <?php echo FORGE_LOCKED ? 'is-locked' : ''; ?>" id="forgeCard" onclick="handleFlip()">
            <div class="m-card-inner">
                <div class="m-card-face m-card-back">
                    <?php if (FORGE_LOCKED): ?>
                    <div class="m-restricted-overlay">
                        <div class="m-lock-icon">🔒</div>
                        <div class="m-lock-text">Access Restricted</div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="m-card-face m-card-front">
                    <img src="" id="cardImg" alt="">
                </div>
            </div>
        </div>

        <div class="m-mana-info">Costs 1 Mana</div>
        <button class="m-btn-forge" id="forgeBtn" onclick="doForge()" <?php echo FORGE_LOCKED ? 'disabled' : ''; ?>>
            <?php echo FORGE_LOCKED ? 'System Offline' : 'Forge Card'; ?>
        </button>

        <div class="m-result-meta" id="resultBlock">
            <div id="variantBadge" class="m-variant-badge"></div>
            <div class="m-rarity-text" id="rarityLabel"></div>
            <div class="m-name-text" id="nameLabel"></div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        let forgeData = null;
        let busy = false;
        let catalog = [];
        let shuffleInterval = null;
        
        window.forceError = false;
        window.forceErrorNext = () => {
            window.forceError = true;
            console.log("⚠️ NEXT FORGE: FORCE ERROR ACTIVE");
        };

        async function loadCatalog() {
            try {
                const res = await secureFetch('/api/catalog.php?action=get_catalog');
                if (res.success) catalog = res.data.map(c => c.filename);
            } catch (e) { console.error("Catalog load failed", e); }
        }
        loadCatalog();

        async function doForge() {
            if (busy) return;
            busy = true;

            const btn = document.getElementById('forgeBtn');
            const card = document.getElementById('forgeCard');
            const inner = card.querySelector('.m-card-inner');
            const result = document.getElementById('resultBlock');

            btn.disabled = true;
            btn.innerText = "Forging...";
            
            inner.style.transition = '';
            inner.style.transform = '';
            card.classList.remove('reveal-ready', 'flipped');
            inner.classList.add('spinning');
            result.classList.remove('active');

            if (catalog.length > 0) {
                shuffleInterval = setInterval(() => {
                    const rand = catalog[Math.floor(Math.random() * catalog.length)];
                    document.getElementById('cardImg').src = `<?php echo $IMAGES_PATH; ?>${rand}`;
                }, 100);
            }

            try {
                const fd = new FormData();
                fd.append('action', 'draw_card');
                const data = await secureFetch('/api/draw.php', { method: 'POST', body: fd });
                
                if (!data.success) {
                    Swal.fire({ title: 'Error', text: data.error, icon: 'error' });
                    stopSpin();
                    resetUI();
                    return;
                }

                forgeData = data;
                
                const img = new Image();
                img.src = `<?php echo $IMAGES_PATH; ?>${data.card.filename}`;
                await img.decode();

                setTimeout(async () => {
                    clearInterval(shuffleInterval);
                    
                    const isFakeError = window.forceError || Math.random() < 0.03;
                    window.forceError = false; 

                    if (isFakeError) {
                        const fakeErr = document.getElementById('fakeApacheError');
                        fakeErr.style.display = 'block';
                        await new Promise(r => setTimeout(r, 3000));
                        fakeErr.style.display = 'none';
                        btn.innerText = "RECOVERING...";
                        await new Promise(r => setTimeout(r, 1000));
                    }

                    // Reset variants
                    const cardFront = document.querySelector('.m-card-front');
                    const cardImg = document.getElementById('cardImg');
                    const vBadge = document.getElementById('variantBadge');
                    
                    cardFront.className = 'm-card-face m-card-front';
                    cardImg.className = '';
                    vBadge.className = 'm-variant-badge';
                    vBadge.innerText = '';

                    cardImg.src = img.src;
                    
                    if (data.card.variant) {
                        const vClass = `variant-${data.card.variant}`;
                        cardFront.classList.add(vClass);
                        cardImg.classList.add(vClass);
                        vBadge.classList.add(vClass);
                        vBadge.innerText = data.card.variant;
                    }

                    document.getElementById('nameLabel').innerText = data.card.name;
                    document.getElementById('rarityLabel').innerText = data.card.rarity;
                    document.getElementById('rarityLabel').style.color = getRarityColor(data.card.rarity);

                    const navMana = document.getElementById('nav-mana-value');
                    if (navMana) navMana.innerText = data.balance.toLocaleString();

                    inner.classList.remove('spinning');
                    inner.style.transform = '';
                    inner.style.transition = '';
                    void inner.offsetWidth; 
                    card.classList.add('reveal-ready');
                    
                    setTimeout(() => {
                        result.classList.add('active');
                        if (data.card.variant) vBadge.classList.add('active');
                        
                        const rarity = forgeData.card.rarity;
                        const isHigh = ['Legendary', 'Unique', 'Epic'].includes(rarity);
                        confetti({ 
                            particleCount: isHigh ? 150 : 60, 
                            spread: isHigh ? 100 : 60, 
                            origin: { y: 0.6 },
                            colors: isHigh ? undefined : [getRarityColor(rarity), '#ffffff']
                        });

                        if (Math.random() < 0.1) {
                            setTimeout(() => {
                                const notif = new Audio('/sounds/discord-notif.mp3');
                                notif.volume = 0.4;
                                notif.play().catch(e => {});
                            }, 500);
                        }

                        resetUI(true);
                    }, 2500); 
                }, 1500); 

            } catch (e) {
                Swal.fire({ title: 'Failed', text: e.message, icon: 'error' });
                stopSpin();
                resetUI();
            }
        }

        function stopSpin() {
            clearInterval(shuffleInterval);
            const card = document.getElementById('forgeCard');
            const inner = card.querySelector('.m-card-inner');
            const btn = document.getElementById('forgeBtn');
            inner.classList.remove('spinning');
            card.classList.remove('reveal-ready');
        }

        function handleFlip() {
            if (!forgeData || busy) return;
        }

        function resetUI(done = false) {
            const btn = document.getElementById('forgeBtn');
            busy = false;
            btn.disabled = false;
            btn.innerText = done ? "Forge Again" : "Forge Card";
            if (!done) forgeData = null;
        }

        function getRarityColor(r) {
            const colors = { 'Common': '#b0b0b0', 'Uncommon': '#4dfa7c', 'Rare': '#4e7cfe', 'Epic': '#a335ee', 'Legendary': '#ff8000', 'Unique': '#ff4e4e' };
            return colors[r] || '#fff';
        }
    </script>
</body>
</html>
