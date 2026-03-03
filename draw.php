
<?php
/**
 * DRAW SYSTEM FRONTEND - SIMPLIFIED
 */
session_name('ILLUSIONARY_SID');
session_start();

// Mobile Detection
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua)) {
    header("Location: mobile/draw.php");
    exit;
}

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: auth.php?redirect=draw.php");
    exit;
}

require_once 'config.php';
define('FORGE_LOCKED', false); // Toggle this to lock/unlock
$my_id = (string)$_SESSION['user_data']['id'];
$IMAGES_PATH = 'images/images/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Card Forge</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="variations.css">
    <?php require_once 'theme-config.php'; injectTheme($THEME); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <style>
        .draw-stage {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            padding: 2rem;
        }

        .card-container {
            width: 320px;
            height: 480px;
            perspective: 1000px;
            cursor: pointer;
            margin-bottom: 3rem;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }

        .card-container.flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 16px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .card-back {
            background: #111 url('images/images/back.png') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-front {
            transform: rotateY(180deg);
            background: #000;
        }

        .card-front img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Locked State */
        .card-container.is-locked {
            cursor: not-allowed;
            filter: grayscale(0.8) contrast(0.8);
        }

        .card-container.is-locked .card-inner:hover {
            transform: none !important;
        }

        .restricted-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 100;
            backdrop-filter: blur(4px);
            pointer-events: none;
        }

        .lock-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 20px rgba(255, 0, 234, 0.5));
            animation: pulse 2s infinite alternate;
        }

        .lock-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #fff;
            text-shadow: 0 0 10px rgba(0,0,0,1);
        }

        @keyframes pulse {
            from { transform: scale(1); opacity: 0.8; }
            to { transform: scale(1.1); opacity: 1; }
        }

        .card-inner.spinning {
            animation: continuous-spin 1s linear infinite;
        }

        @keyframes continuous-spin {
            0% { transform: rotateY(0deg); }
            100% { transform: rotateY(360deg); }
        }

        /* Result reveal styling */
        .card-container.reveal-ready .card-inner {
            transition: transform 2.5s cubic-bezier(0.1, 0.8, 0.2, 1);
            transform: rotateY(1980deg); /* 5 full spins + 180deg to front */
        }

        .btn-forge {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            border: none;
            padding: 16px 48px;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            z-index: 10;
        }

        .btn-forge:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 0, 234, 0.3);
        }

        .btn-forge:disabled {
            filter: grayscale(1);
            opacity: 0.5;
            cursor: not-allowed;
        }

        .result-meta {
            text-align: center;
            margin-top: 2rem;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.4s ease;
        }

        .result-meta.active {
            opacity: 1;
            transform: translateY(0);
        }

        .rarity-text {
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .name-text {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            color: #fff;
        }

        .mana-info {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    <!-- Fake Apache Error Container -->
    <div id="fakeApacheError" style="display:none; position:fixed; inset:0; background:white; color:black; font-family:serif; padding:50px; z-index:99999; text-align: left;">
        <h1 style="font-family: serif; color: black; font-size: 2em; border-bottom: none; margin-bottom: 0.5em;">Forbidden</h1>
        <p style="font-family: serif; font-size: 1.1em;">You don't have permission to access /draw.php on this server.</p>
        <hr style="border: 0; border-top: 1px solid #ccc; margin: 1em 0;">
        <address style="font-family: serif; font-style: italic;">Apache/2.4.41 (Ubuntu) Server at illusionary.cards Port 80</address>
    </div>
    <?php $nav_subtitle = 'Card Forge'; include 'nav.php'; ?>

    <main class="draw-stage">
        <div class="card-container <?php echo FORGE_LOCKED ? 'is-locked' : ''; ?>" id="forgeCard" onclick="handleFlip()">
            <div class="card-inner">
                <div class="card-face card-back">
                    <?php if (FORGE_LOCKED): ?>
                    <div class="restricted-overlay">
                        <div class="lock-icon">🔒</div>
                        <div class="lock-text">Access Restricted</div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-face card-front">
                    <img src="" id="cardImg" alt="">
                </div>
            </div>
        </div>

        <div class="mana-info">Costs 1 Mana</div>
        <button class="btn-forge" id="forgeBtn" onclick="doForge()" <?php echo FORGE_LOCKED ? 'disabled' : ''; ?>>
            <?php echo FORGE_LOCKED ? 'System Offline' : 'Forge Card'; ?>
        </button>

        <div class="result-meta" id="resultBlock">
            <div class="rarity-text" id="rarityLabel"></div>
            <div class="name-text" id="nameLabel"></div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        let forgeData = null;
        let busy = false;
        let catalog = [];
        let shuffleInterval = null;
        
        // Debug Tools
        window.forceError = false;
        window.forceErrorNext = () => {
            window.forceError = true;
            console.log("⚠️ NEXT FORGE: FORCE ERROR ACTIVE");
        };

        // Fetch catalog for shuffle pool
        async function loadCatalog() {
            try {
                // api/catalog.php already filters for is_hidden = 0
                const res = await secureFetch('api/catalog.php?action=get_catalog');
                if (res.success) catalog = res.data.map(c => c.filename);
            } catch (e) { console.error("Catalog load failed", e); }
        }
        loadCatalog();

        async function doForge() {
            if (busy) return;
            busy = true;

            const btn = document.getElementById('forgeBtn');
            const card = document.getElementById('forgeCard');
            const inner = card.querySelector('.card-inner');
            const result = document.getElementById('resultBlock');

            btn.disabled = true;
            btn.innerText = "Forging...";
            
            // Start spinning & Shuffling
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
                const data = await secureFetch('api/draw.php', { method: 'POST', body: fd });
                
                if (!data.success) {
                    Swal.fire({ title: 'Error', text: data.error, icon: 'error' });
                    stopSpin();
                    resetUI();
                    return;
                }

                forgeData = data;
                
                // Preload final image
                const img = new Image();
                img.src = `<?php echo $IMAGES_PATH; ?>${data.card.filename}`;
                await img.decode();

                // Suspense delay
                setTimeout(async () => {
                    clearInterval(shuffleInterval);
                    
                    // CHANCE FOR FAKE ERROR (3%)
                    const isFakeError = window.forceError || Math.random() < 0.03;
                    window.forceError = false; 

                    if (isFakeError) {
                        const fakeErr = document.getElementById('fakeApacheError');
                        fakeErr.style.display = 'block';
                        
                        // Wait 3 seconds
                        await new Promise(r => setTimeout(r, 3000));
                        
                        fakeErr.style.display = 'none';
                        
                        // Briefly show a "recovering" state
                        btn.innerText = "RECOVERING...";
                        await new Promise(r => setTimeout(r, 1000));
                    }

                    // FINAL LANDING
                    document.getElementById('cardImg').src = img.src;
                    document.getElementById('nameLabel').innerText = data.card.name;
                    document.getElementById('rarityLabel').innerText = data.card.rarity;
                    document.getElementById('rarityLabel').style.color = getRarityColor(data.card.rarity);

                    // Update mana
                    const navMana = document.getElementById('nav-mana-value');
                    if (navMana) navMana.innerText = data.balance.toLocaleString();

                    inner.classList.remove('spinning');
                    inner.style.transform = ''; // IMPORTANT: Clear inline override
                    inner.style.transition = ''; // IMPORTANT: Clear inline override
                    void inner.offsetWidth; 
                    card.classList.add('reveal-ready');
                    
                    setTimeout(() => {
                        result.classList.add('active');
                        
                        // Universal Confetti
                        const rarity = forgeData.card.rarity;
                        const isHigh = ['Legendary', 'Unique', 'Epic'].includes(rarity);
                        confetti({ 
                            particleCount: isHigh ? 150 : 60, 
                            spread: isHigh ? 100 : 60, 
                            origin: { y: 0.6 },
                            colors: isHigh ? undefined : [getRarityColor(rarity), '#ffffff']
                        });

                        // Random chance for Discord Notification (10%)
                        if (Math.random() < 0.1) {
                            setTimeout(() => {
                                const notif = new Audio('sounds/discord-notif.mp3');
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
            const inner = card.querySelector('.card-inner');
            const btn = document.getElementById('forgeBtn');
            inner.classList.remove('spinning');
            card.classList.remove('reveal-ready');
            btn.style.filter = "";
        }

        function handleFlip() {
            // Landed on front already
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
    <?php include 'null-egg.php'; ?>
</body>
</html>
