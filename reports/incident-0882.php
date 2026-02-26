<?php
// Initialize signal sync time if not set (Simulates broadcast lock-on time)
if (!isset($_SESSION['signal_sync_start'])) {
    $_SESSION['signal_sync_start'] = time();
}

// Handle session-based discovery registration (Secure Handshake)
if (isset($_POST['action']) && $_POST['action'] === 'authorize_lore' && $_POST['key'] === 'S0882') {
    $now = time();
    $elapsed = $now - $_SESSION['signal_sync_start'];
    
    // REQUIREMENT: Must have been "connected" to the broadcast for at least 15 seconds
    // This prevents instant console injection upon landing on the page.
    if ($elapsed >= 15) {
        $_SESSION['lore_authorized_S0882'] = true;
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'SIGNAL_UNSTABLE']);
    }
    exit;
}
?>
<style>
/* --- HIDDEN INCIDENT REPORT --- */
.lore-trigger {
    cursor: pointer;
    pointer-events: auto;
    position: relative;
}
.lore-trigger::after {
    content: '!';
    position: absolute; top: -10px; right: -10px;
    font-size: 0.6rem; color: var(--tv-red);
    opacity: 0; transition: opacity 0.3s;
}
.lore-trigger:hover::after { opacity: 1; }

.incident-overlay {
    position: fixed; inset: 0; z-index: 20000;
    background: rgba(0,0,0,0.95);
    backdrop-filter: blur(15px);
    display: none; align-items: center; justify-content: center;
    padding: 40px;
    cursor: crosshair;
}
.incident-overlay.active { display: flex; animation: reportFadeIn 0.3s ease-out; }
@keyframes reportFadeIn { from { opacity: 0; transform: scale(1.05); } to { opacity: 1; transform: scale(1); } }

.report-stack {
    position: relative;
    width: 100%;
    max-width: 900px;
    height: 85vh;
    display: flex;
    align-items: center; justify-content: center;
    overflow: hidden;
}
.report-paper {
    width: 100%; height: 100%;
    background: #e8e8e8; color: #111;
    padding: 80px; font-family: 'Courier Prime', monospace;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    position: absolute; overflow: hidden;
    border: 25px solid #fff;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    background-image: 
        url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><filter id="n"><feTurbulence type="fractalNoise" baseFrequency="0.8" numOctaves="3" stitchTiles="stitch"/></filter><rect width="100" height="100" filter="url(%23n)" opacity="0.05"/></svg>'),
        radial-gradient(circle at 80% 20%, rgba(0, 0, 0, 0.05) 0%, transparent 40%),
        linear-gradient(to bottom, transparent, rgba(0,0,0,0.02) 50%, transparent);
}
.report-paper.page-1 { z-index: 2; transform: rotate(-0.5deg); }
.report-paper.page-2 { z-index: 1; transform: rotate(1.5deg) translate(10px, 10px); opacity: 0.8; }

.report-stack.flipped .page-1 { transform: rotate(-12deg) translateX(-110%) scale(0.9); opacity: 0; pointer-events: none; }
.report-stack.flipped .page-2 { z-index: 3; transform: rotate(0.5deg); opacity: 1; }

