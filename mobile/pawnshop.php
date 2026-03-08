<?php
/**
 * MOBILE PAWNSHOP
 * Optimized for touch interactions and vertical displays.
 */
session_name('ILLUSIONARY_SID');
session_start();

require_once __DIR__ . '/../config.php';
$ENV_PATH = '/root/Illusionary/.env';
$IMAGES_PATH = '/images/images/';

$env = loadEnv($ENV_PATH);

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php");
    exit;
}

$my_id = $_SESSION['user_data']['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | Null's Backroom</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;900&family=Playfair+Display:ital,wght@0,900;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <link rel="stylesheet" href="/variations.css">
    <link rel="preload" href="/illusionary.png" as="image">
    <?php 
    require_once __DIR__ . '/../theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        :root {
            --pawn-void: #0d0a08;
            --pawn-gold: #d4af37;
            --pawn-wood: #2c1e14;
            --pawn-parchment: #f5e6d3;
        }

        .m-pawn-layout {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            padding-bottom: 5rem;
        }

        /* --- THE DEALER --- */
        .m-dealer-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1rem;
        }

        .m-null-frame {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            border: 4px solid var(--pawn-gold);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            background: #000;
        }

        .m-null-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .m-null-dialogue {
            background: rgba(20, 15, 10, 0.9);
            border: 1px solid rgba(212, 175, 55, 0.2);
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-style: italic;
            color: var(--pawn-parchment);
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        /* --- ADMIN INTEL --- */
        .m-admin-intel-wrap {
            display: flex;
            justify-content: center;
            padding: 1rem 0;
            width: 100%;
        }
        .m-admin-intel-btn {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid var(--pawn-gold);
            color: var(--pawn-gold);
            padding: 8px 16px;
            border-radius: 4px;
            font-family: 'Outfit';
            font-weight: 800;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .m-admin-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.95);
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .m-admin-modal-content {
            background: #0d0a08;
            border: 1px solid var(--pawn-gold);
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 8px;
            padding: 1.5rem;
            position: relative;
        }
        .m-admin-modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--pawn-gold);
            font-size: 1.2rem;
        }
        .m-guide-section { margin-bottom: 1.5rem; border-bottom: 1px solid rgba(212, 175, 55, 0.1); padding-bottom: 0.8rem; }
        .m-guide-section:last-child { border-bottom: none; }
        .m-guide-title { color: var(--pawn-gold); font-family: 'Outfit'; font-weight: 900; margin-bottom: 0.5rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
        .m-guide-text { color: var(--pawn-parchment); opacity: 0.9; font-size: 0.75rem; line-height: 1.5; }

        /* --- THE TABLE --- */
        .m-table-section {
            background: linear-gradient(180deg, #1a1410 0%, #0d0a08 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-top: 3px solid var(--pawn-gold);
            border-radius: 8px;
            padding: 1.5rem;
            position: relative;
        }

        .m-table-label {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--pawn-gold);
            color: #000;
            font-family: 'Outfit';
            font-weight: 900;
            padding: 4px 15px;
            border-radius: 4px;
            font-size: 0.65rem;
            letter-spacing: 2px;
        }

        .m-slots {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .m-slot {
            aspect-ratio: 2/3;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .m-slot.filled {
            border-color: var(--pawn-gold);
        }

        .m-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .m-slot .remove-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: var(--pawn-gold);
            color: #000;
            border: none;
            border-radius: 3px;
            width: 20px;
            height: 20px;
            font-weight: 900;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        .m-valuation {
            text-align: center;
            font-family: 'Outfit';
            font-weight: 900;
            color: var(--pawn-gold);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            display: none;
        }
        .m-frag-count { font-size: 0.7rem; opacity: 0.6; margin-left: 0.3rem; font-weight: 700; color: var(--pawn-parchment); vertical-align: middle; }

        /* --- ARCHIVE DUST UI (MOBILE) --- */
        .m-dust-wrap {
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            width: 100%;
        }
        .m-dust-label {
            font-size: 0.55rem;
            color: var(--pawn-gold);
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            opacity: 0.7;
        }
        .m-dust-progress-bg {
            width: 80%;
            height: 3px;
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        .m-dust-progress-fill {
            height: 100%;
            background: var(--pawn-gold);
            width: 0%;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.4);
        }
        .m-dust-value {
            font-size: 0.6rem;
            font-weight: 700;
            color: var(--pawn-parchment);
            opacity: 0.5;
        }

        .m-bonus-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .m-bonus-tag {
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: var(--pawn-gold);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.55rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .m-deal-btn {
            width: 100%;
            padding: 1rem;
            background: var(--pawn-gold);
            border: none;
            border-radius: 4px;
            color: #000;
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.3;
            pointer-events: none;
            transition: all 0.3s;
        }

        .m-deal-btn.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* --- THE ARCHIVE --- */
        .m-vault-section {
            background: rgba(0,0,0,0.3);
            border-radius: 8px;
            padding: 1rem;
        }

        .m-vault-header {
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .m-demand-banner {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            padding: 8px 12px;
            border-radius: 4px;
            text-align: right;
        }

        .m-demand-label {
            font-size: 0.5rem;
            color: var(--pawn-gold);
            font-weight: 800;
            letter-spacing: 1px;
        }

        .m-demand-value {
            font-size: 0.75rem;
            color: #fff;
            font-weight: 900;
        }

        .m-pawn-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .m-vault-item {
            aspect-ratio: 2/3;
            background: #000;
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .m-vault-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.7;
        }

        .m-vault-item.selected {
            border-color: var(--pawn-gold);
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }

        .m-vault-item.selected img {
            opacity: 1;
        }

        .m-item-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background: var(--pawn-gold);
            color: #000;
            font-size: 0.6rem;
            font-weight: 900;
            padding: 1px 4px;
            border-radius: 2px;
        }

        /* --- SN MODAL --- */
        .m-sn-modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 6000;
            display: none;
            padding: 2rem;
            align-items: center;
            justify-content: center;
        }

        .m-sn-content {
            background: #0d0a08;
            border: 1px solid var(--pawn-gold);
            width: 100%;
            max-width: 300px;
            padding: 1.5rem;
            border-radius: 8px;
        }

        #snGrid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            max-height: 350px;
            overflow-y: auto;
            padding-right: 5px;
            margin-top: 1rem;
        }

        /* Scrollbar for mobile snGrid */
        #snGrid::-webkit-scrollbar { width: 3px; }
        #snGrid::-webkit-scrollbar-track { background: transparent; }
        #snGrid::-webkit-scrollbar-thumb { background: var(--pawn-gold); border-radius: 10px; }

        .m-sn-option {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            padding: 12px 10px;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            text-align: center;
        }

        .m-sn-option.selected {
            border-color: var(--pawn-gold);
            background: rgba(212, 175, 55, 0.1);
        }

        .m-sn-name {
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 0.9rem;
            color: var(--pawn-gold);
        }

        .m-variant-name {
            font-size: 0.55rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = "Null's Pawnshop";
    include 'nav.php'; 
    ?>

    <main class="m-pawn-layout">
        <!-- DEALER -->
        <section class="m-dealer-section">
            <div class="m-null-frame">
                <img src="/happynull.png" id="nullPortrait" alt="Null" loading="lazy">
            </div>
            <div style="font-family:'Outfit'; font-weight:900; font-size:0.7rem; letter-spacing:2px; color:var(--pawn-gold);" id="dealerStatus">DEALER AWAITING</div>
            <div class="m-null-dialogue" id="nullDialogue">
                "Seeking to trade history for essence? Show me what you've found."
            </div>
        </section>

        <!-- COUNTER -->
        <section class="m-table-section" id="tradeTable">
            <div class="m-table-label">EXCHANGE TABLE</div>
            <div class="m-slots">
                <div class="m-slot" id="slot-0"></div>
                <div class="m-slot" id="slot-1"></div>
                <div class="m-slot" id="slot-2"></div>
            </div>
            <div class="m-valuation" id="manaValuation">VALUATION: 0 MANA</div>
            <div class="m-bonus-list" id="bonusList"></div>

            <div class="m-dust-wrap" id="dustWrap" style="display: none;">
                <div class="m-dust-label">Archive Dust</div>
                <div class="m-dust-progress-bg">
                    <div class="m-dust-progress-fill" id="dustFill"></div>
                </div>
                <div class="m-dust-value"><span id="dustVal">0</span> / 1000 Fragments</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                <button class="m-deal-btn" id="finangleBtn" style="background: transparent; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--pawn-gold); display: none;" onclick="finangle()">Finangle</button>
                <button class="m-deal-btn" id="strikeDealBtn" onclick="strikeDeal()">Strike the Deal</button>
            </div>
        </section>

        <!-- ARCHIVE -->
        <section class="m-vault-section">
            <div class="m-vault-header">
                <div style="font-family:'Playfair Display'; font-weight:900; font-size:1.2rem; color:var(--pawn-gold);">The Backroom</div>
                <div class="m-demand-banner" id="pawnDemand" style="display: none;">
                    <div class="m-demand-label">DEMAND</div>
                    <div class="m-demand-value" id="demandValue">...</div>
                </div>
            </div>
            <div id="pawnGrid" class="m-pawn-grid">
                <!-- Hydrate via JS -->
            </div>
        </section>

        <?php if (isAdmin($my_id)): ?>
            <div class="m-admin-intel-wrap">
                <button class="m-admin-intel-btn" onclick="openAdminGuide()">Pawnshop Guidance (Admins Only)</button>
            </div>
        <?php endif; ?>
    </main>

    <!-- SN MODAL -->
    <div class="m-sn-modal" id="snModal" onclick="if(event.target == this) closeSnPicker()">
        <div class="m-sn-content">
            <h3 style="color:var(--pawn-gold); font-family:'Outfit'; font-weight:900; margin-bottom:1rem; font-size:1rem; text-align:center;">Select Card</h3>
            <div id="snGrid"></div>
        </div>
    </div>

    <!-- ADMIN GUIDE MODAL -->
    <div id="adminGuideModal" class="m-admin-modal" onclick="if(event.target == this) closeAdminGuide()">
        <div class="m-admin-modal-content">
            <span class="m-admin-modal-close" onclick="closeAdminGuide()">✕</span>
            <h2 style="color:var(--pawn-gold); font-family:'Playfair Display'; margin-bottom:1.5rem; text-align:center; font-size:1.5rem;">Pawnshop Guidance (Admins Only)</h2>
            <div id="adminGuideContent">
                <!-- Fetch via JS -->
            </div>
        </div>
    </div>

    <script>
        const IMAGES_PATH = '<?php echo $IMAGES_PATH; ?>';
        let selectedCards = [];
        let cardCategories = {};
        let currentValuation = 0;
        let currentFragments = 0;
        let haggleCount = 0;
        let userMana = 0;
        let archiveDust = 0;
        const MAX_SELECTION = 3;
        let maxHaggles = 2;

        const NULL_LINES = {
            idle: [
                "History is a heavy currency. Choose your offerings wisely.", 
                "Show me something I haven't seen in a cycle. The Backroom is hungry.", 
                "The void is patient, but the archives are shifting.", 
                "Everything has its valuation. Even memories.",
                "The resonance of the Archive is strong today. What does it draw from you?"
            ],
            selecting: [
                "Interesting choice. It has a specific texture.", 
                "The resonance is building. Add more.", 
                "Continue... the balance is beginning to shift.",
                "A curious piece of history you've brought forward."
            ],
            ready: [
                "A fair exchange. Strike the deal if you are ready to let go.", 
                "The scales are balanced. A satisfactory sacrifice.",
                "The valuation is complete. History is ready to be written."
            ],
            success: [
                "The Archive has grown. Your power follows, as always.", 
                "A wise trade. The void accepts your offering.",
                "Transaction sealed. Your essence is replenished."
            ],
            haggle_win: [
                "Fine. Your persistence is as heavy as the cards. I'll adjust the weight.", 
                "You have a silver tongue for a mortal. A better offer.",
                "A generous shift. Don't expect another."
            ],
            haggle_loss: [
                "You test my hunger. Greed is a familiar scent. A lower offer.", 
                "The valuation has shifted... and not in your favor.",
                "The Archive's interest is waning. This is all it offers now."
            ],
            synergy: [
                "A perfect alignment. The resonance increases the valuation.",
                "These cards belong together. Harmony in history.",
                "Resonant pieces. The Backroom rewards such preservation."
            ],
            inscription: [
                "These cards carry extra weight. Someone left a mark on them.",
                "Inscriptions. History carved into the surface. A fine offering.",
                "I can feel the intent behind these messages. It increases the valuation."
            ],
            moody: [
                "I'm not in the mood for games today. The void is calling.",
                "The Archive feels distant today. My patience is thin.",
                "A heavy day in the Backroom. I'm less inclined to generosity.",
                "The resonance is off today. Don't expect me to be easy."
            ],
            sealed: ["Null is done trading for now. Return after another cycle."]
        };

        window.addEventListener('load', hydratePawnshop);

        async function hydratePawnshop() {
            const grid = document.getElementById('pawnGrid');
            try {
                const data = await secureFetch(`/api/pawnshop.php?action=get_inventory`);
                grid.innerHTML = '';
                
                if (data.success) {
                    const demand = data.demand;
                    maxHaggles = demand.max_haggles ?? 2;
                    
                    // Mood dialogue
                    if (demand.is_stingy || demand.mood_bias < -0.05) {
                        document.getElementById('nullDialogue').innerText = '"' + NULL_LINES.moody[Math.floor(Math.random()*NULL_LINES.moody.length)] + '"';
                    }

                    document.getElementById('pawnDemand').style.display = 'block';
                    document.getElementById('demandValue').innerText = demand.label;
                    userMana = data.balance;
                    archiveDust = data.archive_dust || 0;
                    updateDustUI();
                    
                    cardCategories = {};
                    data.cards.forEach(card => {
                        let meetsDemand = false;
                        if(data.demand.type === 'rarity') meetsDemand = data.demand.values.includes(card.rarity_name);
                        else if(data.demand.type === 'name') meetsDemand = data.demand.values.includes(parseInt(card.id));
                        
                        if (meetsDemand) cardCategories[card.id] = card;
                    });

                    Object.keys(cardCategories).forEach(id => {
                        const card = cardCategories[id];
                        const div = document.createElement('div');
                        div.className = 'm-vault-item';
                        div.id = `vault-${id}`;
                        div.onclick = () => openSnPicker(id);
                        div.innerHTML = `<img src="${IMAGES_PATH}${card.filename}" loading="lazy">`;
                        grid.appendChild(div);
                    });
                } else if (data.message.includes('sealed')) {
                    renderSealed();
                }
            } catch (e) {
                if (e.message.includes('sealed')) renderSealed();
                else grid.innerHTML = `<div style="grid-column:1/-1; color:#ff4e4e; text-align:center;">${e.message}</div>`;
            }
        }

        function renderSealed() {
            document.getElementById('dealerStatus').innerText = 'BACKROOM SEALED';
            document.getElementById('nullPortrait').src = '/illusionary.png';
            document.getElementById('nullDialogue').innerText = '"' + NULL_LINES.sealed[0] + '"';
            document.getElementById('tradeTable').style.opacity = '0.3';
            document.getElementById('tradeTable').style.pointerEvents = 'none';
            document.getElementById('pawnDemand').style.display = 'none';
            document.getElementById('pawnGrid').innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:2rem; color:var(--text-muted);">Come back after another cycle.</div>';
        }

        function openSnPicker(cardId) {
            const card = cardCategories[cardId];
            const modal = document.getElementById('snModal');
            const grid = document.getElementById('snGrid');
            grid.innerHTML = '';

            card.sns.forEach(inst => {
                const isSelected = selectedCards.some(s => s.id === inst.id.toString());
                const div = document.createElement('div');
                div.className = `m-sn-option ${isSelected ? 'selected' : ''}`;
                div.onclick = () => {
                    toggleInstance(inst, cardId);
                    openSnPicker(cardId);
                };
                const vClass = inst.variant ? `variant-${inst.variant}` : '';
                div.innerHTML = `<span class="m-sn-name ${vClass}">#${inst.sn}</span> <span class="m-variant-name">${inst.variant || 'Standard'}</span>`;
                grid.appendChild(div);
            });
            modal.style.display = 'flex';
        }

        function closeSnPicker() { document.getElementById('snModal').style.display = 'none'; }

        function toggleInstance(inst, cardId) {
            const idStr = inst.id.toString();
            const idx = selectedCards.findIndex(s => s.id === idStr);
            if (idx > -1) {
                selectedCards.splice(idx, 1);
                haggleCount = 0;
            } else if (selectedCards.length < MAX_SELECTION) {
                selectedCards.push({
                    id: idStr,
                    image: IMAGES_PATH + cardCategories[cardId].filename,
                    cardId: cardId,
                    variant: inst.variant || null
                });
                haggleCount = 0;
            }
            renderTable();
            updateUI();
        }

        function renderTable() {
            for (let i = 0; i < MAX_SELECTION; i++) {
                const slot = document.getElementById(`slot-${i}`);
                slot.innerHTML = '';
                if (selectedCards[i]) {
                    slot.classList.add('filled');
                    const vClass = selectedCards[i].variant ? `variant-${selectedCards[i].variant}` : '';
                    slot.innerHTML = `<button class="remove-btn" onclick="removeAt(${i})">×</button><img src="${selectedCards[i].image}" class="${vClass}" loading="lazy">`;
                } else {
                    slot.classList.remove('filled');
                }
            }
        }

        function removeAt(idx) {
            selectedCards.splice(idx, 1);
            haggleCount = 0;
            renderTable();
            updateUI();
        }

        async function updateUI() {
            const dealBtn = document.getElementById('strikeDealBtn');
            const finBtn = document.getElementById('finangleBtn');
            const valEl = document.getElementById('manaValuation');
            const box = document.getElementById('nullDialogue');

            if (selectedCards.length === MAX_SELECTION) {
                dealBtn.classList.add('active');
                valEl.style.display = 'block';
                
                if (haggleCount === 0 && currentValuation === 0) {
                    const ids = selectedCards.map(s => s.id);
                    const q = ids.map(id => `instance_ids[]=${id}`).join('&');
                    const d = await secureFetch(`/api/pawnshop.php?action=get_valuation&haggle_count=0&${q}`);
                    if (d.success) {
                        currentValuation = d.valuation;
                        currentFragments = d.fragments || 0;
                        archiveDust = d.archive_dust || 0;
                        updateDustUI();
                        maxHaggles = d.max_haggles ?? 2;
                        const list = document.getElementById('bonusList');
                        list.innerHTML = d.bonuses.map(b => `<span class="m-bonus-tag">${b}</span>`).join('');
                        
                        // Enhanced dialogue logic
                        if (d.bonuses.some(b => b.includes('Synergy'))) {
                             box.innerText = '"' + NULL_LINES.synergy[Math.floor(Math.random()*NULL_LINES.synergy.length)] + '"';
                        } else if (d.bonuses.some(b => b.includes('Inscribed'))) {
                             box.innerText = '"' + NULL_LINES.inscription[Math.floor(Math.random()*NULL_LINES.inscription.length)] + '"';
                        } else {
                             box.innerText = '"' + NULL_LINES.ready[Math.floor(Math.random()*NULL_LINES.ready.length)] + '"';
                        }
                    }
                }
                
                valEl.innerHTML = `VALUATION: ${currentValuation} <span style="font-size:0.75rem">MANA</span> ${currentFragments > 0 ? `<span class="m-frag-count">(+${currentFragments})</span>` : ''}`;
                if (haggleCount < maxHaggles) {
                    finBtn.style.display = 'block';
                    finBtn.classList.add('active');
                } else {
                    finBtn.style.display = 'none';
                }
            } else {
                dealBtn.classList.remove('active');
                finBtn.style.display = 'none';
                valEl.style.display = 'none';
                currentValuation = 0;
                box.innerText = '"' + (selectedCards.length > 0 ? NULL_LINES.selecting[Math.floor(Math.random()*NULL_LINES.selecting.length)] : NULL_LINES.idle[0]) + '"';
            }

            Object.keys(cardCategories).forEach(cid => {
                const el = document.getElementById(`vault-${cid}`);
                if (!el) return;
                const count = selectedCards.filter(s => s.cardId === cid).length;
                el.classList.toggle('selected', count > 0);
                const badge = el.querySelector('.m-item-badge');
                if (badge) badge.remove();
                if (count > 0) el.innerHTML += `<div class="m-item-badge">${count}</div>`;
            });
        }

        async function finangle() {
            if (selectedCards.length !== MAX_SELECTION || haggleCount >= maxHaggles) return;

            const ids = selectedCards.map(s => s.id);
            const q = ids.map(id => `instance_ids[]=${id}`).join('&');
            
            try {
                const d = await secureFetch(`/api/pawnshop.php?action=get_valuation&haggle_count=${haggleCount + 1}&${q}`);
                if (d.success) {
                    haggleCount++;
                    currentValuation = d.valuation;
                    currentFragments = d.fragments || 0;
                    archiveDust = d.archive_dust || 0;
                    updateDustUI();
                    maxHaggles = d.max_haggles ?? 2;
                    updateUI();
                    document.getElementById('nullDialogue').innerText = '"' + NULL_LINES.haggle_win[Math.floor(Math.random()*NULL_LINES.haggle_win.length)] + '"';
                } else {
                    Swal.fire({ title: 'Null Refuses', text: d.message, icon: 'error', background: '#0d0a08', color: '#f5e6d3' });
                }
            } catch (e) {
                console.error(e);
            }
        }

        function updateDustUI() {
            const wrap = document.getElementById('dustWrap');
            const fill = document.getElementById('dustFill');
            const val = document.getElementById('dustVal');
            
            if (wrap) wrap.style.display = 'flex';
            if (val) val.innerText = archiveDust;
            if (fill) {
                const percent = Math.min(100, (archiveDust / 1000) * 100);
                fill.style.width = percent + '%';
            }
        }

        async function strikeDeal() {
            const res = await Swal.fire({
                title: 'Strike Deal?',
                text: `Permanent trade for ${currentValuation} Mana.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d4af37',
                background: '#0d0a08',
                color: '#f5e6d3'
            });

            if (res.isConfirmed) {
                const fd = new FormData();
                fd.append('action', 'pawn_cards');
                fd.append('final_mana', currentValuation);
                fd.append('haggle_count', haggleCount);
                selectedCards.forEach(s => fd.append('instance_ids[]', s.id));
                const d = await secureFetch('/api/pawnshop.php', { method: 'POST', body: fd });
                if (d.success) {
                    await Swal.fire({ title: 'Accepted', text: d.message, icon: 'success', background: '#0d0a08', color: '#f5e6d3' });
                    location.reload();
                }
            }
        }
        async function openAdminGuide() {
            const modal = document.getElementById('adminGuideModal');
            const content = document.getElementById('adminGuideContent');
            content.innerHTML = '<div style="text-align:center; padding:1rem; opacity:0.5; font-size:0.8rem;">Accessing Archive Records...</div>';
            modal.style.display = 'flex';

            try {
                const data = await secureFetch('/api/pawnshop.php?action=get_guide');
                if (data.success) {
                    let html = '';
                    for (const key in data.guide) {
                        const sec = data.guide[key];
                        html += `
                            <div class="m-guide-section">
                                <div class="m-guide-title">${sec.title}</div>
                                <div class="m-guide-text">${sec.text}</div>
                            </div>
                        `;
                    }
                    content.innerHTML = html;
                }
            } catch (e) { console.error(e); }
        }

        function closeAdminGuide() {
            document.getElementById('adminGuideModal').style.display = 'none';
        }
    </script>
</body>
</html>
