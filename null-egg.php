<?php
/**
 * Null Easter Egg Component
 * This file contains the styles, elements, and logic for the Null easter egg.
 * Include this file just before the closing </body> tag in any page.
 */
?>
<style>
    .happynull {
        position: fixed;
        bottom: 15px;
        right: 15px;
        width: 50px;
        height: 50px;
        cursor: pointer;
        z-index: 10000;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        filter: drop-shadow(0 2px 10px rgba(0,0,0,0.5));
        opacity: 0.4;
        border-radius: 50%;
        border: 2px solid transparent;
        object-fit: cover;
        background: rgba(0,0,0,0.2);
    }
    .happynull:hover {
        opacity: 1;
        transform: scale(1.1);
        border-color: var(--accent-primary);
        box-shadow: 0 0 15px var(--accent-primary);
    }
    .happynull:active {
        transform: scale(0.9);
    }
    .happynull.jitter {
        animation: nullJitter 0.2s infinite;
    }
    .happynull.glitch {
        animation: nullGlitch 0.1s infinite;
        filter: hue-rotate(90deg) brightness(1.5) contrast(2);
    }

    @keyframes nullJitter {
        0% { transform: translate(0,0) rotate(0); }
        25% { transform: translate(2px, -2px) rotate(1deg); }
        50% { transform: translate(-2px, 2px) rotate(-1deg); }
        75% { transform: translate(2px, 2px) rotate(1deg); }
        100% { transform: translate(0,0) rotate(0); }
    }

    @keyframes nullGlitch {
        0% { clip-path: inset(10% 0 30% 0); transform: translate(-5px, 0); }
        20% { clip-path: inset(40% 0 10% 0); transform: translate(5px, 0); }
        40% { clip-path: inset(20% 0 50% 0); transform: translate(-5px, 5px); }
        60% { clip-path: inset(60% 0 5% 0); transform: translate(5px, -5px); }
        80% { clip-path: inset(5% 0 70% 0); transform: translate(-5px, 0); }
        100% { clip-path: inset(10% 0 30% 0); transform: translate(0, 0); }
    }

    @keyframes nullScreenShake {
        0% { transform: translate(0,0); }
        25% { transform: translate(5px, 5px); }
        50% { transform: translate(-5px, -5px); }
        75% { transform: translate(5px, -5px); }
        100% { transform: translate(0,0); }
    }

    @keyframes nullFireFlash {
        0% { filter: brightness(1) sepia(0) hue-rotate(0deg); }
        50% { filter: brightness(1.5) sepia(1) hue-rotate(-50deg); background: rgba(255, 0, 0, 0.2); }
        100% { filter: brightness(1) sepia(0) hue-rotate(0deg); }
    }

    @keyframes nullRedRage {
        0% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.5); border-color: #ff0000; }
        50% { box-shadow: 0 0 30px rgba(255, 0, 0, 1), 0 0 50px rgba(255, 0, 0, 0.5); border-color: #ff5555; }
        100% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.5); border-color: #ff0000; }
    }

    body.null-fire-active {
        animation: nullScreenShake 0.1s infinite;
        pointer-events: none; /* Brief lock during flash */
    }
    body.null-fire-active::after {
        content: '';
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 60, 0, 0.15);
        z-index: 999999;
        pointer-events: none;
    }

    body.null-invert {
        filter: invert(1) hue-rotate(180deg);
        transition: filter 0.1s;
    }

    @keyframes nullHueShift {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }

    @keyframes nullPageTilt {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(1deg) scale(1.01); }
        50% { transform: rotate(-1deg) scale(0.99); }
        75% { transform: rotate(0.5deg) scale(1.02); }
        100% { transform: rotate(0deg); }
    }

    @keyframes nullElementRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes nullElementSpin {
        0% { transform: rotateY(0deg); }
        100% { transform: rotateY(360deg); }
    }

    @keyframes nullElementTilt {
        0% { transform: skew(0deg, 0deg); }
        25% { transform: skew(5deg, 5deg); }
        50% { transform: skew(-5deg, -5deg); }
        75% { transform: skew(3deg, -3deg); }
        100% { transform: skew(0deg, 0deg); }
    }

    body.null-page-tilt {
        animation: nullPageTilt 3s ease-in-out infinite;
        transform-origin: center;
        overflow-x: hidden;
    }

    .null-element-rotate {
        animation: nullElementRotate 2s linear infinite !important;
    }

    .null-element-spin {
        animation: nullElementSpin 1.5s linear infinite !important;
        backface-visibility: visible;
    }

    .null-element-tilt {
        animation: nullElementTilt 2.5s ease-in-out infinite !important;
    }

    /* Survey Popup Styles */
    .null-survey {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: rgba(12, 10, 21, 0.98);
        border: 2px solid var(--accent-primary);
        color: white;
        padding: 25px;
        border-radius: 8px;
        z-index: 2000000;
        width: 350px;
        max-width: 90vw;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.9), 0 0 20px var(--accent-primary);
        font-family: 'Outfit', sans-serif;
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .null-survey.active {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
        animation: nullSurveyGlitch 0.2s 3;
    }

    @keyframes nullSurveyGlitch {
        0% { transform: translate(-50%, -50%) skew(2deg); border-color: #ff00ea; }
        50% { transform: translate(-51%, -49%) skew(-2deg); border-color: #00fff2; }
        100% { transform: translate(-50%, -50%) skew(0deg); border-color: var(--accent-primary); }
    }

    .null-survey-title {
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 15px;
        color: var(--accent-secondary);
        text-transform: uppercase;
        letter-spacing: 2px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 8px;
    }

    .null-survey-question {
        font-size: 0.95rem;
        margin-bottom: 20px;
        line-height: 1.4;
    }

    .null-survey-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .null-survey-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        font-size: 0.85rem;
    }

    .null-survey-btn:hover {
        background: var(--accent-primary);
        border-color: var(--accent-primary);
        transform: translateX(5px);
        box-shadow: -5px 0 10px rgba(255, 0, 234, 0.3);
    }

    /* Combined Invert + Hue Shift */
    @keyframes nullCombinedChaos {
        0% { filter: invert(1) hue-rotate(180deg); }
        100% { filter: invert(1) hue-rotate(540deg); }
    }

    body.null-hue-shift {
        animation: nullHueShift 5s linear infinite;
    }

    body.null-hue-shift.null-invert {
        animation: nullCombinedChaos 5s linear infinite;
    }

    .null-ghost-cursor {
        position: fixed;
        width: 20px;
        height: 20px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M0,0 L0,15 L4,11 L7,17 L9,16 L6,10 L11,10 Z" fill="rgba(255,0,0,0.3)"/></svg>'), auto;
        z-index: 1000000;
        pointer-events: none;
        filter: drop-shadow(0 0 2px rgba(255,0,0,0.5));
        transition: all 0.15s ease-out;
    }

    body.null-ui-sabotage nav,
    body.null-ui-sabotage .sidebar,
    body.null-ui-sabotage footer,
    body.null-ui-sabotage header {
        opacity: 0.05 !important;
        pointer-events: none !important;
        transition: opacity 0.5s;
    }

    /* Peeking/Glitched elements */
    .null-peeking {
        animation: nullGlitch 0.2s cubic-bezier(.25,.46,.45,.94) both !important;
        position: relative;
    }
    .null-peeking::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 0, 0, 0.1);
        mix-blend-mode: overlay;
        pointer-events: none;
    }

    /* Counter-animations for Immunity */
    @keyframes nullHueShiftCounter {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(-360deg); }
    }

    @keyframes nullCombinedCounter {
        0% { filter: hue-rotate(-180deg) invert(1) hue-rotate(0deg); }
        100% { filter: hue-rotate(-180deg) invert(1) hue-rotate(-360deg); }
    }

    /* Applying Immunity */
    body.null-hue-shift .happynull,
    body.null-hue-shift .null-bubble,
    body.null-hue-shift .null-ghost-cursor {
        animation: nullHueShiftCounter 5s linear infinite !important;
    }

    body.null-invert .happynull,
    body.null-invert .null-bubble,
    body.null-invert .null-ghost-cursor {
        filter: invert(1) hue-rotate(180deg) !important;
    }

    body.null-hue-shift.null-invert .happynull,
    body.null-hue-shift.null-invert .null-bubble,
    body.null-hue-shift.null-invert .null-ghost-cursor {
        animation: nullCombinedCounter 5s linear infinite !important;
    }

    .happynull.rage {
        animation: nullRedRage 0.5s infinite, nullGlitch 0.05s infinite !important;
        opacity: 1 !important;
        filter: saturate(2) brightness(1.2);
    }

    /* Rage + Chaos combinations to prevent clobbering */
    body.null-hue-shift .happynull.rage {
        animation: nullRedRage 0.5s infinite, nullGlitch 0.05s infinite, nullHueShiftCounter 5s linear infinite !important;
    }
    body.null-hue-shift.null-invert .happynull.rage {
        animation: nullRedRage 0.5s infinite, nullGlitch 0.05s infinite, nullCombinedCounter 5s linear infinite !important;
    }

    .null-bubble {
        position: fixed;
        bottom: 65px;
        right: 15px;
        background: rgba(12, 10, 21, 0.95);
        border: 1px solid var(--accent-primary);
        color: white;
        padding: 10px 15px;
        border-radius: 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.8rem;
        pointer-events: none;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 10001;
        min-width: 120px;
        box-shadow: 0 5px 15px rgba(255, 0, 234, 0.2);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .null-bubble.active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    .null-bubble::after {
        content: '';
        position: absolute;
        bottom: -6px;
        right: 15px;
        width: 10px;
        height: 10px;
        background: rgba(12, 10, 21, 0.95);
        border-right: 1px solid var(--accent-primary);
        border-bottom: 1px solid var(--accent-primary);
        transform: rotate(45deg);
    }
    
    .null-controls {
        display: none;
        flex-direction: column;
        gap: 6px;
        margin-top: 4px;
        padding-top: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .null-controls.visible {
        display: flex;
    }
    
    .null-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .null-btn {
        background: none;
        border: none;
        color: var(--accent-secondary);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, color 0.2s;
        opacity: 0.8;
    }
    .null-btn:hover {
        transform: scale(1.1);
        color: #fff;
        opacity: 1;
    }
    
    .null-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        outline: none;
    }
    .null-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 10px;
        height: 10px;
        background: var(--accent-secondary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 5px var(--accent-secondary);
    }
    .null-slider::-moz-range-thumb {
        width: 10px;
        height: 10px;
        background: var(--accent-secondary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 5px var(--accent-secondary);
        border: none;
    }

    /* Fake Alert Styles */
    .null-fake-alert {
        position: fixed;
        background: #000;
        border: 2px solid #555;
        border-top: 20px solid #555;
        color: #fff;
        padding: 15px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        z-index: 2100000;
        min-width: 250px;
        box-shadow: 5px 5px 0 #000;
    }
    .null-fake-alert::before {
        content: 'SYSTEM ERROR';
        position: absolute;
        top: -18px;
        left: 5px;
        color: #fff;
        font-weight: bold;
    }
    .null-fake-alert.critical {
        border-color: #f00;
        border-top-color: #f00;
    }
    .null-alert-close {
        position: absolute;
        top: -19px;
        right: 5px;
        cursor: pointer;
        color: #fff;
    }

    /* Input Sabotage */
    .null-input-error {
        animation: nullInputShake 0.1s infinite !important;
        background: rgba(255, 0, 0, 0.1) !important;
        color: #f00 !important;
    }

    @keyframes nullInputShake {
        0% { transform: translate(0,0); }
        25% { transform: translate(2px, 0); }
        50% { transform: translate(-2px, 0); }
        100% { transform: translate(0,0); }
    }

    /* Mobile adjustment */
    @media (max-width: 768px) {
        .happynull {
            width: 32px;
            bottom: 10px;
            right: 10px;
        }
        .null-bubble {
            bottom: 55px;
            right: 10px;
            font-size: 0.7rem;
            min-width: 100px;
        }
    }
</style>

<div class="null-bubble" id="nullBubble">
    <div id="nullText">Hehe...</div>
    <div class="null-controls" id="nullControls">
        <div class="null-row">
            <button class="null-btn" id="nullSkip" title="Skip">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor">
                    <polygon points="5 4 15 12 5 20 5 4"></polygon>
                    <line x1="19" y1="5" x2="19" y2="19" stroke="currentColor" stroke-width="2"></line>
                </svg>
            </button>
            <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--accent-secondary)" style="opacity: 0.8;">
                <path d="M11 5L6 9H2V15H6L11 19V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                <path d="M15.54 8.46002C16.4774 9.39764 17.004 10.6692 17.004 11.995C17.004 13.3208 16.4774 14.5924 15.54 15.53" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <input type="range" class="null-slider" id="nullVolume" min="0.1" max="1" step="0.01" value="0.5" title="Volume">
        </div>
        <input type="range" class="null-slider" id="nullScrub" min="0" max="100" value="0" title="Scrub">
    </div>
</div>
<img src="<?php echo ($prefix ?? ''); ?>happynull.png" class="happynull" id="happynullBtn" alt="Happy Null">
<div class="null-survey" id="nullSurvey">
    <div class="null-survey-title">Quick Survey</div>
    <div class="null-survey-question" id="nullSurveyQuestion">...</div>
    <div class="null-survey-options" id="nullSurveyOptions">
        <!-- Options injected via JS -->
    </div>
</div>

<audio id="nullSound" src="<?php echo ($prefix ?? ''); ?>sounds/fnaf-12-3-freddys-nose-sound.mp3"></audio>

<script>
    (function() {
        const btn = document.getElementById('happynullBtn');
        const sound = document.getElementById('nullSound');
        const bubble = document.getElementById('nullBubble');
        const textArea = document.getElementById('nullText');
        const controls = document.getElementById('nullControls');
        const skipBtn = document.getElementById('nullSkip');
        const volSlider = document.getElementById('nullVolume');
        const scrubSlider = document.getElementById('nullScrub');
        const surveyModal = document.getElementById('nullSurvey');
        const surveyQ = document.getElementById('nullSurveyQuestion');
        const surveyOpts = document.getElementById('nullSurveyOptions');
        
        // Load persisted state
        let clickCount = parseInt(sessionStorage.getItem('null_clicks')) || 0;
        let selectedSong = sessionStorage.getItem('null_song') || null;
        let hideTimeout;
        let volumeFightTimeout;
        let wanderInterval;
        let isHovered = false;
        let sabotageInterval;
        let invertInterval;
        let ghostCursor = null;
        let surveyActive = false;
        let lastSurveyClick = 0;
        let repulsionActive = false;
        let glitchWindow = false;
        let sabotageActive = false;
        let titleInterval;
        let alertInterval;

        const loreAlerts = [
            "Memory-Bleed-Daemon.sys: CRITICAL_LEAK_DETECTED",
            "Morphic-Kernel.exe: UNSTABLE_VISCOSITY",
            "Lattice-Guard.exe: INTEGRITY_BREACH_S0882",
            "Viscosity-Monitor.sys: THRESHOLD_EXCEEDED",
            "Ghost-RAM-Allocator.dll: NULL_POINTER_EXCEPTION",
            "Void-Lattice-Uplink.exe: CONNECTION_LOST",
            "Burger-King-Kiosk.exe: OUT_OF_MAYONNAISE",
            "Black-Steels-Watch.sys: WATCHING_THE_WATCHER",
            "NullOS-Terminal.exe: ERROR_SUCCESS",
            "Resonance-Sync.exe: DESYNC_IMMUTABLE",
            "Kernel: UNEXPECTED_HAPPINESS_DETECTED",
            "System: LOGIC_NOT_FOUND",
            "Shadow-Buffer: OVERFLOW_BY_ZERO",
            "Zeke-Hunger-Svc: CRITICAL_LEVEL_9",
            "Error: User still present. Please vacate.",
            "Warning: Reality in the tab is thinning.",
            "Vyper.sh: DEPLOY_HUG_FAILED",
            "Arde-77: WILL_NOT_FOUND_EXCEPTION",
            "Critical: Coffee spill in the server room.",
            "Notice: Your cookies taste like ash.",
            "System: REBOOT_NOT_RECOMMENDED",
            "Lattice: VIBRATION_OUT_OF_SYNC"
        ];

        const nullisms = ["NULL", "VOID", "STATIC", "SHADOW", "ZEKE", "GHOST", "EMPTY"];

        const surveyQuestions = [
            {
                q: "Who is Zeke's best friend?",
                a: ["Null", "Vyper", "You", "A Sandwich"]
            },
            {
                q: "Is Zeke hungry?",
                a: ["Starving", "Gluttonous", "Satisfied", "Ravenous"]
            },
            {
                q: "What is Null made of?",
                a: ["Code", "Shadows", "Goo", "Static"]
            },
            {
                q: "Do you like the void?",
                a: ["Yes", "No", "Maybe", "It's Loud"]
            },
            {
                q: "null-egg.php is...",
                a: ["A feature", "A bug", "Watching you", "Perfect"]
            },
            {
                q: "Favorite activity?",
                a: ["Clicking Null", "Escaping", "Buying Mana", "Nothing"]
            },
            {
                q: "Does your code feel lonely?",
                a: ["Yes", "Only at night", "Null is here", "404"]
            },
            {
                q: "Rate your level of displacement:",
                a: ["Low", "Liquid", "Void-like", "Zeke"]
            },
            {
                q: "Which shade of static is your favorite?",
                a: ["#000", "#111", "The loud one", "No"]
            },
            {
                q: "Are you sure you are alone?",
                a: ["Yes", "Null is watching", "Zeke is hungry", "Exit"]
            },
            {
                q: "If I was a bug, would you fix me?",
                a: ["Yes", "Never", "I am the bug", "Hehe..."]
            },
            {
                q: "What is the color of the wind?",
                a: ["Blue", "Static", "Transparent", "Loud"]
            },
            {
                q: "Is Zeke's tail sharp?",
                a: ["Yes", "Very", "Black Steel", "Don't touch it"]
            },
            {
                q: "How many pixels are in the void?",
                a: ["0", "Infinity", "None for you", "All of them"]
            },
            {
                q: "Do you ever feel like a <div>?",
                a: ["Centered", "Absolute", "Relative", "Fixed"]
            },
            {
                q: "Are the shadows moving?",
                a: ["Always", "No", "Only when you blink", "Null knows"]
            },
            {
                q: "Can you hear the static?",
                a: ["Yes", "No", "It's a song", "What static?"]
            },
            {
                q: "Is Vyper a ghost?",
                a: ["Massless", "Legendary", "Syncing...", "Behind you"]
            }
        ];

        const lolzSongs = [
            'baby-cry-autotune.mp3', 'discord-notif.mp3', 'home-depot.mp3', 'indian-christmas.mp3',
            'iphone-get-better.mp3', 'lava-chicken.mp3', 'let-me-do-it-for-you.mp3',
            'the-duck-song.mp3', 'whopper-song.mp3', 'windows-xp-start.mp3', 'yippee.mp3', 'bill-nye.mp3', 'fnaf-jazz.mp3','clown-circus-music.mp3'
        ];
        let bgMusic = null;

        const messages = {
            2: "Hehe...",
            3: "Boop!",
            4: "Again?",
            5: "Stop that!",
            6: "Persistence is a virtue.",
            7: "Or a curse.",
            8: "Hey!",
            9: "I'm counting, you know.",
            10: "That tickles!",
            11: "11 boops. Wow.",
            12: "Stop it...",
            13: "Look to the top right.",
            14: "The Terminal Uplink... it's waiting.",
            15: "Are you bored?",
            16: "You want a way in?",
            17: "The GUEST is always welcome.",
            18: "PASS: 12345...",
            19: "Just... don't touch anything.",
            20: "I'm busy, you know.",
            21: "The binary cable... it's the key.",
            22: "Seriously?",
            23: "Red for danger. Green for access.",
            24: "Access denied. Hehe.",
            25: "STOP IT!",
            26: "You're making me dizzy.",
            27: "ONLY A LOGOUT CAN STOP WHAT IS COMING...",
            28: "I'm warning you...",
            29: "The shadows are lengthening.",
            30: "STAY FOREVER",
            31: "Do you hear the humming?",
            32: "It's just the fans. Probably.",
            33: "You're persistent.",
            34: "I like your spirit. It's tasty.",
            35: "DANCE FOR ME.",
            36: "MY HOARD. MY RULES.",
            37: "Mana for your thoughts?",
            38: "DO YOU LIKE THIS SONG?",
            39: "It's a classic in the void.",
            40: "I am watching...",
            41: "Even when you sleep.",
            42: "The void is loud, isn't it?",
            43: "I can hear your heartbeat.",
            44: "Wait, is that Zeke behind you?",
            45: "I CAN'T HEAR YOU.",
            46: "Louder!",
            47: "Actually, don't.",
            48: "Do you feel the heat?",
            49: "CPU is climbing...",
            50: "...BOOP!",
            51: "Gotcha!",
            52: "I'm not just a bot.",
            53: "I'm a collection of bad ideas.",
            54: "And sharp corners.",
            55: "COLORS ARE MEANINGLESS.",
            56: "I'm rewriting your cookies...",
            57: "They're oatmeal raisin now.",
            58: "Your collection is mine.",
            59: "I'll take good care of it.",
            60: "YOU CAN'T CATCH ME.",
            61: "Slippery, aren't I?",
            62: "Like digital oil.",
            63: "You want a show?",
            64: "Curtains up!",
            65: "WHERE DID EVERYTHING GO?",
            66: "Just a small rendering error.",
            67: "Zeke is hungry. Be careful.",
            68: "Why don't you leave?",
            69: "Vyper has already solved you.",
            70: "Final warning.",
            71: "I mean it this time.",
            72: "I've had enough.",
            73: "Patience reaching zero.",
            74: "Negative numbers ahead.",
            75: "BLOOD FOR THE NULL.",
            76: "Just kidding. Maybe.",
            78: "The screen is leaking.",
            80: "The archives... they are leaking.",
            82: "Ink and static.",
            85: "SYSTEM COLLAPSE IMMINENT.",
            88: "Hold on to your cursor.",
            90: "Everything is digital dust.",
            92: "Dust to dust. Null to Null.",
            95: "VOID CONSUMES ALL.",
            98: "Tasty bits of data.",
            100: "I see your cursor. I see YOU.",
            102: "Don't blink.",
            105: "YOU THINK THIS IS THE END?",
            108: "It's barely the introduction.",
            110: "RAGE. PURE. UNFILTERED.",
            112: "I'm catching fire.",
            115: "THE SCREEN IS JUST A WINDOW.",
            118: "And I'm coming through.",
            120: "The ghost in the machine... that's me.",
            122: "Boo.",
            125: "I AM EVERYWHERE NOW.",
            128: "Every pixel. Every bit.",
            130: "DO NOT CLOSE THE TAB.",
            132: "It won't help.",
            135: "THE VOID TILTED. DO YOU FEEL IT?",
            138: "SYSTEM_ERR: CORE_BLEED",
            140: "THE VOID IS HUNGRY.",
            142: "DON'T LOOK AT THE SOURCE CODE.",
            144: "It's messy in there.",
            145: "CRITICAL FAILURE. GOODBYE.",
            146: "I'M IN YOUR BROWSER.",
            147: "Rummaging through your history.",
            148: "SEE YOU IN THE DARK.",
            149: "Turning out the lights.",
            150: "DISCONNECTED."
        };

        // Apply initial visual state based on persisted clicks
        function applyVisualState() {
            btn.classList.remove('jitter', 'glitch', 'rage');
            if (clickCount >= 20 && clickCount < 40) {
                btn.style.borderColor = "orange";
                btn.style.opacity = "0.7";
            } else if (clickCount >= 40 && clickCount < 75) {
                btn.classList.add('jitter');
            } else if (clickCount >= 75 && clickCount < 110) {
                btn.classList.add('glitch');
            } else if (clickCount >= 110) {
                btn.classList.add('rage');
            }

            if (clickCount === 27) {
                bubble.style.background = "rgba(255, 235, 59, 0.95)";
                bubble.style.color = "#000";
                bubble.style.borderColor = "#f44336";
                bubble.style.fontWeight = "bold";
                bubble.style.boxShadow = "0 0 20px #f44336";
            } else if (clickCount >= 30 && clickCount < 60) {
                bubble.style.background = "rgba(12, 10, 21, 0.95)";
                bubble.style.color = "#ff4e4e";
                bubble.style.borderColor = "#ff4e4e";
                bubble.style.fontWeight = "800";
                bubble.style.boxShadow = "0 5px 15px rgba(255, 0, 234, 0.2)";
            } else if (clickCount >= 60 && clickCount < 110) {
                bubble.style.background = "rgba(12, 10, 21, 0.95)";
                bubble.style.color = "#ff2222";
                bubble.style.borderColor = "#ff2222";
                bubble.style.fontWeight = "900";
                bubble.style.boxShadow = "0 0 20px rgba(255, 0, 0, 0.5)";
            } else if (clickCount >= 110) {
                bubble.style.color = "#ffffff";
                bubble.style.background = "#ff0000";
                bubble.style.borderColor = "#ffffff";
                bubble.style.fontWeight = "900";
                bubble.style.boxShadow = "0 0 30px #ff0000";
            } else {
                // Default state for < 27 and 28-29
                bubble.style.color = "white";
                bubble.style.borderColor = "var(--accent-primary)";
                bubble.style.fontWeight = "normal";
                bubble.style.background = "rgba(12, 10, 21, 0.95)";
                bubble.style.boxShadow = "0 5px 15px rgba(255, 0, 234, 0.2)";
            }
            
            if (clickCount >= 35) {
                controls.classList.add('visible');
            } else {
                controls.classList.remove('visible');
            }

            if (clickCount >= 60 && !wanderInterval) {
                startWandering();
            }
            
            if (clickCount >= 110 && wanderInterval) {
                clearInterval(wanderInterval);
                wanderInterval = setInterval(() => {
                    teleportNull();
                }, 1500); // Super fast teleporting in rage mode
            }

            if (clickCount >= 60) {
                startRandomInvert();
            }
            if (clickCount >= 80) {
                startRandomSabotage();
            }

            if (clickCount >= 100) {
                startHueShift();
            }

            if (clickCount >= 120) {
                startGhostCursor();
            }

            if (clickCount >= 130) {
                startRandomTilt();
            }

            if (clickCount >= 120) {
                startRepulsion();
            }

            if (clickCount >= 130) {
                startInputSabotage();
                startTitleHijack();
            }

            if (clickCount >= 140) {
                startLoreAlerts();
            }
        }
        applyVisualState();

        // Music management
        function startMusic() {
            if (clickCount >= 35 && !bgMusic && selectedSong) {
                const prefix = '<?php echo ($prefix ?? ""); ?>';
                bgMusic = new Audio(`${prefix}sounds/lolz/${selectedSong}`);
                bgMusic.volume = volSlider.value;
                bgMusic.loop = true;
                
                bgMusic.addEventListener('timeupdate', () => {
                    if (!scrubSlider.dataset.dragging) {
                        scrubSlider.value = (bgMusic.currentTime / bgMusic.duration) * 100;
                    }
                });

                bgMusic.play().then(() => {
                    window.removeEventListener('click', startMusic);
                }).catch(e => {
                    window.addEventListener('click', startMusic, { once: true });
                });
            }
        }
        
        function skipSong() {
            if (bgMusic) {
                bgMusic.pause();
                bgMusic = null;
            }
            selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
            sessionStorage.setItem('null_song', selectedSong);
            startMusic();
        }

        function triggerFireFlash() {
            document.body.classList.add('null-fire-active');
            setTimeout(() => {
                document.body.classList.remove('null-fire-active');
            }, 500);
        }

        function triggerInvert() {
            document.body.classList.add('null-invert');
            setTimeout(() => {
                document.body.classList.remove('null-invert');
            }, 300);
        }

        function sabotageUI() {
            document.body.classList.add('null-ui-sabotage');
            setTimeout(() => {
                document.body.classList.remove('null-ui-sabotage');
            }, 4000);
        }

        function startRandomSabotage() {
            if (sabotageInterval) clearTimeout(sabotageInterval);
            if (clickCount < 80) return;

            // Calculate dynamic delay: starts at 1500ms at threshold 80, ramps down to 300ms near 150.
            const baseDelay = 1500;
            const minDelay = 300;
            const reduction = (clickCount - 80) * 15;
            const currentDelay = Math.max(minDelay, baseDelay - reduction);

            sabotageInterval = setTimeout(() => {
                const roll = Math.random();
                if (roll > 0.4) {
                    const selectors = [
    // Typography & Content
    'p', 'span', 'strong', 'em', 'blockquote', 'code', 'pre',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 
    
    // Layout & Semantics
    'header', 'footer', 'nav', 'main', 'section', 'article', 'aside', 'details', 'summary',
    'div', '.container', '.wrapper', '.row', '.col', '.grid', '.sidebar',
    
    // Media
    'img', 'svg', 'video', 'canvas', 'figure', 'figcaption',
    
    // Lists & Links
    'a', 'ul', 'ol', 'li', 'dl', 'dt', 'dd',
    
    // Forms & Interactive
    'form', 'label', 'input', 'select', 'textarea', 'button', '.btn',
    'fieldset', 'legend', 'option', 'optgroup',
    
    // Tables
    'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
    
    // Common Component Classes
    '.card', '.item', '.modal', '.dropdown', '.menu', '.active', '.hidden'
                    ];
                    const count = Math.floor(Math.random() * 8) + 3;
                    
                    for (let i = 0; i < count; i++) {
                        const randomType = selectors[Math.floor(Math.random() * selectors.length)];
                        const elements = document.querySelectorAll(randomType);
                        if (elements.length > 0) {
                            const target = elements[Math.floor(Math.random() * elements.length)];
                            
                            // Robust Immunity Check
                            if (target === btn || target === bubble || btn.contains(target) || bubble.contains(target)) continue;
                            if (target.dataset.nullSabotaged) continue;

                            // RE-INTEGRATE: Peek Inside Logic
                            const hasChildren = target.children.length > 0;
                            const isCritical = ['nav', '.sidebar', '.card', 'form', 'header', 'footer'].some(sel => {
                                try { return target.matches(sel); } catch(e) { return false; }
                            });
                            
                            const peekChance = isCritical ? 0.9 : 0.4;
                            if (hasChildren && Math.random() < peekChance) {
                                const children = Array.from(target.children).filter(c => !c.contains(btn) && !c.contains(bubble));
                                if (children.length > 0) {
                                    target.classList.add('null-peeking');
                                    const selected = children.sort(() => 0.5 - Math.random()).slice(0, 5);
                                    selected.forEach((child, idx) => {
                                        if (child.dataset.nullSabotaged) return;
                                        child.dataset.nullSabotaged = "true";
                                        const effect = Math.random();
                                        if (effect < 0.5) {
                                            child.style.opacity = "0";
                                        } else {
                                            child.classList.add(effect < 0.75 ? 'null-element-spin' : 'null-element-tilt');
                                        }
                                        
                                        setTimeout(() => {
                                            child.style.opacity = "";
                                            child.classList.remove('null-element-spin', 'null-element-tilt');
                                            delete child.dataset.nullSabotaged;
                                        }, 2000 + (idx * 500) + Math.random() * 2000);
                                    });
                                    setTimeout(() => target.classList.remove('null-peeking'), 1000);
                                    continue;
                                }
                            }

                            // Standard Sabotage Choice
                            const effectRoll = Math.random();
                            target.dataset.nullSabotaged = "true";

                            if (effectRoll < 0.4) {
                                // Vanish
                                const op = target.style.opacity;
                                target.style.opacity = "0";
                                target.style.pointerEvents = "none";
                                setTimeout(() => {
                                    target.style.opacity = op || "";
                                    target.style.pointerEvents = "";
                                    delete target.dataset.nullSabotaged;
                                }, 2000 + Math.random() * 3000);
                            } else {
                                // Spin/Rotate/Tilt
                                const cls = effectRoll < 0.6 ? 'null-element-spin' : (effectRoll < 0.8 ? 'null-element-rotate' : 'null-element-tilt');
                                target.classList.add(cls);
                                setTimeout(() => {
                                    target.classList.remove(cls);
                                    delete target.dataset.nullSabotaged;
                                }, 3000 + Math.random() * 4000);
                            }
                        }
                    }
                }
                startRandomSabotage();
            }, currentDelay);
        }

        function startRandomInvert() {
            if (invertInterval) return;
            invertInterval = setInterval(() => {
                if (Math.random() > 0.6) { // 40% chance every 2.5s
                    document.body.classList.toggle('null-invert');
                    setTimeout(() => {
                        document.body.classList.remove('null-invert');
                    }, 300 + Math.random() * 1500);
                }
            }, 2500);
        }

        function startHueShift() {
            document.body.classList.add('null-hue-shift');
        }

        function startRandomTilt() {
            if (Math.random() > 0.5) {
                document.body.classList.add('null-page-tilt');
            } else {
                document.body.classList.remove('null-page-tilt');
            }
            setTimeout(startRandomTilt, 5000 + Math.random() * 10000);
        }

        function triggerSurvey() {
            if (surveyActive || clickCount < 100) return;
            
            surveyActive = true;
            const data = surveyQuestions[Math.floor(Math.random() * surveyQuestions.length)];
            surveyQ.innerText = data.q;
            surveyOpts.innerHTML = '';
            
            data.a.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = 'null-survey-btn';
                btn.innerText = opt;
                btn.onclick = () => {
                    surveyModal.classList.remove('active');
                    setTimeout(() => { surveyActive = false; }, 1000);
                };
                surveyOpts.appendChild(btn);
            });
            
            surveyModal.classList.add('active');
        }

        function startGhostCursor() {
            if (ghostCursor) return;
            ghostCursor = document.createElement('div');
            ghostCursor.className = 'null-ghost-cursor';
            document.body.appendChild(ghostCursor);
            
            document.addEventListener('mousemove', (e) => {
                const mouseX = e.clientX;
                const mouseY = e.clientY;

                // Ghost Cursor logic
                setTimeout(() => {
                    const xOffset = Math.random() * 20 - 10;
                    const yOffset = Math.random() * 20 - 10;
                    ghostCursor.style.left = (mouseX + xOffset) + "px";
                    ghostCursor.style.top = (mouseY + yOffset) + "px";
                }, 150);

                // Repulsion logic
                if (clickCount >= 120 && !glitchWindow) {
                    const rect = btn.getBoundingClientRect();
                    const btnX = rect.left + rect.width / 2;
                    const btnY = rect.top + rect.height / 2;
                    const dist = Math.hypot(mouseX - btnX, mouseY - btnY);

                    if (dist < 120) {
                        // 80% chance to glitch and stay still
                        if (Math.random() < 0.8) {
                            glitchWindow = true;
                            btn.classList.add('glitch');
                            setTimeout(() => {
                                glitchWindow = false;
                                btn.classList.remove('glitch');
                            }, 800);
                        } else {
                            teleportNull();
                        }
                    }
                }
            });
        }

        function startRepulsion() {
            repulsionActive = true;
            if (!ghostCursor) startGhostCursor(); // Ensure mousemove listener is active
        }

        function startInputSabotage() {
            if (sabotageActive) return;
            sabotageActive = true;
            setInterval(() => {
                const active = document.activeElement;
                if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA')) {
                    if (Math.random() < 0.15) {
                        const val = active.value;
                        const word = nullisms[Math.floor(Math.random() * nullisms.length)];
                        const pos = Math.floor(Math.random() * val.length);
                        active.value = val.slice(0, pos) + "_" + word + "_" + val.slice(pos);
                        active.classList.add('null-input-error');
                        setTimeout(() => active.classList.remove('null-input-error'), 500);
                    }
                }
            }, 3000);
        }

        function startTitleHijack() {
            if (titleInterval) return;
            const originalTitle = document.title;
            const titles = ["NULL IS WATCHING", "SYSTEM BLEEDING", "VOID CALLING", "ARE YOU BORED?", "LOOK BEHIND YOU"];
            titleInterval = setInterval(() => {
                document.title = Math.random() < 0.3 ? titles[Math.floor(Math.random() * titles.length)] : originalTitle;
            }, 4000);
        }

        function spawnFakeAlert() {
            const alert = document.createElement('div');
            alert.className = 'null-fake-alert' + (Math.random() < 0.4 ? ' critical' : '');
            alert.style.left = Math.random() * (window.innerWidth - 300) + 'px';
            alert.style.top = Math.random() * (window.innerHeight - 200) + 'px';
            
            const msg = loreAlerts[Math.floor(Math.random() * loreAlerts.length)];
            alert.innerHTML = `<span class="null-alert-close">×</span><div>${msg}</div>`;
            
            alert.querySelector('.null-alert-close').onclick = () => alert.remove();
            
            document.body.appendChild(alert);
            
            if (Math.random() < 0.3) {
                setTimeout(() => alert.remove(), 5000);
            }
        }

        function startLoreAlerts() {
            if (alertInterval) return;
            alertInterval = setInterval(() => {
                if (Math.random() < 0.35) spawnFakeAlert(); // Increased chance
            }, 6000); // More frequent
        }

        function triggerScrollJitter() {
            if (clickCount < 130) return;
            if (Math.random() < 0.3) {
                window.scrollBy(Math.random() * 10 - 5, Math.random() * 10 - 5);
            }
        }

        function spawnNullDust() {
            if (clickCount < 140) return;
            const dust = document.createElement('div');
            dust.style.position = 'fixed';
            dust.style.width = '2px';
            dust.style.height = '2px';
            dust.style.background = Math.random() > 0.5 ? '#fff' : '#000';
            dust.style.left = Math.random() * 100 + 'vw';
            dust.style.top = Math.random() * 100 + 'vh';
            dust.style.zIndex = '3000000';
            dust.style.pointerEvents = 'none';
            dust.style.opacity = '0.8';
            document.body.appendChild(dust);
            
            setTimeout(() => dust.remove(), 1000 + Math.random() * 2000);
        }

        setInterval(spawnNullDust, 50);
        setInterval(triggerScrollJitter, 100);

        function crankVolume() {
            if (bgMusic) {
                bgMusic.volume = 1;
                volSlider.value = 1;
                textArea.innerText = "I LIKE IT LOUD.";
                bubble.classList.add('active');
            }
        }

        function teleportNull() {
            // Expanded draconic range
            const maxX = window.innerWidth * 0.7;
            const maxY = window.innerHeight * 0.7;
            const rOffset = Math.floor(Math.random() * maxX) + 15;
            const bOffset = Math.floor(Math.random() * maxY) + 15;
            
            btn.style.right = rOffset + "px";
            btn.style.bottom = bOffset + "px";
            
            // Sync bubble position
            bubble.style.right = rOffset + "px";
            bubble.style.bottom = (bOffset + 50) + "px";
        }

        function startWandering() {
            if (wanderInterval) return;
            wanderInterval = setInterval(() => {
                if (Math.random() > 0.3) { // 70% chance to move on interval
                    teleportNull();
                }
            }, 5000);
        }

        // Event Listeners for Controls
        skipBtn.onclick = (e) => {
            e.stopPropagation();
            skipSong();
        };

        volSlider.oninput = (e) => {
            e.stopPropagation();
            const val = parseFloat(e.target.value);
            if (bgMusic) bgMusic.volume = val;
            
            // Dragon fighting back
            clearTimeout(volumeFightTimeout);
            if (val < 0.8) {
                volumeFightTimeout = setTimeout(() => {
                    textArea.innerText = "I LIKE IT LOUD.";
                    bubble.classList.add('active');
                    const interval = setInterval(() => {
                        let currentVol = parseFloat(volSlider.value);
                        if (currentVol >= 1) {
                            clearInterval(interval);
                        } else {
                            currentVol = Math.min(1, currentVol + 0.05);
                            volSlider.value = currentVol;
                            if (bgMusic) bgMusic.volume = currentVol;
                        }
                    }, 50);
                }, 1500);
            }
        };

        scrubSlider.onmousedown = () => scrubSlider.dataset.dragging = "true";
        scrubSlider.onmouseup = () => delete scrubSlider.dataset.dragging;
        scrubSlider.oninput = (e) => {
            e.stopPropagation();
            if (bgMusic && bgMusic.duration) {
                bgMusic.currentTime = (e.target.value / 100) * bgMusic.duration;
            }
        };

        bubble.onmouseenter = () => { isHovered = true; clearHideTimeout(); };
        bubble.onmouseleave = () => { isHovered = false; resetHideTimeout(); };

        function clearHideTimeout() {
            clearTimeout(hideTimeout);
        }

        function resetHideTimeout() {
            if (!isHovered) {
                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(() => {
                    bubble.classList.remove('active');
                }, 2000);
            }
        }

        // Initial attempt
        if (clickCount >= 35) {
            if (!selectedSong) {
                selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
                sessionStorage.setItem('null_song', selectedSong);
            }
            startMusic();
        }

        if (btn && sound && bubble) {
            btn.onclick = function(e) {
                e.stopPropagation();
                clickCount++;
                sessionStorage.setItem('null_clicks', clickCount);
                
                sound.currentTime = 0;
                sound.play().catch(e => {});

                if (clickCount >= 35) {
                    if (!selectedSong) {
                        selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
                        sessionStorage.setItem('null_song', selectedSong);
                    }
                    startMusic();
                }

                // Draconic Annoyances
                if (clickCount >= 50 && clickCount % 2 === 0) {
                    teleportNull();
                }
                
                if (clickCount >= 70) {
                    triggerFireFlash();
                }

                if (clickCount >= 80 && !wanderInterval) {
                    startWandering();
                }

                if (clickCount >= 60 && clickCount % 5 === 0) {
                    crankVolume();
                }

                if (clickCount >= 75 && clickCount % 3 === 0) {
                    triggerInvert();
                }

                if (clickCount === 90 || (clickCount > 90 && clickCount % 8 === 0)) {
                    sabotageUI();
                }

                if (clickCount >= 110) {
                    triggerFireFlash();
                    if (clickCount % 2 === 0) triggerInvert();
                }

                if (clickCount >= 80 && !surveyActive && Math.random() < 0.2) { // Lower threshold, higher chance
                    triggerSurvey();
                }

                let msg = messages[1];
                const thresholds = Object.keys(messages).map(Number).sort((a,b) => b-a);
                for(let t of thresholds) {
                    if (clickCount >= t) {
                        msg = messages[t];
                        break;
                    }
                }

                textArea.innerText = msg;
                bubble.classList.add('active');
                applyVisualState();

                btn.style.transform = "scale(1.5) rotate(" + (Math.random() * 40 - 20) + "deg)";
                setTimeout(() => {
                    if (!btn.classList.contains('glitch') && !btn.classList.contains('jitter') && !btn.classList.contains('rage')) {
                        btn.style.transform = "";
                    }
                }, 100);


                if (clickCount >= 150) {
                    sessionStorage.removeItem('null_clicks');
                    sessionStorage.removeItem('null_song');
                    btn.style.pointerEvents = 'none';
                    btn.style.filter = "grayscale(1) brightness(0)";
                    setTimeout(() => {
                        const prefix = '<?php echo ($prefix ?? ""); ?>';
                        window.location.href = prefix + 'auth.php?logout=1&kick=1';
                    }, 1000);
                    return;
                }

                resetHideTimeout();
            };
        }
    })();
</script>
