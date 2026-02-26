<style>
    /* Premium Tour System */
    .tutorial-modal {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: transparent;
        z-index: 999990;
        display: flex;
        pointer-events: none;
        transition: all 0.3s;
    }
    
    .tutorial-content {
        position: fixed;
        background: linear-gradient(135deg, rgba(25, 25, 40, 0.98), rgba(10, 10, 15, 1));
        border: 2px solid var(--accent-secondary);
        border-radius: 16px;
        width: 380px;
        padding: 30px;
        pointer-events: auto;
        box-shadow: 0 20px 80px rgba(0,0,0,0.9), 0 0 0 2px var(--accent-secondary);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 1000000;
    }

    .tutorial-content::after {
        content: '';
        position: absolute;
        width: 0; height: 0;
        border: 12px solid transparent;
        transition: all 0.3s;
        display: none;
    }

    .tutorial-content.point-left::after { display: block; border-right-color: var(--accent-secondary); right: 100%; top: 50%; transform: translateY(-50%); }
    .tutorial-content.point-top::after { display: block; border-bottom-color: var(--accent-secondary); bottom: 100%; left: 50%; transform: translateX(-50%); }
    .tutorial-content.point-right::after { display: block; border-left-color: var(--accent-secondary); left: 100%; top: 50%; transform: translateY(-50%); }
    .tutorial-content.point-bottom::after { display: block; border-top-color: var(--accent-secondary); top: 100%; left: 50%; transform: translateX(-50%); }

    .tutorial-step { display: none; margin-bottom: 20px; }
    .tutorial-step.active { display: block; animation: fadeInTutorial 0.4s ease-out; }
    @keyframes fadeInTutorial { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .tutorial-title { font-size: 1.4rem; font-weight: 900; font-family: 'Outfit'; margin-bottom: 12px; color: var(--accent-secondary); text-transform: uppercase; letter-spacing: 2px; display: flex; align-items: center; gap: 10px; }
    .tutorial-title i { font-style: normal; font-size: 0.8rem; background: var(--accent-secondary); color: #000; padding: 2px 8px; border-radius: 4px; }
    .tutorial-text { font-size: 0.95rem; color: #eee; line-height: 1.7; margin-bottom: 25px; font-family: 'Inter'; }
    .tutorial-nav { display: flex; justify-content: space-between; align-items: center; gap: 10px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; }

    .tour-spotlight {
        position: relative !important;
        z-index: 999991 !important;
        box-shadow: 0 10px 40px rgba(0, 229, 255, 0.4), 0 0 0 10px rgba(0, 229, 255, 0.1) !important;
        border: 2px solid var(--accent-secondary) !important;
        background: rgba(0, 229, 255, 0.05) !important;
        pointer-events: none !important;
    }
    
    .tour-pulse-btn {
         animation: tourPulse 2s infinite;
    }
    @keyframes tourPulse {
        0% { box-shadow: 0 0 0 0 rgba(0, 229, 255, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(0, 229, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 229, 255, 0); }
    }
</style>

<!-- Tutorial Component -->
<div id="tradeTutorialOverlay" class="tutorial-modal" style="display: none;">
    <div id="tradeTutorialBox" class="tutorial-content">
        <!-- STEP 1: WELCOME -->
        <div class="tutorial-step" data-step="1">
            <div class="tutorial-title">The Exchange <i>BETA</i></div>
            <div class="tutorial-text">
                Welcome to the <strong>Illusionary Trading Center</strong>. This is where collectors negotiate deals to complete their sets. <br><br> Let's dive into the details.
            </div>
            <div class="tutorial-nav">
                <span></span>
                <button class="claim-btn tour-pulse-btn" onclick="nextTradeStep(2)">GET STARTED</button>
            </div>
        </div>

        <!-- STEP 2: SIDEBAR -->
        <div class="tutorial-step" data-step="2">
            <div class="tutorial-title">The Hub</div>
            <div class="tutorial-text">
                The <strong>Partner Hub</strong> is your control center. Search for collectors, track <strong>Active Requests</strong>, or explore the <strong>Collector Pool</strong>—which now uses smart matchmaking to suggest the best partners for your collection.
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(1)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(3)">NEXT</button>
            </div>
        </div>

        <!-- STEP 3: MISSING CARD MATCH -->
        <div class="tutorial-step" data-step="3">
            <div class="tutorial-title">🎯 Missing Match</div>
            <div class="tutorial-text">
                The smart matchmaking filters for <strong>Missing Card Matches</strong>. These are collectors who own tradeable cards that are currently missing from your vault. They're your primary targets for set completion!
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(2)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(4)">NEXT</button>
            </div>
        </div>

        <!-- STEP 4: MARKET ACTIVE -->
        <div class="tutorial-step" data-step="4">
            <div class="tutorial-title">⚡ Market Active</div>
            <div class="tutorial-text">
                The <strong>Market Active</strong> tag identifies traders who have been active in the Exchange within the last 7 days. You're much more likely to get a fast response from these active collectors!
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(3)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(5)">NEXT</button>
            </div>
        </div>

        <!-- STEP 5: TOP COLLECTOR -->
        <div class="tutorial-step" data-step="5">
            <div class="tutorial-title">🏆 Top Collector</div>
            <div class="tutorial-text">
                <strong>Top Collectors</strong> are the titans of the scene. They hold massive inventories and often have rare duplicates. If you can't find a direct match, these <strong>Titans</strong> are your best bet for resource depth.
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(4)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(6)">NEXT</button>
            </div>
        </div>

        <!-- STEP 6: SELECTING CARDS -->
        <div class="tutorial-step" data-step="6">
            <div class="tutorial-title" id="tutStep6Title">Selecting Cards</div>
            <div class="tutorial-text" id="tutStep6Text">
                You'll need to select cards from <strong>Both Sides</strong>. Click cards in your vault to offer them, and click cards in the partner's vault (on the right) that you wish to receive!
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(5)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(7)">NEXT</button>
            </div>
        </div>

        <!-- STEP 7: SERIAL NUMBERS -->
        <div class="tutorial-step" data-step="7">
            <div class="tutorial-title" id="tutStep7Title">Serial Numbers</div>
            <div class="tutorial-text" id="tutStep7Text">
                When a card is 🔒 Locked, you can click the <strong>SN Pills</strong> to add them to the trade. You can select multiple copies of the same card if you own them!
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(6)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(8)">NEXT</button>
            </div>
        </div>

        <!-- STEP 8: THE STAGE -->
        <div class="tutorial-step" data-step="8">
            <div class="tutorial-title" id="tutStep8Title">The Desk</div>
            <div class="tutorial-text" id="tutStep8Text">
                Your deal is summarized here on the <strong>Negotiation Stage</strong>. It shows exactly what you are offering versus what you are acquiring.
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(7)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(9)">NEXT</button>
            </div>
        </div>

        <!-- STEP 9: VERIFY & PROPOSE -->
        <div class="tutorial-step" data-step="9">
            <div class="tutorial-title" id="tutStep9Title">Finalize deal</div>
            <div class="tutorial-text" id="tutStep9Text">
                Complete the <strong>Identity Verification</strong>, then hit <strong>Initiate Proposal</strong> to send your offer. The other collector will receive a notification in their bell icon immediately!
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(8)">BACK</button>
                <button class="claim-btn" onclick="nextTradeStep(10)">NEXT</button>
            </div>
        </div>

        <!-- STEP 10: QUESTIONS -->
        <div class="tutorial-step" data-step="10">
            <div class="tutorial-title">Have Questions?</div>
            <div class="tutorial-text">
                Need more help? You can always reach out to the community or report any issues you find. <br><br>
                <div style="display: flex; flex-direction: column; gap: 10px; background: rgba(0, 229, 255, 0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(0, 229, 255, 0.1);">
                    <a href="javascript:void(0)" onclick="reportIssue()" style="color: #ff4e4e; text-decoration: none; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 1.2rem;">⚠</span> REPORT A SYSTEM BUG
                    </a>
                </div>
            </div>
            <div class="tutorial-nav">
                <button class="claim-btn" style="background:rgba(255,255,255,0.05); color:#fff; font-size:0.7rem;" onclick="nextTradeStep(9)">BACK</button>
                <button class="claim-btn" onclick="closeTradeTour()">DONE</button>
            </div>
        </div>
    </div>
</div>

<script>
    function startTradeTour(step = 1) {
        document.getElementById('tradeTutorialOverlay').style.display = 'flex';
        const container = document.querySelector('.trade-container');
        if (container) {
            container.style.position = 'relative';
            container.style.zIndex = '999980';
        }
        nextTradeStep(step);
    }

    function closeTradeTour() {
        document.getElementById('tradeTutorialOverlay').style.display = 'none';
        localStorage.setItem('trade_tutorial_seen', 'true');
        document.querySelectorAll('.tour-spotlight').forEach(el => el.classList.remove('tour-spotlight'));
        const container = document.querySelector('.trade-container');
        if (container) {
            container.style.zIndex = '';
            container.style.position = '';
        }
    }

    function nextTradeStep(n) {
        // Reset steps
        document.querySelectorAll('.tutorial-step').forEach(s => s.classList.remove('active'));
        const next = document.querySelector(`.tutorial-step[data-step="${n}"]`);
        if (next) next.classList.add('active');

        const box = document.getElementById('tradeTutorialBox');
        document.querySelectorAll('.tour-spotlight').forEach(el => el.classList.remove('tour-spotlight'));
        box.className = 'tutorial-content';

        let target = null;
        let pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };

        // Check if we are in Desk View or Hub View
        const urlParams = new URLSearchParams(window.location.search);
        const hasPartner = !!urlParams.get('partner');

        if (n === 2) {
            target = document.querySelector('.trade-hub');
            if (target) {
                const rect = target.getBoundingClientRect();
                pos = { top: '20%', left: (rect.right + 30) + 'px', transform: 'none' };
                box.classList.add('point-left');
                target.classList.add('tour-spotlight');
            }
        } else if (n >= 3 && n <= 5) {
            // Smart Matchmaking Categories - Point out specific examples
            const categories = { 
                3: 'Missing Card Match', 
                4: 'Market Active', 
                5: 'Top Collector' 
            };
            const catName = categories[n];
            
            // Try to find a card with this category
            target = document.querySelector(`[data-category="${catName}"]`);
            
            if (target) {
                const rect = target.getBoundingClientRect();
                // If it's in the sidebar (hub-item)
                if (target.classList.contains('hub-item')) {
                    pos = { top: (rect.top - 20) + 'px', left: (rect.right + 30) + 'px', transform: 'none' };
                    box.classList.add('point-left');
                } else {
                    // Main dashboard (stat-card)
                    pos = { top: (rect.top - 20) + 'px', left: (rect.right + 30) + 'px', transform: 'none' };
                    box.classList.add('point-left');
                }
                target.classList.add('tour-spotlight');
            } else {
                // Fallback to the whole list if specific match isn't present
                target = document.getElementById('activeCollectorsList') || document.getElementById('hubCollectorsList');
                pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
                if (target) target.classList.add('tour-spotlight');
            }
        } else if (n === 6) {
            target = document.querySelector('.trading-desk');
            if (target) {
                pos = { top: '150px', left: '50%', transform: 'translateX(-50%)' };
                target.classList.add('tour-spotlight');
                document.getElementById('tutStep6Title').innerText = "The Vaults";
                document.getElementById('tutStep6Text').innerHTML = "Select cards from <strong>Both Sides</strong>. Click any card in your inventory to offer it, then scroll through the <strong>Partner's Vault</strong> on the right to pick the cards you want in return!";
            } else {
                target = document.getElementById('activeCollectorsList') || document.getElementById('hubCollectorsList');
                document.getElementById('tutStep6Title').innerText = "Initiating a Deal";
                document.getElementById('tutStep6Text').innerHTML = "Once you've found a partner who has what you need, simply click their profile to open their personal Vault and start negotiating.";
                pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
                if (target) target.classList.add('tour-spotlight');
            }
        } else if (n === 7) {
            target = document.getElementById('mineVaultIntel');
            if (target) {
                const rect = target.getBoundingClientRect();
                pos = { top: (rect.bottom + 30) + 'px', left: (rect.left + rect.width / 2 - 180) + 'px', transform: 'none' };
                box.classList.add('point-top');
                target.classList.add('tour-spotlight');
                document.getElementById('tutStep7Title').innerText = "Vault Intel";
                document.getElementById('tutStep7Text').innerHTML = "When a card is 🔒 <strong>Locked</strong>, you can select specific Serial Numbers here. Clicking a pill adds that exact copy to the trade stage in the center.";
            } else {
                document.getElementById('tutStep7Title').innerText = "Trading Logic";
                document.getElementById('tutStep7Text').innerHTML = "Each card instance has a unique Serial Number (SN). Low numbers or special sequences are highly sought after in the market!";
                pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
            }
        } else if (n === 8) {
            target = document.querySelector('.negotiation-stage');
            if (target) {
                const rect = target.getBoundingClientRect();
                pos = { top: '25%', left: (rect.left - 380) + 'px', transform: 'none' };
                box.classList.add('point-right');
                target.classList.add('tour-spotlight');
            } else {
                pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
            }
        } else if (n === 9) {
            target = document.querySelector('.negotiation-stage'); // Use the same area for verification
            if (target) {
                 const rect = target.getBoundingClientRect();
                 pos = { top: (rect.bottom - 450) + 'px', left: (rect.left - 380) + 'px', transform: 'none' };
                 box.classList.add('point-right');
                 target.classList.add('tour-spotlight');
            } else {
                 pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
            }
        } else if (n === 10) {
            // Questions step - center screen
            pos = { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
        }

        Object.assign(box.style, pos);
    }

    // Auto-start for new users
    window.addEventListener('load', () => {
        if (!localStorage.getItem('trade_tutorial_seen')) {
            setTimeout(startTradeTour, 2000);
        }
    });
</script>
