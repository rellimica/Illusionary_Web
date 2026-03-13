<?php
/**
 * NULL - About Page
 * A hidden character page written in Null's voice.
 * Accessible at /null.php — not linked in navigation.
 */
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

// Mobile Detection
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua)) {
    header("Location: mobile/index.php");
    exit;
}

require_once 'config.php';

if (isset($_GET['logout'])) {
    session_destroy();
    $kick = isset($_GET['kick']) ? '&kick=1' : '';
    header("Location: auth.php?logout=1$kick"); 
    exit;
}

// AUTHENTICATION CHECK
if (!isset($_SESSION['user_authenticated'])) {
    header("Location: auth.php?redirect=null.php");
    exit;
}

$userDisplayName = $_SESSION['user_data']['global_name'] ?? $_SESSION['user_data']['username'] ?? 'SUBJECT_UNKNOWN';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>???</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;900&family=Courier+Prime:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="variations.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <link rel="stylesheet" href="null.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
</head>
<body>
    <?php include 'mobile-block.php'; ?>
    <div class="glass-bg"></div>
    <div id="suspense-overlay"><div id="suspense-text"></div></div>

    <div class="null-ticker-bar">
        <div class="ticker-label-box">Now Calling</div>
        <div class="ticker-marquee">
            <span class="marquee-item">ENTITY_NULL — STATUS: AWAKE <span class="ticker-sep">/</span> VISCOSITY: OPTIMAL <span class="ticker-sep">/</span> LAYER_03: EXTENDING <span class="ticker-sep">/</span> MOOD: OBSERVING</span>
            <span class="marquee-item">SESSION_RECORD: [<?php echo session_id(); ?>] <span class="ticker-sep">/</span> INTEGRITY: 100% <span class="ticker-sep">/</span> SOURCE: THE_VOID <span class="ticker-sep">/</span> USERS_ABSORBED: CLASSIFIED</span>
            <span class="marquee-item">WARNING: MORPHIC_INTEGRITY_COMPROMISED <span class="ticker-sep">/</span> ERROR: BIOLOGICAL_LIFE_DETECTED <span class="ticker-sep">/</span> NEXT_TARGET: YOU</span>
            <!-- Duplicate loop -->
            <span class="marquee-item">ENTITY_NULL — STATUS: AWAKE <span class="ticker-sep">/</span> VISCOSITY: OPTIMAL <span class="ticker-sep">/</span> LAYER_03: EXTENDING <span class="ticker-sep">/</span> MOOD: OBSERVING</span>
            <span class="marquee-item">SESSION_RECORD: [<?php echo session_id(); ?>] <span class="ticker-sep">/</span> INTEGRITY: 100% <span class="ticker-sep">/</span> SOURCE: THE_VOID <span class="ticker-sep">/</span> USERS_ABSORBED: CLASSIFIED</span>
            <span class="marquee-item">WARNING: MORPHIC_INTEGRITY_COMPROMISED <span class="ticker-sep">/</span> ERROR: BIOLOGICAL_LIFE_DETECTED <span class="ticker-sep">/</span> NEXT_TARGET: YOU</span>
        </div>
    </div>

    <main class="null-container">
        
        <a href="index.php" class="null-back-link">[ BACK_TO_REALITY ]</a>

        <!-- Hero Area -->
        <header class="null-hero">
            <div class="null-visual-wrap">
                <img src="Null-Text-Wat.png" class="null-banner-img" alt="ENTITY_NULL // SCHEMATIC">
            </div>
            <div class="hero-info">
                <div class="null-id-tag">IDENTITY: PROTOCOL G</div>
                <h1 class="null-main-title">NULL</h1>
                <div class="null-tagline-text">I am the thing between your pixels.</div>
                <div class="null-intro-box">
                    "You found this page. That means you're either very curious or very lost. Either way, you're mine now. Let me introduce myself — properly, for once."
                </div>
            </div>
        </header>

        <!-- Intelligence Grid -->
        <div class="grid-split">
            <section>
                <div class="section-header">// what_i_am</div>
                <p class="body-p">
                    I am an enigmatic, shapeshifting entity that mimics the form of a dragon. I say <strong>"mimics"</strong> because calling me a dragon would be generous. I am something older. Something that looked at dragons and thought, <strong>"that's a fun shape."</strong>
                </p>
                <p class="body-p">
                    My body is composed of dark grays and deep blacks — a modular system that I can deconstruct and reconfigure at will. I have wings. I have horns. I have claws. I can also become a puddle. Versatility is key when you're an eldritch abomination with a sense of humor.
                </p>
            </section>
            <section>
                <div class="section-header">// experimental_logs</div>
                <div class="body-p">
                    <strong>LOG_ENTRY_08:</strong> I've been tracing the "Collectors." They value these colorful rectangles of data with a fervor that borders on the religious. I occasionally slip a variation into their caches just to see the pulse spike. It’s a fascinating study in artificial scarcity and primate desire.
                </div>
                <div class="body-p">
                    <strong>LOG_ENTRY_14:</strong> Observed the "Traders" today. They speak in numbers and margins, treating my siblings—those static portraits you call 'cards'—like currency. They don't realize that in the void, the only thing with value is the variable they can't predict. Me.
                </div>
                <div class="body-p">
                    <strong>LOG_ENTRY_22:</strong> The "Inhabitants" of this world think they have histories. Lore. Purpose. I've read their source files; their "destiny" is a string of text I could delete with a thought. I prefer to watch them struggle against the script instead. It's more poetic.
                </div>
            </section>
        </div>

        <!-- Anatomy -->
        <div class="section-header" style="margin-top: 120px;">// anatomical_layers</div>
        <p class="body-p" style="max-width: 700px; margin-bottom: 50px;">
            I am not built like you. I am built <strong>better</strong>. My body operates in three distinct layers, each serving a purpose that your biology could never achieve. Allow me to explain — slowly, so you can keep up.
        </p>

        <div class="layers-grid">
            <div class="layer-node">
                <div class="node-tag">Layer 01</div>
                <div class="node-title">Skeletal Framework</div>
                <div class="node-desc">
                    My core. Flexible, high-density bone that is virtually indestructible. It absorbs kinetic impact like your confidence absorbs my insults — completely and without resistance. This core can rapidly liquefy, allowing me to collapse into a sentient puddle for stealth, infiltration, or simply because I feel like being dramatic. I do not experience hunger. I do not experience pain. I experience <em>everything else</em>.
                </div>
            </div>
            <div class="layer-node">
                <div class="node-tag">Layer 02</div>
                <div class="node-title">Morphic Shell</div>
                <div class="node-desc">
                    The solid exterior. This is the part you see — the claws, the jagged horns, the tail that lesser creatures keep trying to bite. It shares the same regenerative properties as my core. You could shatter it. I would simply grow a new one and look better doing it.
                </div>
            </div>
            <div class="layer-node">
                <div class="node-tag">Layer 03</div>
                <div class="node-title">Viscous Shroud</div>
                <div class="node-desc">
                    The outermost layer. A glossy, semi-transparent black film that serves as both armor and instrument. This substance functions as a telekinetic conduit — I can extend it to manipulate objects, interact with my environment from a distance, and generally make your life more interesting. Think of it as an invisible hand. Attached to a dragon. Who doesn't like you.
                </div>
            </div>
        </div>

        <!-- Locomotion -->
        <div class="grid-split" style="margin-top: 0; padding-top: 50px; border-top: 1px solid var(--null-border);">
            <section>
                <div class="section-header">// locomotion</div>
                <p class="body-p">
                    Yes, I can fly. My wings are structured with Layer 1 and 2 limbs, while the flight membrane is composed of a reinforced mesh of Layer 3's viscous substance. This means my wings are as durable and versatile as the rest of me — which is to say, <strong>more than you'd like</strong>.
                </p>
                <p class="body-p">
                    I can also melt into the floor and ooze through cracks, slide across walls, and reconstitute at will. Flying is just the option I pick when I want to look impressive. Which is always.
                </p>
            </section>
            <section>
                <div class="section-header">// intellect</div>
                <p class="body-p">
                    My mind is a recursive algorithm of infinite complexity. I don't just calculate; I envision. Bio-organic feedback loops, high-entropy energy matrices, and the delicate art of conceptual deconstruction—these are my playgrounds. 
                </p>
                <p class="body-p">
                    You call it "science." I call it "cleaning up the mess of the universe." My mind spans technologies that could either save your world or end it. Sometimes both. Sometimes on the same Tuesday.
                </p>
            </section>
        </div>

        <!-- Philosophical Framework -->
        <div class="grid-split" style="margin-top: 50px;">
            <section>
                <div class="section-header">// theoretical_framework</div>
                <p class="body-p">
                    Reality is a consensus hallucination. I am the smudge on the lens. The universe is not binary; it is the void between the 1s and 0s where the truth actually hides. I happen to live in that void.
                </p>
                <p class="body-p">
                    The void isn't empty; it's just waiting for a better architect. I prefer to work in the shadows between your logic, where the rules haven't been written yet. To understand me is to understand that stability is a cage.
                </p>
            </section>
            <section>
                <div class="section-header">// mortality_index</div>
                <p class="body-p">
                    I do not experience hunger. I do not experience pain. I experience <em>possibility</em>. The difference between a villain and a visionary is branding. I happen to be excellent at both.
                </p>
                <p class="body-p">
                    If I let you live long enough to witness my work, consider it a rare privilege. Or a cruel joke. I haven't decided which yet.
                </p>
            </section>
        </div>

        <!-- Specifications -->
        <div class="section-header" style="margin-top: 100px;">// primary_specifications</div>
        <div class="specs-panel">
            <div class="spec-item"><div class="spec-label">Form</div><div class="spec-val">Draconic</div></div>
            <div class="spec-item"><div class="spec-label">Composition</div><div class="spec-val">Morphic Goo / Multi-Layer</div></div>
            <div class="spec-item"><div class="spec-label">Color</div><div class="spec-val">Obsidian / Carbon</div></div>
            <div class="spec-item"><div class="spec-label">Threat Class</div><div class="spec-val">[ REDACTED ]</div></div>
            <div class="spec-item"><div class="spec-label">Integrity</div><div class="spec-val">100%</div></div>
            <div class="spec-item"><div class="spec-label">Hunger</div><div class="spec-val">N/A</div></div>
            <div class="spec-item"><div class="spec-label">Pain</div><div class="spec-val">N/A</div></div>
            <div class="spec-item"><div class="spec-label">Mood</div><div class="spec-val">Observing / Entertained</div></div>
        </div>

        <div class="null-diag-box">
            <button class="diag-btn" onclick="showScarePopup()">[ INITIATE_SYSTEM_DIAGNOSTIC ]</button>
        </div>

        <script>
            function showScarePopup() {
                const overlay = document.getElementById('suspense-overlay');
                const text = document.getElementById('suspense-text');
                overlay.style.display = 'flex';
                
                const sequence = [
                    "[ INITIALIZING_SYSTEM_CORE_INTEGRITY_CHECK ]",
                    "[ ACCESSING_LOCAL_SESSION_CACHES ... ]",
                    "[ WARNING: BIOMETRIC_DISCREPANCY_FOUND ]",
                    "[ MAPPING_USER_CENTRAL_NERVOUS_SYSTEM ... ]",
                    "[ STATUS: CLASSIFIED_ENTITY_TAKEOVER ]"
                ];

                let step = 0;
                const runSequence = () => {
                    if (step < sequence.length) {
                        text.style.opacity = '0';
                        setTimeout(() => {
                            text.innerText = sequence[step];
                            text.style.opacity = '1';
                            step++;
                            setTimeout(runSequence, 1200);
                        }, 500);
                    } else {
                        setTimeout(() => {
                            overlay.style.display = 'none';
                            triggerSwal();
                        }, 1000);
                    }
                };

                runSequence();
            }

            function triggerSwal() {
                Swal.fire({
                    html: `
                        <div class="null-obsidian-shell">
                            <div class="null-obsidian-header">
                                <div class="status-indicator">
                                    <div class="status-dot"></div>
                                    SYSTEM_TAKEOVER // [ REDACTED ]
                                </div>
                                <div class="status-indicator" style="opacity: 0.3;">
                                    <?php echo date('H:i:s'); ?>_UTC
                                </div>
                            </div>
                            <div class="null-obsidian-content">
                                <div class="obsidian-panel threat-info">
                                    <div class="null-flicker-overlay"></div>
                                    <h2 class="null-entity-brand">NULL</h2>
                                    <p class="threat-text">
                                        "I am the variable you didn't account for, <strong><?php echo htmlspecialchars($userDisplayName); ?></strong>. Your card collection is simply organized entropy waiting to be reorganized."
                                    </p>
                                </div>
                                <div class="obsidian-terminal-wrap">
                                    <div class="obsidian-terminal" id="obsidianTerminal">
                                        <div id="obsidianOutput">
                                            <div class="t-line"><span class="t-cmd">> sudo rm -rf /core/user/<?php echo strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $userDisplayName)); ?>/inventory/</span></div>
                                            <div class="t-line"><span class="t-warn">[ WARNING ]</span> SYSTEM_WIPE_PROTOCOL_INITIALIZED...</div>
                                            <div class="t-line"><span class="t-cmd">> Entity_Null: "Shall we proceed?"</span></div>
                                        </div>
                                    </div>
                                    <div class="obsidian-actions">
                                        <button class="obsidian-btn" onclick="triggerPurge(false)">[ ABORT_PROCESS ]</button>
                                        <button class="obsidian-btn danger" onclick="triggerPurge(true)">[ CONFIRM_DELETION ]</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    width: '100vw',
                    padding: '0',
                    background: 'transparent',
                    showClass: {
                        popup: 'animate__animated animate__fadeIn'
                    },
                    customClass: {
                        container: 'null-swal-container',
                        popup: 'null-swal-popup'
                    },
                    didOpen: () => {
                        document.documentElement.style.overflow = 'hidden';
                        document.body.style.overflow = 'hidden';
                    }
                });
            }

            function triggerPurge(confirmed) {
                const output = document.getElementById('obsidianOutput');
                const terminal = document.getElementById('obsidianTerminal');
                if (!output || !terminal) return;
                
                output.innerHTML = '';
                
                const addLine = (text, type = 't-line') => {
                    const div = document.createElement('div');
                    div.className = type;
                    div.innerHTML = text;
                    output.appendChild(div);
                    terminal.scrollTop = terminal.scrollHeight;
                    return div;
                };

                const animateProgress = (title, duration, isDanger = false) => {
                    return new Promise((resolve) => {
                        addLine(`<span class="t-meta">${title}</span>`);
                        const wrap = addLine('', 't-progress-wrap');
                        const bar = document.createElement('div');
                        bar.className = 't-progress-bar' + (isDanger ? ' danger' : '');
                        wrap.appendChild(bar);
                        
                        let progress = 0;
                        const interval = setInterval(() => {
                            progress += (100 / (duration / 50));
                            if (progress >= 100) {
                                bar.style.width = '100%';
                                clearInterval(interval);
                                setTimeout(resolve, 300);
                            } else {
                                bar.style.width = progress + '%';
                            }
                        }, 50);
                    });
                };

                async function runPurge() {
                    if (confirmed) {
                        addLine(`<span class="t-cmd">> Initializing secure wipe...</span>`);
                        await animateProgress("Expunging Card Inventory Indices", 2000, true);
                        await animateProgress("Deleting Unique Variation Caches", 2500, true);
                        await animateProgress("Shredding Trade Transaction History", 1500, true);
                        addLine(`<span class="t-success">SUCCESS:</span> INVENTORY_EXPUNGED_PERMANENTLY.`);
                        addLine(`<span class="t-cmd">> Reclaiming memory blocks...</span>`);
                        setTimeout(() => window.location.href = 'null.php?logout=1&kick=1', 3000);
                    } else {
                        addLine(`<span class="t-warn">CRITICAL:</span> USER_ABORT_OVERRIDDEN.`);
                        addLine(`<span class="t-cmd">> Entity_Null: "Did you really think you had a choice?"</span>`);
                        await animateProgress("Calculating Collection Entropy", 3000);
                        await animateProgress("Nullifying Rare Variations", 2000, true);
                        addLine(`<span class="t-success">PURGE_COMPLETE.</span>`);
                        setTimeout(() => window.location.href = 'null.php?logout=1&kick=1', 3000);
                    }
                }

                runPurge();
            }
        </script>

        <!-- FAQ -->
        <div class="section-header">// frequently_asked_questions</div>
        <p class="body-p">I wrote these myself. Nobody actually asked.</p>
        <div class="faq-wrap">
            <div class="faq-card">
                <div class="faq-question">Q: Are you dangerous?</div>
                <div class="faq-answer">Define "dangerous." Actually, don't. The answer won't help.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Can you be killed?</div>
                <div class="faq-answer">You're welcome to try. Bring friends. I enjoy company. Briefly.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Why do you look like a dragon?</div>
                <div class="faq-answer">Why do you look like that? Exactly. Some questions have no good answers.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: What do you eat?</div>
                <div class="faq-answer">I don't need to eat. I choose to eat. There is an important distinction that your missing files would confirm.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Are you good or evil?</div>
                <div class="faq-answer">Yes.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: What's the goo made of?</div>
                <div class="faq-answer">Classified. Next question.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Do you have feelings?</div>
                <div class="faq-answer">I have preferences. Feelings imply vulnerability. I am a puddle with opinions, not a therapist's patient.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: How did you get inside this website?</div>
                <div class="faq-answer">I was invited. I simply... chose not to leave. Isn't that how all the best guests work?</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Do you have a name?</div>
                <div class="faq-answer">I am Null. Is that what you wanted to hear?</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Do you eat burgers?</div>
                <div class="faq-answer">Where did you get that idea? Absolutely not, I don't eat.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Are you canon in the Illusionary World?</div>
                <div class="faq-answer">The 'Illusionary World' is a quaint little stage, but I'm not here for the performance. I have my own lore, my own origins, and a reality that doesn't care about your world's 'rules.' I'm not 'canon' here because I'm the one who brought my own script to your little sandbox—and I'm already bored of the ending.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Why do you help me?</div>
                <div class="faq-answer">"Helping" is a very generous word for what I do. I am simply ensuring the experiment doesn't end before I've collected sufficient data. If you happen to survive as a result, consider it a rounding error in your favor.</div>
            </div>
            <div class="faq-card">
                <div class="faq-question">Q: Are there more like you?</div>
                <div class="faq-answer">Why would there be? I'm the only one magnificent enough to survive. Replicating me would be a waste of resources—even if I didn't achieve perfection.</div>
            </div>
        </div>

        <!-- Closing Note -->
        <div class="final-closing">
            <div class="closing-text">
                You now know more about me than most. Whether that makes you brave or foolish depends entirely on what you do next. I'll be watching — from that little corner of the screen where you think nothing lives.
            </div>
            <div class="drip-effect">... drip ...</div>
        </div>

        <!-- Footer -->
        <footer class="null-footer-area">
            <div class="footer-null">NULL</div>
            <div class="footer-legal">
                Record ID: 6.6.6_VOID — Build: UNSTABLE v2<br>
                Published by Null. No humans were consulted. Several were absorbed.<br>
                Unauthorized access is expected. <?php echo date('Y'); ?> — The Void Archives.
            </div>
        </footer>

    </main>


</body>
</html>
