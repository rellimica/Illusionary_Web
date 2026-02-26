<?php
/**
 * Discord ID Tutorial Component
 * Provides a step-by-step guide for users to find their Discord User ID.
 */
?>
<style>
    /* Discord Tutorial Overlay */
    .discord-tutorial-modal {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(8px);
        z-index: 1000100;
        display: none;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
    }
    
    .discord-tutorial-content {
        background: linear-gradient(135deg, #2c2f33, #23272a);
        border: 2px solid #5865F2; /* Discord Purple */
        border-radius: 20px;
        width: 450px;
        padding: 40px;
        box-shadow: 0 30px 100px rgba(0,0,0,0.8), 0 0 20px rgba(88, 101, 242, 0.3);
        position: relative;
        animation: discordModalIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes discordModalIn {
        from { opacity: 0; transform: scale(0.9) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .discord-step { display: none; }
    .discord-step.active { display: block; }

    .discord-title {
        font-family: 'Outfit';
        font-weight: 900;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .discord-title .icon {
        width: 32px;
        height: 32px;
        background: #5865F2;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .discord-text {
        font-family: 'Inter';
        font-size: 1rem;
        color: #dcddde;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .discord-text strong {
        color: #fff;
        font-weight: 700;
    }

    .discord-visual {
        background: rgba(0,0,0,0.3);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid rgba(255,255,255,0.05);
        text-align: center;
    }

    .discord-visual img {
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .discord-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .discord-btn {
        padding: 12px 25px;
        border-radius: 10px;
        font-family: 'Outfit';
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
    }

    .discord-btn-primary {
        background: #5865F2;
        color: #fff;
    }

    .discord-btn-primary:hover {
        background: #4752c4;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(88, 101, 242, 0.4);
    }

    .discord-btn-secondary {
        background: rgba(255,255,255,0.05);
        color: #dcddde;
    }

    .discord-btn-secondary:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }

    .close-discord-tut {
        position: absolute;
        top: 20px;
        right: 20px;
        color: #72767d;
        cursor: pointer;
        font-size: 1.5rem;
        transition: color 0.2s;
    }

    .close-discord-tut:hover {
        color: #fff;
    }

    .dev-mode-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #36393f;
        padding: 10px 15px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .toggle-switch {
        width: 40px;
        height: 20px;
        background: #43b581;
        border-radius: 20px;
        position: relative;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        right: 2px;
        top: 2px;
        width: 16px;
        height: 16px;
        background: #fff;
        border-radius: 50%;
    }
</style>

<div id="discordIdTutorial" class="discord-tutorial-modal" onclick="if(event.target === this) closeDiscordIdTour()">
    <div class="discord-tutorial-content">
        <span class="close-discord-tut" onclick="closeDiscordIdTour()">&times;</span>
        
        <!-- STEP 1: Intro -->
        <div class="discord-step" data-discord-step="1">
            <div class="discord-title">
                <div class="icon">🆔</div>
                Identify Yourself
            </div>
            <div class="discord-text">
                To trade with other collectors, you often need their <strong>Discord User ID</strong>. This is a unique 18-digit number that never changes, unlike a username.
            </div>
            <div class="discord-nav">
                <span></span>
                <button class="discord-btn discord-btn-primary" onclick="nextDiscordStep(2)">Show Me How</button>
            </div>
        </div>

        <!-- STEP 2: Developer Mode -->
        <div class="discord-step" data-discord-step="2">
            <div class="discord-title">
                <div class="icon">⚙️</div>
                Step 1: Developer Mode
            </div>
            <div class="discord-text">
                Discord hides IDs by default. To see them, go to your <strong>User Settings</strong>, select <strong>Advanced</strong> and toggle <strong>Developer Mode</strong> to <strong>ON</strong>.
            </div>
            <div class="discord-visual">
                <div class="dev-mode-toggle">
                    <span style="font-size: 0.8rem; font-weight: 600;">Developer Mode</span>
                    <div class="toggle-switch"></div>
                </div>
            </div>
            <div class="discord-nav">
                <button class="discord-btn discord-btn-secondary" onclick="nextDiscordStep(1)">Back</button>
                <button class="discord-btn discord-btn-primary" onclick="nextDiscordStep(3)">Next</button>
            </div>
        </div>

        <!-- STEP 3: Copy ID -->
        <div class="discord-step" data-discord-step="3">
            <div class="discord-title">
                <div class="icon">🖱️</div>
                Step 2: Copy User ID
            </div>
            <div class="discord-text">
                Now, simply <strong>Right-Click</strong> any user's profile picture or name (including your own!) and select <strong>Copy User ID</strong> at the bottom of the menu.
            </div>
            <div class="discord-visual">
                <div style="background: #18191c; border-radius: 4px; padding: 10px; text-align: left; font-size: 0.8rem;">
                    <div style="padding: 5px 10px; opacity: 0.5;">...</div>
                    <div style="padding: 8px 10px; background: #4752c4; color: #fff; border-radius: 3px; display: flex; justify-content: space-between; align-items: center;">
                        <span>Copy User ID</span>
                        <span style="opacity: 0.7;">📋</span>
                    </div>
                </div>
            </div>
            <div class="discord-nav">
                <button class="discord-btn discord-btn-secondary" onclick="nextDiscordStep(2)">Back</button>
                <button class="discord-btn discord-btn-primary" onclick="closeDiscordIdTour()">I Got It!</button>
            </div>
        </div>
    </div>
</div>

<script>
    function startDiscordIdTour() {
        document.getElementById('discordIdTutorial').style.display = 'flex';
        nextDiscordStep(1);
    }

    function closeDiscordIdTour() {
        document.getElementById('discordIdTutorial').style.display = 'none';
    }

    function nextDiscordStep(n) {
        document.querySelectorAll('.discord-step').forEach(s => s.classList.remove('active'));
        const next = document.querySelector(`.discord-step[data-discord-step="${n}"]`);
        if (next) next.classList.add('active');
    }
</script>
