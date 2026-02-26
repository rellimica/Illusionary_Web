<?php
/**
 * NULL'S PAWNSHOP
 * Redesigned as an immersive "Old Fashioned" Dealer shop.
 */
session_name('ILLUSIONARY_SID');
session_start();

require_once 'config.php';
$ENV_PATH = '/root/Illusionary/.env';
$IMAGES_PATH = 'images/images/';

$env = loadEnv($ENV_PATH);

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: auth.php");
    exit;
}

try {
    $host = $env['MYSQL_HOST'] ?? 'localhost';
    $db   = $env['MYSQL_DATABASE'] ?? '';
    $user = $env['MYSQL_USER'] ?? '';
    $pass = $env['MYSQL_PASSWORD'] ?? '';
    $port = $env['MYSQL_PORT'] ?? '3306';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4;port=$port";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) {
    header("Location: db_error.php");
    exit;
}

$my_id = $_SESSION['user_data']['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Null's Backroom</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;900&family=Playfair+Display:ital,wght@0,900;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="variations.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        :root {
            --pawn-void: #0d0a08;
            --pawn-gold: #d4af37;
            --pawn-wood: #2c1e14;
            --pawn-wood-light: #4a3423;
            --pawn-parchment: #f5e6d3;
            --glass-card: rgba(255, 255, 255, 0.02);
            --counter-bg: rgba(20, 15, 10, 0.9);
        }

        body {
            background: #080605;
            color: var(--pawn-parchment);
            overflow-x: hidden;
        }

        /* Ambient Library Lighting */
        .shop-glow {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 1000px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }

        .shop-layout {
            display: flex;
            flex-direction: column;
            gap: 3rem;
            margin-top: 1rem;
            padding-bottom: 5rem;
        }

        /* --- THE DEALER (NULL) --- */
        .shopkeeper-view {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        .null-portrait-frame {
            width: 220px;
            height: 220px;
            border-radius: 12px;
            overflow: hidden;
            border: 6px solid var(--pawn-gold);
            outline: 2px solid var(--pawn-wood);
            box-shadow: 0 20px 60px rgba(0,0,0,0.8), inset 0 0 30px rgba(0,0,0,0.5);
            margin-bottom: 2rem;
            background: #000;
            position: relative;
        }

        .null-portrait-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 2;
        }

        .null-portrait-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: sepia(0.3) contrast(1.1);
        }

        .null-status {
            font-family: 'Playfair Display';
            font-weight: 900;
            font-style: italic;
            color: var(--pawn-gold);
            letter-spacing: 2px;
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
            text-transform: uppercase;
        }

        .null-dialogue-box {
            background: var(--counter-bg);
            border: 1px solid var(--pawn-wood-light);
            border-radius: 4px;
            padding: 1.5rem 3rem;
            color: var(--pawn-parchment);
            font-family: 'Inter';
            font-style: italic;
            font-size: 1.1rem;
            line-height: 1.6;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
            position: relative;
        }

        .null-dialogue-box::after {
            content: '';
            position: absolute;
            inset: 5px;
            border: 1px solid rgba(212, 175, 55, 0.1);
            pointer-events: none;
        }

        /* --- THE ANTIQUE COUNTER --- */
        .trade-counter {
            background: linear-gradient(180deg, #1a1410 0%, #0d0a08 100%);
            border: 2px solid var(--pawn-wood-light);
            border-top: 4px solid var(--pawn-gold);
            border-radius: 8px 8px 0 0;
            padding: 3rem;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            position: relative;
            box-shadow: 0 -30px 60px rgba(0,0,0,0.5);
        }

        .counter-label {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--pawn-gold);
            color: #000;
            font-family: 'Outfit';
            font-weight: 900;
            padding: 6px 30px;
            border-radius: 4px;
            font-size: 0.8rem;
            letter-spacing: 3px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }

        .counter-slots {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3rem;
            justify-items: center;
            margin-bottom: 3rem;
        }

        .counter-slot {
            width: 200px;
            aspect-ratio: 2/3;
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.2);
            position: relative;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.5);
        }

        .counter-slot.filled {
            box-shadow: 0 15px 35px rgba(0,0,0,0.6);
            border-color: var(--pawn-gold);
        }

        .counter-slot .slot-placeholder {
            font-family: 'Playfair Display';
            font-weight: 900;
            font-size: 3rem;
            color: rgba(212, 175, 55, 0.05);
        }

        .counter-card {
            width: 100%;
            height: 100%;
            border-radius: 4px;
            overflow: hidden;
            animation: card-place 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
        }

        @keyframes card-place {
            0% { transform: scale(1.2) translateY(-20px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        .counter-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .counter-card .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            background: var(--pawn-gold);
            color: #000;
            border: none;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            font-size: 1rem;
            font-weight: 900;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .counter-slot:hover .remove-btn { opacity: 1; }

        .trade-controls {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .mana-estimate {
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 1.3rem;
            color: var(--pawn-gold);
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
            display: none;
            letter-spacing: 1px;
        }

        .strike-btn {
            padding: 1.2rem 5rem;
            background: var(--pawn-gold);
            border: 2px solid var(--pawn-wood);
            border-radius: 4px;
            color: #000;
            font-family: 'Outfit';
            font-weight: 900;
            font-size: 1.4rem;
            text-transform: uppercase;
            letter-spacing: 4px;
            cursor: pointer;
            transition: all 0.3s;
            opacity: 0.2;
            pointer-events: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
        }

        .strike-btn.active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .strike-btn.active:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.3);
        }

        /* --- THE ARCHIVE (VAULT) --- */
        .vault-section {
            background: #050403;
            border-top: 1px solid var(--pawn-wood-light);
            padding: 4rem 0;
            position: relative;
        }

        .vault-header {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 3rem;
            padding: 0 2rem;
        }

        .vault-title {
            font-family: 'Playfair Display';
            font-weight: 900;
            font-size: 2.5rem;
            color: var(--pawn-gold);
            letter-spacing: 1px;
        }

        .demand-banner {
            background: rgba(212, 175, 55, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-top: 3px solid var(--pawn-gold);
            padding: 1rem 2rem;
            border-radius: 2px;
            position: relative;
        }

        .demand-banner::before {
            content: '♦';
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pawn-gold);
            font-size: 0.6rem;
        }

        .demand-label {
            font-size: 0.65rem;
            color: var(--pawn-gold);
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .demand-value {
            color: var(--pawn-parchment);
            font-weight: 800;
            font-family: 'Outfit';
            font-size: 1.1rem;
            text-transform: uppercase;
        }

        .pawn-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .vault-item {
            aspect-ratio: 2/3;
            border-radius: 4px;
            background: #000;
            border: 1px solid var(--pawn-wood-light);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .vault-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .vault-item:hover {
            transform: translateY(-8px);
            border-color: var(--pawn-gold);
            box-shadow: 0 15px 30px rgba(0,0,0,0.5);
        }

        .vault-item:hover img { opacity: 1; }

        .vault-item.has-selected {
            border-color: var(--pawn-gold);
            outline: 2px solid var(--pawn-gold);
            outline-offset: -6px;
        }

        .vault-item .badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--pawn-gold);
            color: #000;
            width: 24px;
            height: 24px;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 900;
            z-index: 5;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .vault-item .qty {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 8px;
            background: rgba(0,0,0,0.8);
            color: var(--pawn-gold);
            font-size: 0.6rem;
            font-weight: 800;
            text-align: center;
            letter-spacing: 1px;
            border-top: 1px solid var(--pawn-wood-light);
        }

        /* --- MODAL --- */
        .sn-picker-modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.95);
            backdrop-filter: blur(10px);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .sn-picker-content {
            background: #0d0a08;
            border: 2px solid var(--pawn-gold);
            border-radius: 4px;
            max-width: 500px;
            width: 100%;
            padding: 3rem;
            position: relative;
            box-shadow: 0 40px 100px rgba(0,0,0,1);
        }

        .sn-option {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--pawn-wood-light);
            padding: 15px 20px;
            border-radius: 2px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 0.8rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sn-option:hover { background: rgba(212, 175, 55, 0.05); border-color: var(--pawn-gold); }
        .sn-option.selected { border-color: var(--pawn-gold); background: rgba(212, 175, 55, 0.1); color: var(--pawn-gold); }
        .sn-option .sn-val { font-family: 'Playfair Display'; font-weight: 900; font-size: 1.2rem; }

        @media (max-width: 768px) {
            .counter-slots { grid-template-columns: 1fr; gap: 1.5rem; }
            .counter-slot { width: 160px; }
        }
    </style>
</head>
<body>
    <div class="shop-glow"></div>
    
    <?php 
    $nav_subtitle = "Null's Pawnshop";
    include 'nav.php'; 
    ?>

    <div class="shop-layout">
        
        <!-- THE DEALER -->
        <section class="shopkeeper-view">
            <div class="null-portrait-frame">
                <img src="happynull.png" alt="Null">
            </div>
            <div class="null-status">DEALER AWATING</div>
            <div class="null-dialogue-box" id="nullDialogue">
                "Welcome to the Archive... Seek you a trade of history for power?"
            </div>
        </section>

        <!-- THE COUNTER -->
        <section class="trade-counter">
            <div class="counter-label">EXCHANGE TABLE</div>
            
            <div class="counter-slots">
                <div class="counter-slot" id="slot-0"><span class="slot-placeholder">I</span></div>
                <div class="counter-slot" id="slot-1"><span class="slot-placeholder">II</span></div>
                <div class="counter-slot" id="slot-2"><span class="slot-placeholder">III</span></div>
            </div>

            <div class="trade-controls">
                <div class="mana-estimate" id="manaEstimate">VALUATION: 0 MANA</div>
                <div style="display: flex; gap: 1rem;">
                    <button class="strike-btn" style="padding: 1.2rem 3rem; background: transparent; border-color: rgba(212, 175, 55, 0.3); color: var(--pawn-gold); opacity: 0; pointer-events: none;" id="finangleBtn" onclick="finangle()">Finangle</button>
                    <button class="strike-btn" id="strikeDealBtn" onclick="strikeDeal()">Strike the Deal</button>
                </div>
            </div>
        </section>

        <!-- THE ARCHIVE -->
        <section class="vault-section">
            <div class="vault-header">
                <div>
                    <h2 class="vault-title">The Backroom</h2>
                    <p style="color: var(--pawn-gold); font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; font-weight: 800; opacity: 0.6;">Select cards to place them on the dealer's table.</p>
                </div>
                
                <div class="demand-banner" id="pawnDemand" style="display: none;">
                    <div class="demand-label">TODAY'S ACQUISITION</div>
                    <div class="demand-value" id="demandValue">...</div>
                </div>
            </div>

            <div id="pawnCardGrid" class="pawn-grid">
                <!-- Hydrated via JS -->
            </div>
        </section>
    </div>

    <!-- SN Modal -->
    <div id="snPickerModal" class="sn-picker-modal" onclick="if(event.target == this) closeSnPicker()">
        <div class="sn-picker-content">
            <h3 id="pickerCardName" style="color:var(--pawn-gold); font-family:'Playfair Display'; margin-bottom:1.5rem; text-align:center; font-size:1.8rem;">Select Fragment</h3>
            <div id="snPickerGrid"></div>
        </div>
    </div>

    <script>
        const IMAGES_PATH = '<?php echo $IMAGES_PATH; ?>';
        let selectedCards = []; 
        let cardCategories = {};
        let currentValuation = 0;
        let haggleCount = 0;
        const MAX_SELECTION = 3;
        const MAX_HAGGLES = 2;

        const RARITY_VALUES = <?php echo json_encode($CARD_RARITY_VALUES); ?>;

        const NULL_LINES = {
            idle: [
                "Cards are a curious currency. I help move the weight.",
                "Looking to trade some history for power? I'm listening.",
                "The Backroom is always open for the right price.",
                "Patience is a virtue in this business, but some cards don't stay long.",
                "I track the flow of cards. Right now, the tides are shifting.",
                "Everything has a valuation. Even the things you consider junk.",
                "The candles are burning low. Bring your offerings to the light."
            ],
            selecting: [
                "A curious piece. It has a specific... texture.",
                "One. The Backroom requires balance. Continue.",
                "Two cards. The valuation is beginning to take shape.",
                "I've seen similar relics back in the early cycles. This one is better preserved.",
                "Almost there. One more to seal the weight of the trade.",
                "Interesting... but is it enough to tempt the void?"
            ],
            ready: [
                "A fair valuation. Are you prepared to part with them permanently?",
                "The scales are balanced. Strike the deal when you are ready to let go.",
                "Three pieces of history. The Backroom accepts this offering.",
                "A substantial trade. The Mana yield will be... satisfactory.",
                "The valuation is complete. Let it be written into the ledger."
            ],
            success: [
                "The transaction is sealed. These cards belong to the Backroom now.",
                "Mana for memory. A classic trade, and a wise one.",
                "You'll forget you ever owned them. The Backroom never forgets.",
                "History has been consumed. Your power grows.",
                "A pleasure doing business. Come back when you find more... curiosities."
            ],
            refusal: [
                "My hunger is specific today. This does not satisfy the current cycle.",
                "The Archive has no place for this fragment. Not today.",
                "I strictly acquisition only according to the current demand.",
                "A noble effort, but irrelevant to my current interests."
            ],
            haggle_win: [
                "Fine... your persistence is as heavy as the relics. A better offer.",
                "You have a silver tongue for a mortal. I'll adjust the weight.",
                "A generous shift. Don't expect another."
            ],
            haggle_loss: [
                "You test my patience. The Archive's interest is waning. A lower offer.",
                "The value of history is subjective. I've decided these are worth less now.",
                "Greed is a familiar scent. I've adjusted the valuation accordingly."
            ]
        };

        window.addEventListener('load', hydratePawnshop);

        async function hydratePawnshop() {
            try {
                const data = await secureFetch(`api/pawnshop.php?action=get_inventory`);
                const grid = document.getElementById('pawnCardGrid');
                grid.innerHTML = '';
                
                if (!data.success) return;

                const demand = data.demand;
                const demandValueEl = document.getElementById('demandValue');
                if (demandValueEl) {
                    demandValueEl.innerText = demand.label;
                    document.getElementById('pawnDemand').style.display = 'block';
                }

                cardCategories = {};
                data.cards.forEach(card => {
                    let meetsDemand = false;
                    if (demand.type === 'rarity') {
                        meetsDemand = demand.values.includes(card.rarity_name);
                    } else if (demand.type === 'name') {
                        meetsDemand = demand.values.includes(parseInt(card.id));
                    }
                    
                    if (meetsDemand) cardCategories[card.id] = card;
                });

                Object.keys(cardCategories).forEach(id => {
                    const card = cardCategories[id];
                    const div = document.createElement('div');
                    div.className = `vault-item rarity-${card.rarity_name.toLowerCase()}`;
                    div.id = `vault-${id}`;
                    div.onclick = () => openSnPicker(id);

                    div.innerHTML = `
                        <img src="${IMAGES_PATH}${card.filename}">
                        <div class="qty">${card.sns.length} CARDS</div>
                    `;
                    grid.appendChild(div);
                });

            } catch (e) { console.error(e); }
        }

        function openSnPicker(cardId) {
            const card = cardCategories[cardId];
            const modal = document.getElementById('snPickerModal');
            const grid = document.getElementById('snPickerGrid');
            document.getElementById('pickerCardName').innerText = card.name.replace(/_/g, ' ');
            grid.innerHTML = '';

            card.sns.forEach(inst => {
                const isSelected = selectedCards.some(s => s.id === inst.id.toString());
                const div = document.createElement('div');
                div.className = `sn-option ${isSelected ? 'selected' : ''}`;
                if (selectedCards.length >= MAX_SELECTION && !isSelected) div.style.opacity = '0.3';
                
                div.onclick = () => toggleInstance(inst, cardId);
                div.innerHTML = `<span class="sn-val">#${inst.sn}</span> <span style="font-size:0.7rem; color:var(--pawn-gold); opacity:0.6;">${inst.variant || 'Standard'}</span>`;
                grid.appendChild(div);
            });
            modal.style.display = 'flex';
        }

        function closeSnPicker() { document.getElementById('snPickerModal').style.display = 'none'; }

        function toggleInstance(inst, cardId) {
            const instIdStr = inst.id.toString();
            const index = selectedCards.findIndex(s => s.id === instIdStr);

            if (index > -1) {
                selectedCards.splice(index, 1);
                haggleCount = 0; // Reset haggling on change
            } else if (selectedCards.length < MAX_SELECTION) {
                selectedCards.push({
                    id: instIdStr,
                    sn: inst.sn,
                    image: IMAGES_PATH + cardCategories[cardId].filename,
                    cardId: cardId,
                    rarity: cardCategories[cardId].rarity_name.toLowerCase()
                });
                haggleCount = 0; // Reset haggling on change
            }
            
            openSnPicker(cardId);
            renderCounter();
            updateUI();
        }

        function renderCounter() {
            for (let i = 0; i < MAX_SELECTION; i++) {
                const slot = document.getElementById(`slot-${i}`);
                slot.innerHTML = '';
                slot.classList.remove('filled');

                if (selectedCards[i]) {
                    slot.classList.add('filled');
                    slot.innerHTML = `
                        <div class="counter-card">
                            <button class="remove-btn" onclick="removeAt(${i})">&times;</button>
                            <img src="${selectedCards[i].image}">
                        </div>
                    `;
                } else {
                    slot.innerHTML = `<span class="slot-placeholder">${i === 0 ? 'I' : i === 1 ? 'II' : 'III'}</span>`;
                }
            }
        }

        function removeAt(index) {
            selectedCards.splice(index, 1);
            haggleCount = 0;
            renderCounter();
            updateUI();
        }

        async function updateUI() {
            const btn = document.getElementById('strikeDealBtn');
            const hButton = document.getElementById('finangleBtn');
            const estimate = document.getElementById('manaEstimate');
            const status = document.querySelector('.null-status');

            if (selectedCards.length === MAX_SELECTION) {
                btn.classList.add('active');
                estimate.style.display = 'block';
                status.innerText = 'VALUATION COMPLETE';
                
                if (haggleCount === 0 && currentValuation === 0) {
                    // Initial Valuation from API (Deterministic)
                    const ids = selectedCards.map(s => s.id);
                    const query = ids.map(id => `instance_ids[]=${id}`).join('&');
                    try {
                        const data = await secureFetch(`api/pawnshop.php?action=get_valuation&haggle_count=0&${query}`);
                        if (data.success) {
                            currentValuation = data.valuation;
                            updateDialogue('ready', currentValuation);
                        }
                    } catch (e) { console.error(e); }
                }
                
                estimate.innerText = `VALUATION: ${currentValuation} MANA`;
                
                if (haggleCount < MAX_HAGGLES) {
                    hButton.style.opacity = '1';
                    hButton.style.pointerEvents = 'auto';
                    hButton.innerText = `Finangle (${MAX_HAGGLES - haggleCount})`;
                } else {
                    hButton.style.opacity = '0';
                    hButton.style.pointerEvents = 'none';
                }
            } else {
                btn.classList.remove('active');
                hButton.style.opacity = '0';
                hButton.style.pointerEvents = 'none';
                estimate.style.display = 'none';
                currentValuation = 0; // Reset
                status.innerText = selectedCards.length > 0 ? 'ACQUIRING Cards' : 'DEALER AWAITING';
                if (selectedCards.length > 0) updateDialogue('selecting');
            }

            Object.keys(cardCategories).forEach(catId => {
                const vaultEl = document.getElementById(`vault-${catId}`);
                if (!vaultEl) return;
                const count = selectedCards.filter(s => s.cardId === catId).length;
                const existing = vaultEl.querySelector('.badge');
                if (existing) existing.remove();
                if (count > 0) vaultEl.innerHTML += `<div class="badge">${count}</div>`;
                vaultEl.classList.toggle('has-selected', count > 0);
            });
        }

        async function finangle() {
            if (selectedCards.length !== MAX_SELECTION || haggleCount >= MAX_HAGGLES) return;

            const ids = selectedCards.map(s => s.id);
            const query = ids.map(id => `instance_ids[]=${id}`).join('&');
            
            try {
                // Pass haggleCount + 1 to the API for the next tier of seeding
                const data = await secureFetch(`api/pawnshop.php?action=get_valuation&haggle_count=${haggleCount + 1}&${query}`);
                if (data.success) {
                    haggleCount++;
                    const oldVal = currentValuation;
                    currentValuation = data.valuation;
                    
                    const type = currentValuation >= oldVal ? 'haggle_win' : 'haggle_loss';
                    updateDialogue(type, currentValuation);
                    updateUI();

                    // Flash effect on mana estimate
                    const est = document.getElementById('manaEstimate');
                    est.style.color = currentValuation >= oldVal ? '#4caf50' : '#f44336';
                    setTimeout(() => est.style.color = 'var(--pawn-gold)', 1000);
                }
            } catch (e) { console.error(e); }
        }

        function updateDialogue(type, value = null) {
            const box = document.getElementById('nullDialogue');
            let lines = [...NULL_LINES[type]];
            
            if (type === 'ready' && value !== null) {
                lines.push(`Best I can offer you is ${value} Mana..`);
                lines.push(`My valuation stands... ${value} Mana, and not a fragment more.`);
            }
            
            if ((type === 'haggle_win' || type === 'haggle_loss') && value !== null) {
                lines = lines.map(l => l + ` (${value} Mana)`);
            }

            const line = lines[Math.floor(Math.random() * lines.length)];
            box.innerText = `"${line}"`;
        }

        async function strikeDeal() {
            if (selectedCards.length !== MAX_SELECTION) return;

            const res = await Swal.fire({
                title: 'Strike the Deal?',
                text: `Null will take these forever. You will receive ${currentValuation} Mana.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--pawn-gold)',
                confirmButtonText: 'Let it be so',
                cancelButtonText: 'Wait',
                background: '#0d0a08',
                color: '#f5e6d3'
            });

            if (res.isConfirmed) {
                const fd = new FormData();
                fd.append('action', 'pawn_cards');
                fd.append('final_mana', currentValuation);
                selectedCards.forEach(s => fd.append('instance_ids[]', s.id));

                try {
                    const data = await secureFetch('api/pawnshop.php', { method: 'POST', body: fd });
                    if (data.success) {
                        Swal.fire({ title: 'Deal Struck!', text: data.message, icon: 'success', background: '#0d0a08', color: '#f5e6d3' });
                        selectedCards = [];
                        haggleCount = 0;
                        renderCounter();
                        updateUI();
                        updateDialogue('success');
                    } else {
                        Swal.fire({ title: 'Refusal', text: data.message, icon: 'error', background: '#0d0a08', color: '#f5e6d3' });
                    }
                } catch (e) { console.error(e); }
            }
        }
    </script>
</body>
</html>