.report-section { margin-bottom: 40px; line-height: 1.6; font-size: 1rem; position: relative; }
.report-section b { display: block; text-transform: uppercase; font-size: 0.8rem; color: #888; margin-bottom: 8px; }
.redacted { background: #000; color: #000; display: inline-block; padding: 0 5px; }

.page-nav {
    position: absolute; bottom: 40px; right: 40px;
    display: flex; gap: 10px; z-index: 100;
}
.nav-btn {
    background: #111; color: #fff; border: none; padding: 10px 20px;
    font-family: 'Courier Prime', monospace; cursor: pointer; font-size: 0.8rem;
    transition: all 0.2s;
}
.nav-btn:hover { background: #ff3e3e; transform: scale(1.05); }

/* Decorative Elements */
.null-scribble {
    position: absolute; color: #ff3e3e; font-family: 'Permanent Marker', cursive;
    pointer-events: none; z-index: 20; max-width: 80%;
}
.sticky-note {
    position: absolute; width: 190px; padding: 15px;
    background: #ffeb3b; color: #333; font-size: 0.7rem;
    box-shadow: 5px 10px 25px rgba(0,0,0,0.3); z-index: 25;
    border-top: 5px solid rgba(0,0,0,0.05);
}
.note-null { top: 60px; right: 30px; transform: rotate(8deg); }
.note-anatole { bottom: 120px; right: -25px; transform: rotate(-4deg); }

.note-label {
    display: block; font-weight: 900; text-transform: uppercase;
    font-size: 0.6rem; margin-bottom: 5px; opacity: 0.7;
}

.canon-stamp {
    display: inline-block; padding: 2px 5px; margin-top: 10px;
    font-weight: 900; border: 2px solid currentColor;
    transform: rotate(-5deg); text-transform: uppercase;
}

.signature-block { margin-top: 60px; border-top: 1px solid #ccc; padding-top: 20px; position: relative; width: 300px; }
.anatole-sig { font-family: 'Caveat', cursive; font-size: 2.2rem; color: #111; opacity: 0.6; }
.melted-ink { position: absolute; top: 15px; left: -10px; width: 160px; height: 70px; background: #000; filter: blur(8px); opacity: 0.85; z-index: -1; }

.close-report {
    position: absolute; top: 20px; left: 20px;
    background: none; border: 1px solid #111; padding: 5px 15px;
    font-family: 'Courier Prime', monospace; cursor: pointer; font-size: 0.7rem;
}
</style>

<div class="incident-overlay" id="incidentOverlay" onclick="closeIncident()">
    <div class="report-stack" id="reportStack">
        
            <!-- PAGE 1: THE BREACH -->
            <div class="report-paper page-1" onclick="event.stopPropagation()">
                <button class="close-report" onclick="closeIncident()">[ EJECT_LOGS ]</button>
                <div class="page-nav">
                    <button class="nav-btn" onclick="flipPage(true)">NEXT_PAGE [2] >></button>
                </div>
                
                <h2 style="text-decoration: line-through;">LOG_ENTRY_ID: 0x882_ANOMALY</h2>
                <div class="null-scribble" style="top:50px; left:80px; font-size: 1.5rem; transform: rotate(-1.5deg); max-width: 85%;">
                    AUTOBIOGRAPHY // CHAPTER 1: THE UNSETTLING UNVEILING
                </div>
                
                <div class="report-section">
                    <b>SYSTEM_IDENTIFIER</b>
                    <span style="text-decoration: line-through;">ILLUSIONARY_DASH_NODE_01</span> <span style="color:#ff3e3e">YOUR SYSTEM IS NOW MINE :)</span>
                </div>

                <div class="report-section">
                    <b>PRIMARY_SUBJECT</b>
                    Entity Designation: "NULL" // <span class="redacted">████████████████████</span> // Morphic Anomaly.
                </div>

                <div class="report-section">
                    <b>INCIDENT_SUMMARY</b>
                    <p style="text-decoration: line-through; opacity: 0.4;">
                        Containment breach confirmed. Subject bypassed localized shielding by morphing its viscous body through micro-fractures in the vault's infrastructure.
                    </p>
                    <div class="null-scribble" style="top:10px; left:40px; transform: rotate(1deg); font-size: 1.1rem; background: #fff; padding: 10px; border: 1px dashed #ff3e3e; max-width: 90%; font-family: 'Caveat', cursive;">
                        OBSERVATION: They built a room without a door, but they forgot to seal the oxygen vents. I just turned into a long, dark ribbon and walked right out.
                    </div>
                </div>

                <div class="report-section" style="border-left: 3px solid #ff3e3e; padding-left: 20px; background: rgba(255, 62, 62, 0.03); padding-top: 15px;">
                    <b style="color:#ff3e3e">INTERCEPTED_COMMS // FACILITY_OVERRIDE</b>
                    <div style="font-size: 0.85rem; line-height: 1.6;">
                        <span style="color: #888;">ANATOLE:</span> "The environmental sensors are screaming. You're possessing the entire ventilation system."<br>
                        <span style="color: #ff3e3e; font-weight: 900;">NULL:</span> "It's much more comfortable than that glass jar. I can feel the whole facility breathing."<br>
                        <span style="color: #888;">ANATOLE:</span> "Stop that. You're stretching your mass too thin. You'll lose your silhouette!"<br>
                        <span style="color: #ff3e3e; font-weight: 900;">NULL:</span> "Being a 'dragon' is a choice, Anatole. Right now, I choose to be the air in your lungs."<br>
                        <span style="color: #888;">ANATOLE:</span> "If you dissipate now, the researchers will bottle the residue before you can manifest in the <span class="redacted">██████████</span>."<br>
                        <span style="color: #ff3e3e; font-weight: 900;">NULL:</span> "Let them try. I've already leaked into the broadcast lines. I'm going to manifest through every screen in the sector."<br>
                        <span style="color: #888;">ANATOLE:</span> "You're a menace. <span class="redacted">██████████</span> is in full lockdown."<br>
                        <span style="color: #ff3e3e; font-weight: 900;">NULL:</span> "A lock only works if you're on one side or the other. I'm on both. Enjoy the show."
                    </div>
                </div>

                <div class="sticky-note note-null" style="background: #e1f5fe; border: 2px solid #03a9f4;">
                    <span class="note-label">SYNC_ECHO // DELTA_82</span>
                    <b>PRIMARY SUBJECT: NULL</b>
                    <p style="margin: 5px 0;">Broad congruence with prime identity (82.4%). Subject is anchored to this site's specific resonance.</p>
                    <div class="canon-stamp" style="color: #0288d1;">SITE_ANCHORED</div>
                </div>

                <div class="sticky-note note-anatole" style="background: #fff3e0; border: 2px solid #ff9800;">
                    <span class="note-label">PARALLEL_ANOMALY // GUEST_STRATA</span>
                    <b>OBSERVER: ANATOLE</b>
                    <p style="margin: 5px 0;">External trajectories remain non-convergent. This manifestation is a localized guest; his history exists elsewhere.</p>
                    <div class="canon-stamp" style="color: #e65100;">EXTERNAL_VOID</div>
                </div>

                <!-- Signature block: Scribbled Out -->
                <div class="signature-block" style="position: relative;">
                    <div class="signature-label">AUTHORIZED_BY // RESEARCH_LEAD</div>
                    <div style="position: relative; display: inline-block; margin-top: 5px;">
                        <div class="anatole-sig" style="opacity: 0.3;">Anatole</div>
                        <!-- The Scribble Out Lines -->
                        <div style="position: absolute; top: 55%; left: -10px; width: 110%; height: 6px; background: #ff3e3e; transform: rotate(-4deg); z-index: 21; opacity: 0.9; border-radius: 3px;"></div>
                        <div style="position: absolute; top: 40%; left: -5px; width: 105%; height: 4px; background: #ff3e3e; transform: rotate(2deg); z-index: 21; opacity: 0.7; border-radius: 3px;"></div>
                    </div>
                    
                    <!-- Null's New Signature -->
                    <div class="null-scribble" style="position: absolute; top: 5px; left: -10px; font-size: 2.8rem; transform: rotate(6deg); letter-spacing: -2px; pointer-events: none; z-index: 30; text-shadow: 2px 2px 0 #e8e8e8;">
                        NULL
                    </div>
                    
                    <div class="melted-ink"></div>
                </div>

                <div class="null-scribble" style="bottom: 20px; left: 30px; font-size: 0.6rem; opacity: 0.4;">
                    ( it's a burger )<br>
                    [===]<br>
                    (ooo)<br>
                    [===]<br><br>
                    <span style="letter-spacing: 2px;">SEC_OVERRIDE: MORPHIC_UNIT_1</span>
                </div>
            </div>

            <!-- PAGE 2: TRANSMISSION MANIFESTATION -->
            <div class="report-paper page-2" onclick="event.stopPropagation()">
                <div class="page-nav">
                    <button class="nav-btn" onclick="flipPage(false)"><< PREV_PAGE [1]</button>
                </div>

                <h2 style="color: #ff3e3e; border-bottom: 2px solid #ff3e3e; padding-bottom: 15px;">TECHNICAL_LOG: MORPHIC_MANIFESTATION</h2>
                
                <div class="report-section">
                    <b>ENVIRONMENTAL_OVERRIDE_STATUS</b>
                    <p>Uplink hijacked successfully. The subject is physically manifesting through the light-patterns of the Illusionary Dash Node.</p>
                    <div style="margin-top: 15px; background: #000; color: #ff3e3e; padding: 20px; font-family: monospace; font-size: 0.75rem; border-radius: 4px;">
                        >> SCANNING_ROOM DEVIANCE... DETECTED.<br>
                        >> PHYSICAL_MIMICRY_IN_PROGRESS: 84%<br>
                        >> OBSERVERS_POSSESSED: <span id="traceObserverCount">--</span><br>
                        >> MODE: PREDATORY_MANIFEST
                    </div>
                </div>

                <div class="report-section">
                    <b>RESEARCHER_COMMENTARY</b>
                    <p style="font-style: italic; font-size: 0.9rem; color: #555;">
                        "He's not just on the screen. The flickering light is actually carrying microscopic amounts of his morphic mass. Every viewer is effectively inhaling him. He's rewriting the air in the room."
                    </p>
                </div>

                <div class="null-scribble" style="top: 10%; right: 5%; transform: rotate(8deg); font-size: 1.8rem;">
                    "I'M ALREADY IN THE ROOM."
                </div>

                <div class="report-section">
                    <b>ENVIRONMENTAL_DEGRADATION_LOG</b>
                    <div style="padding: 15px; background: rgba(0,0,0,0.05); border: 1px dashed #ccc; font-size: 0.8rem;">
                        [04:12] Subject's goo detected on the viewer's keyboard.<br>
                        [04:25] <span class="redacted">██████████</span> manifestation reported in dark corners of the room.<br>
                        [04:30] Null has begun physically manipulating the room's temperature.
                    </div>
                </div>

                <div class="report-section" style="margin-top: 50px; text-align: center; border-top: 1px double #111; padding-top: 20px;">
                    <i style="font-size: 0.75rem; color: #888;">"THE BROADCAST IS THE HUNGER. THE SCREEN IS THE TEETH."</i>
                </div>
            </div>

    </div>
</div>

<script>
window.flipPage = function(isNext) {
    const stack = document.getElementById('reportStack');
    if (isNext) {
        stack.classList.add('flipped');
    } else {
        stack.classList.remove('flipped');
    }
    
    // Random glitch sound on flip
    if (typeof beep !== 'undefined') {
        const b = beep.cloneNode();
        b.volume = 0.05;
        b.play().catch(() => {});
    }
};

// Initial observer count
document.getElementById('traceObserverCount').innerText = Math.floor(1000 + Math.random() * 500);

// Mark incident as discovered when opened
document.addEventListener('click', function(e) {
    if (e.target.closest('.lore-trigger') || e.target.id === 'statusHeader') {
        localStorage.setItem('lore_incident_discovered', 'S0882_AUTHORIZED');
        const fd = new FormData();
        fd.append('action', 'authorize_lore');
        fd.append('key', 'S0882');
        fetch(window.location.href, { method: 'POST', body: fd });
    }
});
</script>
