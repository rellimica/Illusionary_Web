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

    .null-bubble {
        position: fixed;
        bottom: 65px;
        right: 15px;
        background: rgba(12, 10, 21, 0.95);
        border: 1px solid var(--accent-primary);
        color: white;
        padding: 8px 15px;
        border-radius: 12px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.8rem;
        pointer-events: none;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 10001;
        white-space: nowrap;
        box-shadow: 0 5px 15px rgba(255, 0, 234, 0.2);
    }
    .null-bubble.active {
        opacity: 1;
        transform: translateY(0);
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
        }
    }
</style>

<div class="null-bubble" id="nullBubble">Hehe...</div>
<img src="<?php echo ($prefix ?? ''); ?>happynull.png" class="happynull" id="happynullBtn" alt="Happy Null">
<audio id="nullSound" src="<?php echo ($prefix ?? ''); ?>sounds/fnaf-12-3-freddys-nose-sound.mp3"></audio>

<script>
    (function() {
        const btn = document.getElementById('happynullBtn');
        const sound = document.getElementById('nullSound');
        const bubble = document.getElementById('nullBubble');
        
        // Load persisted state
        let clickCount = parseInt(sessionStorage.getItem('null_clicks')) || 0;
        let selectedSong = sessionStorage.getItem('null_song') || null;
        let hideTimeout;

        const lolzSongs = [
            'baby-cry-autotune.mp3', 'discord-notif.mp3', 'home-depot.mp3', 'indian-christmas.mp3',
            'iphone-get-better.mp3', 'lava-chicken.mp3', 'let-me-do-it-for-you.mp3',
            'the-duck-song.mp3', 'whopper-song.mp3', 'windows-xp-start.mp3', 'yippee.mp3', 'bill-nye.mp3'
        ];
        let bgMusic = null;

        const messages = {
            1: "Hehe...",
            3: "Boop!",
            5: "Stop that!",
            8: "Hey!",
            10: "That tickles!",
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
            25: "STOP IT!",
            28: "I'm warning you...",
            30: "STAY FOREVER",
            33: "You're persistent.",
            35: "DANCE FOR ME.",
            38: "DO YOU LIKE THIS SONG?",
            40: "I am watching...",
            42: "The void is loud, isn't it?",
            45: "I can see everything.",
            48: "Do you feel the glitch?",
            50: "...BOOP!",
            52: "I'm not just a bot.",
            55: "I know what you're thinking.",
            58: "Your collection is mine.",
            60: "You are testing my patience...",
            63: "You want a show?",
            65: "It's getting late.",
            67: "Zeke is hungry. Be careful.",
            68: "Why don't you leave?",
            69: "Vyper has already solved you.",
            70: "Final warning.",
            72: "I've had enough.",
            75: "GOODBYE."
        };

        // Apply initial visual state based on persisted clicks
        function applyVisualState() {
            if (clickCount >= 30 && clickCount < 60) {
                btn.classList.add('jitter');
            } else if (clickCount >= 60) {
                btn.classList.remove('jitter');
                btn.classList.add('glitch');
            }

            if (clickCount >= 30 && clickCount < 50) {
                bubble.style.color = "#ff4e4e";
                bubble.style.borderColor = "#ff4e4e";
                bubble.style.fontWeight = "800";
            } else if (clickCount >= 70) {
                bubble.style.color = "#ff4e4e";
                bubble.style.borderColor = "#ff4e4e";
                bubble.style.fontWeight = "900";
                bubble.style.boxShadow = "0 0 20px rgba(255, 78, 78, 0.5)";
            }
        }
        applyVisualState();

        // Music management
        function startMusic() {
            if (clickCount >= 35 && !bgMusic && selectedSong) {
                const prefix = '<?php echo ($prefix ?? ""); ?>';
                bgMusic = new Audio(`${prefix}sounds/lolz/${selectedSong}`);
                bgMusic.volume = 0.5;
                bgMusic.loop = true;
                bgMusic.play().then(() => {
                    // Success! Remove the global listener if it exists
                    window.removeEventListener('click', startMusic);
                }).catch(e => {
                    // Fail (likely autoplay block), wait for click
                    window.addEventListener('click', startMusic, { once: true });
                });
            }
        }
        
        // Initial attempt (may fail, browser depending)
        if (clickCount >= 35) {
            if (!selectedSong) {
                selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
                sessionStorage.setItem('null_song', selectedSong);
            }
            startMusic();
        }

        if (btn && sound && bubble) {
            btn.onclick = function(e) {
                e.stopPropagation(); // Avoid triggering window listener twice
                clickCount++;
                sessionStorage.setItem('null_clicks', clickCount);
                
                sound.currentTime = 0;
                sound.play().catch(e => {});

                // Ensure music is started/updated
                if (clickCount >= 35) {
                    if (!selectedSong) {
                        selectedSong = lolzSongs[Math.floor(Math.random() * lolzSongs.length)];
                        sessionStorage.setItem('null_song', selectedSong);
                    }
                    startMusic();
                }

                // Determine message
                let msg = messages[1];
                const thresholds = Object.keys(messages).map(Number).sort((a,b) => b-a);
                for(let t of thresholds) {
                    if (clickCount >= t) {
                        msg = messages[t];
                        break;
                    }
                }

                bubble.innerText = msg;
                bubble.classList.add('active');

                // Visual Effects on Null
                applyVisualState();

                // Bounce effect
                btn.style.transform = "scale(0.8) rotate(-10deg)";
                setTimeout(() => {
                    if (!btn.classList.contains('glitch') && !btn.classList.contains('jitter')) {
                        btn.style.transform = "";
                    }
                }, 100);

                // Handle Dialogue Colors (Reset if needed)
                if (clickCount < 30) {
                    bubble.style.color = "white";
                    bubble.style.borderColor = "var(--accent-primary)";
                    bubble.style.fontWeight = "normal";
                    bubble.style.boxShadow = "0 5px 15px rgba(255, 0, 234, 0.2)";
                }

                // Handle Logout Kick
                if (clickCount >= 75) {
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

                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(() => {
                    bubble.classList.remove('active');
                }, 2000);
            };
        }
    })();
</script>
