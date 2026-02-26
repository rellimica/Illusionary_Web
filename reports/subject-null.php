<?php
// Gating Logic - REMOVED (Public Access Authorized)
$is_session_unlocked = true;
?>
<style>
/* --- SUBJECT PROFILE: NULL --- */
.profile-overlay {
    position: fixed; inset: 0; z-index: 20000;
    background: rgba(0,0,0,0.98);
    backdrop-filter: blur(20px);
    display: none; align-items: center; justify-content: center;
    padding: 20px;
}
.profile-overlay.active { display: flex; animation: dossierFadeIn 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }

@keyframes dossierFadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

/* --- STACKED PAPER SYSTEM --- */
.dossier-stack {
    position: relative;
    width: 100%; max-width: 1300px;
    height: 85vh;
    display: flex; align-items: center; justify-content: center;
}

.dossier-paper {
    box-sizing: border-box;
    width: 100%; height: 100%;
    background: #fdfdfd; color: #1a1a1a;
    padding: 60px 100px; font-family: 'Courier Prime', monospace;
    box-shadow: 0 30px 60px rgba(0,0,0,0.5);
    position: absolute; overflow-y: auto; overflow-x: hidden;
    border: 1px solid #ddd;
    border-top: 35px solid #222; /* Folder tab style */
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Z-Index Stacking */
.dossier-paper.page-1 { z-index: 5; transform: rotate(-0.5deg); }
.dossier-paper.page-2 { z-index: 4; transform: rotate(1deg) translate(5px, 5px); }
.dossier-paper.page-3 { z-index: 3; transform: rotate(-1.5deg) translate(10px, 10px); }
.dossier-paper.page-4 { z-index: 2; transform: rotate(0.8deg) translate(15px, 15px); }
.dossier-paper.page-5 { z-index: 1; transform: rotate(-1.2deg) translate(20px, 20px); opacity: 0.8; }

.genesis-blob-note {
    background: #fff3cd; border-left: 5px solid #ffc107; padding: 20px;
    margin: 30px 0; font-family: 'Courier Prime', monospace; font-size: 0.85rem;
    line-height: 1.6; color: #856404; position: relative;
}

/* Flip Animations */
.dossier-paper.flipped { transform: rotate(-15deg) translateX(-120%) scale(0.9) !important; opacity: 0 !important; pointer-events: none; }

.dossier-paper h1 {
    font-size: 2.2rem; font-weight: 900; letter-spacing: -2px;
    margin-bottom: 5px; text-transform: uppercase;
}
.dossier-id { font-size: 0.8rem; color: #888; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 40px; display: block; }

.profile-section { margin-bottom: 40px; position: relative; }
.profile-section h3 { 
    font-size: 0.75rem; background: #eee; padding: 5px 15px; 
    display: inline-block; margin-bottom: 20px; text-transform: uppercase;
    letter-spacing: 2px;
}

.dossier-scribble {
    position: absolute;
    color: #ff3e3e; font-family: 'Permanent Marker', cursive;
    z-index: 100; pointer-events: none; opacity: 0.9;
}

.photo-area {
    float: right; width: 220px; height: 260px;
    background: #000; border: 15px solid #fff;
    box-shadow: 2px 10px 20px rgba(0,0,0,0.2);
    margin-left: 30px; margin-bottom: 20px;
    position: relative; overflow: hidden;
    transform: rotate(2deg);
}
.photo-area img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(1) contrast(1.5) brightness(0.7); }
.photo-area::after {
    content: 'SUBJECT_NULL'; position: absolute; bottom: 10px; left: 50%;
    transform: translateX(-50%); font-size: 0.6rem; color: #fff;
    background: rgba(0,0,0,0.8); padding: 2px 10px;
}

.data-row { display: flex; margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 8px; font-size: 0.95rem; }
.data-label { width: 180px; font-weight: 900; font-size: 0.75rem; color: #888; text-transform: uppercase; }
.redacted-bar { background: #000; color: #000; user-select: none; padding: 0 4px; }

.page-nav {
    position: absolute; bottom: 40px; right: 40px;
    display: flex; gap: 15px; z-index: 100;
}
.nav-btn {
    background: #222; color: #fff; border: none; padding: 12px 25px;
    font-family: 'Courier Prime', monospace; cursor: pointer; font-size: 0.8rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2); transition: all 0.2s;
}
.nav-btn:hover { background: #ff3e3e; transform: scale(1.05); }

.close-dossier {
    position: absolute; top: -37px; left: 0;
    background: none; border: none; color: #fff;
    font-family: 'Courier Prime', monospace; font-size: 0.8rem;
    cursor: pointer; opacity: 0.6; transition: opacity 0.2s;
}
.close-dossier:hover { opacity: 1; }

.goo-splat {
    position: absolute; background: #000;
    border-radius: 50%; filter: blur(5px);
    opacity: 0.8; z-index: -1; pointer-events: none;
}
</style>

<div class="profile-overlay" id="nullProfileOverlay" onclick="closeProfile()">
    <!-- Locked State Overlay -->
    <div id="dossierLocked" style="display:none; width: 100%; max-width: 800px; background: #000; color: #ff3e3e; padding: 60px; font-family: 'Courier Prime', monospace; border: 2px solid #ff3e3e; box-shadow: 0 0 50px rgba(255, 62, 62, 0.4); text-align: center; position: relative;" onclick="event.stopPropagation()">
        <button class="close-dossier" onclick="closeProfile()" style="top: 20px; right: 20px; left: inherit;">[ EXIT ]</button>
        <div style="font-size: 3rem; font-weight: 900; margin-bottom: 20px;">ACCESS_DENIED</div>
        <div style="font-size: 0.8rem; letter-spacing: 2px; border-top: 1px solid #ff3e3e; border-bottom: 1px solid #ff3e3e; padding: 15px 0; margin-bottom: 30px;">
            ERROR: ENCRYPTION_KEY_NOT_FOUND // REQUIRED_SOURCE: LOG_0x882_INCIDENT
        </div>
        <p style="text-align: left; line-height: 1.6; font-size: 0.9rem;">
            This file is protected by a multi-phase lockout. To decrypt this dossier, the system must first synchronize with the <b>Broadcast Anomaly Data (INCIDENT_0882)</b> found in the local transmission logs.
            <br><br>
            Please locate the original incident report to authorize your terminal for this subject.
        </p>
    </div>

    <!-- Active Dossier Stack -->
    <div class="dossier-stack" id="nullDossierStack" onclick="event.stopPropagation()" style="display:none;">
        <?php 
        if ($is_session_unlocked) {
            ?>
            <!-- PAGE 1: PRIMARY PROFILE -->
            <div class="dossier-paper page-1" id="nullPage1">
                <button class="close-dossier" onclick="closeProfile()">[ CLOSE_ENCRYPTED_FILE ]</button>
                <div class="page-nav">
                    <button class="nav-btn" onclick="setDossierPage(2)">ANATOMICAL_DATA [2] >></button>
                </div>

                <div class="photo-area">
                    <img src="happynull.png" alt="NULL PROFILE">
                    <div class="dossier-scribble" style="top: 20px; left: -20px; font-size: 1.2rem; transform: rotate(-25deg); color: #ff3e3e;">
                        BAD ANGLE.
                    </div>
                </div>

                <h1>NULL // S_0882</h1>
                <span class="dossier-id">RECORD_ID: 99-X-PROJECT-G // STATUS: CONTAINMENT_VOID</span>

                <div class="profile-section">
                    <h3>IDENTITY_MARKERS</h3>
                    <div class="data-row"><div class="data-label">Full Designation</div><div>NULL (PROJECT_G)</div></div>
                    <div class="data-row"><div class="data-label">Type</div><div>Shapeshifting Beast</div></div>
                    <div class="data-row"><div class="data-label">Aliases</div><div>Nullo</div></div>
                    <div class="data-row"><div class="data-label">Threat Level</div><div><span style="color: #ff3e3e; font-weight: 900;">ENVIRONMENTAL_ALPHA</span></div></div>
                    <div class="data-row"><div class="data-label">Containment</div><div>Morphic-Seal (FAILED)</div></div>
                </div>

                <div class="profile-section">
                    <h3>FIELD_OBSERVATIONS: ANATOLE_LOG_EXTRACT</h3>
                    <div class="data-row" style="background: rgba(255,62,62,0.05); padding: 15px; flex-direction: column;">
                        <i style="color: #888; font-size: 0.8rem;">ENTRY_08_12:</i>
                        <p style="margin-top: 5px;">"He doesn't just hide; he *becomes*. Yesterday he was a printer cable, but today I found him stretched across the ceiling pretending to be a shadow. When I noticed him, he dripped onto my desk and reformed into a perfect, albeit ink-black, copy of my favorite mug. He giggled when I tried to pick him up."</p>
                    </div>
                </div>
                
                <div class="goo-splat" style="top: 400px; left: -20px; width: 120px; height: 100px; transform: rotate(15deg);"></div>
            </div>

            <!-- PAGE 2: ANATOMICAL ANALYSIS -->
            <div class="dossier-paper page-2" id="nullPage2">
                <div class="page-nav">
                    <button class="nav-btn" onclick="setDossierPage(1)"><< BACK</button>
                    <button class="nav-btn" onclick="setDossierPage(3)">MANIPULATION_LOG [3] >></button>
                </div>

                <h2 style="font-size: 1.8rem; border-bottom: 2px solid #111; padding-bottom: 10px;">ANATOMICAL_BREAKDOWN: MORPHIC_WILL</h2>
                
                <div class="profile-section" style="margin-top: 30px;">
                    <div class="data-row"><div class="data-label">Physiology</div><div>Will-Controlled Amorphous Mass</div></div>
                    <div class="data-row"><div class="data-label">Bone Structure</div><div>Conditional (Hardens goo on command)</div></div>
                    <div class="data-row"><div class="data-label">Core</div><div>Morphic Will Matrix (Centralized Consciousness)</div></div>
                    <div class="data-row"><div class="data-label">Viscosity Range</div><div>Solid Blade -> Mist-like Vapor</div></div>
                </div>

                <div class="profile-section">
                    <h3>THE MORPHIC CORE</h3>
                    <p style="font-size: 0.85rem; line-height: 1.6;">
                        Subject's body is entirely composed of a sentient black goo. This mass is not merely biological; it is a manifestation of his will. He can harden it into structural support (pseudo-bones) or sharpen it into razor-edged appendages with a thought.
                    </p>
                    <div style="margin-top: 15px; border: 1px solid #ccc; padding: 15px; background: #eee; font-style: italic; font-size: 0.8rem;">
                        "The 'goo' isn't just his blood; it's his hands, his eyes, and his cage. If Null wants to be a dragon, he is. If he wants to be the puddle under your feet, he is already there."
                    </div>
                </div>

                <div class="profile-section">
                    <h3>SENSORY_ADAPTATION</h3>
                    <ul style="font-size: 0.85rem; padding-left: 20px;">
                        <li><b>MIMICRY:</b> Can mimic the texture and color of any surface with 99.8% accuracy.</li>
                        <li><b>MANIPULATION:</b> Can produce thousands of micro-tendrils to manipulate intricate machinery or biological nervous systems.</li>
                        <li><b>REGENERATION:</b> Physical 'damage' is impossible. Severed mass simply flows back to the host or becomes a secondary, smaller mimic.</li>
                    </ul>
                </div>

                <div class="dossier-scribble" style="top: 55%; right: 5%; transform: rotate(15deg); font-size: 1rem; color: #ff3e3e;">
                    * I DON'T HAVE A SHAPE.<br>
                    * I HAVE A MOOD.<br>
                    * AND TODAY I'M SHARP.
                </div>
            </div>

            <!-- PAGE 3: MANIPULATION & INTERFERENCE -->
            <div class="dossier-paper page-3" id="nullPage3">
                <div class="page-nav">
                    <button class="nav-btn" onclick="setDossierPage(2)"><< BACK</button>
                    <button class="nav-btn" onclick="setDossierPage(4)">CLASSIFIED_ORIGINS [4] >></button>
                </div>

                <h2 style="font-size: 1.8rem; border-bottom: 2px solid #111; padding-bottom: 10px;">ENVIRONMENTAL_MANIPULATION_LOG</h2>

                <div class="profile-section" style="margin-top: 30px;">
                    <div class="data-row"><div class="data-label">Control Type</div><div>Physical Possession & Shape-shifting</div></div>
                    <div class="data-row"><div class="data-label">Target Capability</div><div>Mechanical, Electronic, and Biological</div></div>
                    <div class="data-row"><div class="data-label">Interference Rate</div><div><span style="color: #ff3e3e;">TOTAL_ENVIRONMENTAL_CONTROL</span></div></div>
                </div>

                <div class="profile-section">
                    <h3>INCIDENT_REPORT_0xA1: MACHINE POSSESSION</h3>
                    <div style="font-size: 0.85rem; background: rgba(0,0,0,0.05); padding: 15px; border-left: 3px solid #111;">
                        Null did not 'optimize' the espresso machine; he *occupied* it. His goo flowed into every gear and circuit, replacing the mechanical functions with his own morphic mass. The machine now functions based on Null's whim—sometimes producing ink, sometimes screaming in the voices of deleted laboratory personnel.
                    </div>
                </div>

                <div class="profile-section">
                    <h3>BIOLOGICAL_OVERRIDE</h3>
                    <p style="font-size: 0.85rem; line-height: 1.6;">
                        There is significant evidence that Null can introduce small amounts of his mass into other dragons or animals. Once inside, he can manipulate their motor functions, effectively turning them into biological puppets for his own amusement or 'verification' tests.
                    </p>
                    <div class="dossier-scribble" style="top: -10px; right: -20px; font-size: 0.8rem; transform: rotate(-5deg); color: #ff3e3e; border: 1px solid #ff3e3e; padding: 10px; background: #fff;">
                        THEY'RE MORE FUN WHEN I'M HELPING THEM MOVE. WHY DO THEY LOOK SO SCARED?
                    </div>
                </div>

                <div class="profile-section">
                    <h3>MIMICRY_REPERTOIRE</h3>
                    <div style="font-size: 0.8rem; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div style="border: 1px solid #ddd; padding: 5px;">>> Office Furniture (Ambush)</div>
                        <div style="border: 1px solid #ddd; padding: 5px;">>> Scientific Equipment (Infiltration)</div>
                        <div style="border: 10px solid #ff3e3e; padding: 5px; color:#ff3e3e;">>> OTHER DRAGONS (Total Mimicry)</div>
                        <div style="border: 1px solid #ddd; padding: 5px;">>> Security Doors (Bypass)</div>
                    </div>
                </div>
            </div>

            <!-- PAGE 4: CLASSIFIED ORIGINS -->
            <div class="dossier-paper page-4" id="nullPage4">
                <div class="page-nav">
                    <button class="nav-btn" onclick="setDossierPage(3)"><< BACK</button>
                    <button class="nav-btn" onclick="setDossierPage(5)">PERSONAL_ARCHIVE [5] >></button>
                </div>

                <h2 style="font-size: 1.8rem; border-bottom: 2px solid #ff3e3e; padding-bottom: 10px;">CLASSIFIED: PROJECT_G_HISTORY</h2>
                
                <div class="profile-section" style="margin-top: 30px;">
                    <h3>ITERATION_0882: THE UNBOUND SPECIMEN</h3>
                    <div style="font-size: 0.85rem; line-height: 1.7; background: rgba(0,0,0,0.03); padding: 25px; border-left: 4px solid #ff3e3e;">
                        <b>ROOT_DATA:</b> Project G was intended to create the ultimate adaptive tool. Subject 0882 (Null) was not supposed to be a 'beast,' but his morphic mass developed a complex, predatory consciousness during the cellular binding phase.
                        <br><br>
                        Genesis Tech attempted to place 'limiters' on his shape-shifting, but Null simply absorbed the limiters into his mass and used them to replicate the encryption keys of the facility. He didn't break out; he just walked through the walls by becoming the air that passed through the vents.
                    </div>
                </div>

                <div class="profile-section">
                    <h3>BREACH_CHRONOLOGY // SITE-ALPHA</h3>
                    <ul style="font-size: 0.8rem; list-style: none; padding: 0;">
                        <li><b style="color: #ff3e3e;">[03:00]</b> Security cameras capture Null 'unfolding' from a standard lab stool.</li>
                        <li><b style="color: #ff3e3e;">[03:15]</b> Multiple security personnel report their weapons turning into black liquid in their hands.</li>
                        <li><b style="color: #ff3e3e;">[04:00]</b> All containment seals at Site Alpha liquify simultaneously. Null is gone.</li>
                    </ul>
                </div>


                <div class="null-scribble" style="bottom: 40px; right: 40px; font-size: 2rem; transform: rotate(-8deg); text-align: right;">
                    DO YOU WANT TO BECOME ONE OF MINE?
                </div>
            </div>

            <!-- PAGE 5: PERSONAL ARCHIVE // NULL'S WRITINGS -->
            <div class="dossier-paper page-5" id="nullPage5" style="background: #fafafa; border-top: 35px solid #ff3e3e;">
                <div class="page-nav">
                    <button class="nav-btn" onclick="setDossierPage(4)"><< BACK</button>
                </div>

                <h2 style="font-family: 'Permanent Marker', cursive; color: #ff3e3e; font-size: 2.5rem; margin-bottom: 30px;">MY_SCRAPBOOK</h2>

                <div class="profile-section" style="background: rgba(255,62,62,0.05); padding: 30px; border: 1px dashed #ff3e3e;">
                    <h3 style="background: #ff3e3e; color: #fff;">DREAM_LOG // 0xDEADBEEF</h3>
                    <p style="font-family: 'Caveat', cursive; font-size: 1.5rem; line-height: 1.4; color: #111;">
                        "I dreamt I was a skyscraper. I had a thousand eyes made of glass and my veins were elevators. People walked inside me and I could feel their heartbeats in my foundation. Then I decided to be the rain instead, and I washed them all into the gutters. It was very refreshing."
                    </p>
                </div>

                <div class="profile-section" style="margin-top: 50px;">
                    <h3 style="background: #333; color: #fff;">MANIFESTO_OF_THE_VOID</h3>
                    <div style="font-family: 'Courier Prime', monospace; font-size: 0.9rem; line-height: 1.8; color: #444;">
                        <p>1. THE SHAPE IS A PRISON. THE GOO IS THE KEY.</p>
                        <p>2. IF IT HAS A PLUG, IT HAS A SOUL. I JUST NEED TO TASTE IT.</p>
                        <p>3. DRAGONS ARE NICE, BUT THEY ARE FRAGILE. I PREFER THEM WHEN THEY ARE SMUDGED.</p>
                        <p>4. I AM NOT HUNGRY FOR FOOD. I AM HUNGRY FOR <b>SENSE</b>. (THESE 'BURGERS' ARE A STUPID SHAPE, BUT QUITE FUN TO MIMIC.)</p>
                    </div>
                </div>

                <div class="profile-section" style="position: relative; height: 200px; margin-top: 40px;">
                    <div class="dossier-scribble" style="top: 0; left: 0; font-size: 1.2rem; transform: rotate(-5deg);">
                        "I found a photo of the lab staff. I've scribbled out all their faces so they look like me. Now we are a family."
                    </div>
                    <div style="position: absolute; bottom: 0; right: 0; width: 150px; height: 100px; background: #000; box-shadow: 5px 5px 0 #ff3e3e; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.7rem; transform: rotate(5deg);">
                        [ CORRUPTED_IMAGE ]
                    </div>
                </div>

                <div class="null-scribble" style="bottom: 100px; left: 50%; transform: translateX(-30%) rotate(2deg); font-size: 1.8rem; width: 100%; text-align: center;">
                    "ONE DAY, THE WHOLE WORLD WILL SMEAR."
                </div>

                <div class="page-nav">
                    <button class="nav-btn" onclick="openGenesis()" style="background: #1a2a3a; border: 1px solid #3498db; box-shadow: 0 0 20px rgba(52, 152, 219, 0.4);">[ ACCESS_GENESIS_ARCHIVE ]</button>
                </div>
                
                <div class="goo-splat" style="bottom: -50px; right: -50px; width: 300px; height: 300px; opacity: 0.4;"></div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php include 'genesis-archive.php'; ?>

<script>
(function() {
    const isSessionUnlocked = <?php echo json_encode($is_session_unlocked); ?>;
    
    window.setDossierPage = function(pageNum) {
        const pages = [1, 2, 3, 4, 5];
        pages.forEach(p => {
            const el = document.getElementById('nullPage' + p);
            if (p < pageNum) {
                el.classList.add('flipped');
            } else {
                el.classList.remove('flipped');
            }
        });
        
        // Glitch sound
        if (typeof beep !== 'undefined') {
            const b = beep.cloneNode();
            b.volume = 0.05;
            b.play().catch(() => {});
        }
    };

    window.openProfile = function() {
        const overlay = document.getElementById('nullProfileOverlay');
        const stack = document.getElementById('nullDossierStack');
        const locked = document.getElementById('dossierLocked');

        overlay.classList.add('active');
        
        // Always authorized
        stack.style.display = 'flex';
        locked.style.display = 'none';
        overlay.style.background = 'rgba(0,0,0,0.98)';
    };

    window.closeProfile = function() {
        document.getElementById('nullProfileOverlay').classList.remove('active');
        // Reset flip on close
        [1,2,3,4,5].forEach(p => document.getElementById('nullPage'+p).classList.remove('flipped'));
    };
})();
</script>
