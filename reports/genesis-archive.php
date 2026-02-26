<?php
// Gating Logic - REMOVED (Public Access Authorized)
$null_dossier_unlocked = true;
?>
<style>
/* --- GENESIS CORP ARCHIVE --- */
.genesis-overlay {
    position: fixed; inset: 0; z-index: 21000;
    background: rgba(10, 15, 25, 0.98);
    backdrop-filter: blur(25px);
    display: none; align-items: center; justify-content: center;
    padding: 20px;
}
.genesis-overlay.active { display: flex; animation: genesisFadeIn 0.5s ease-out; }

@keyframes genesisFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

.genesis-paper {
    box-sizing: border-box;
    width: 95%; max-width: 1200px; height: 85vh;
    background: #f4f1ea; /* Aged paper base */
    color: #2c3e50;
    padding: 80px 120px; font-family: 'Outfit', sans-serif;
    box-shadow: 0 40px 100px rgba(0,0,0,0.8);
    position: relative; overflow-y: auto;
    border-left: 60px solid #1a2a3a; /* Corporate binding */
    
    /* Layered Textures: Aged Gradient + Grain + Scorched Edges */
    background-image: 
        radial-gradient(circle at 50% 50%, transparent 60%, rgba(139, 69, 19, 0.05) 100%), /* Scorched edges */
        url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><filter id="n"><feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/></filter><rect width="100%" height="100%" filter="url(%23n)" opacity="0.04"/></svg>'),
        linear-gradient(135deg, #f4f1ea 0%, #e8e4d9 100%);
}

.fold-mark {
    position: absolute;
    background: rgba(0,0,0,0.03);
    pointer-events: none;
    z-index: 6;
}
.fold-h { left: 0; width: 100%; height: 1px; box-shadow: 0 0 5px rgba(0,0,0,0.05); }
.fold-v { top: 0; height: 100%; width: 1px; box-shadow: 0 0 5px rgba(0,0,0,0.05); }

.redacted-bar {
    background: #1a2a3a;
    color: transparent !important;
    user-select: none;
    padding: 0 2px;
}

/* Dust / Mould Spots */
.dust-spot {
    position: absolute;
    background: #333;
    border-radius: 50%;
    opacity: 0.2;
    pointer-events: none;
    z-index: 5;
}


/* Coffee Stains */
.coffee-stain {
    position: absolute;
    border-radius: 50%;
    filter: blur(15px) contrast(1.2);
    background: radial-gradient(circle, rgba(101, 67, 33, 0.25) 0%, rgba(101, 67, 33, 0.1) 60%, transparent 100%);
    pointer-events: none;
    z-index: 5;
}
.stain-1 { top: 10%; right: 15%; width: 300px; height: 220px; transform: rotate(-10deg); }
.stain-2 { bottom: 20%; left: 10%; width: 250px; height: 180px; transform: rotate(15deg); opacity: 0.7; }

.coffee-ring {
    position: absolute;
    width: 140px; height: 140px;
    border: 4px solid rgba(101, 67, 33, 0.12);
    border-radius: 50%;
    filter: blur(2px);
    pointer-events: none;
    z-index: 5;
}
.ring-1 { top: 25%; left: 20%; transform: rotate(10deg); }
.ring-2 { bottom: 15%; right: 25%; transform: rotate(-5deg); width: 120px; height: 120px; }

/* Ink Splatters */
.ink-splatter {
    position: absolute;
    background: #1a2a3a;
    border-radius: 50%;
    filter: blur(1px) contrast(1.5);
    opacity: 0.15;
    pointer-events: none;
    z-index: 5;
}
.ink-1 { top: 40%; left: 5%; width: 15px; height: 15px; }
.ink-2 { top: 42%; left: 6%; width: 8px; height: 8px; }
.ink-3 { top: 39%; left: 4.5%; width: 5px; height: 5px; }

/* Grease / Water Spots */
.grease-spot {
    position: absolute;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; /* Irregular shape */
    filter: blur(20px);
    pointer-events: none;
    z-index: 5;
}
.grease-1 { top: 70%; right: 10%; width: 150px; height: 120px; transform: rotate(45deg); }
.grease-2 { top: 30%; left: 40%; width: 100px; height: 80px; opacity: 0.3; }


.genesis-paper::before {
    content: 'GENESIS_TECH_INTERNAL';
    position: absolute; top: 0; left: -60px;
    height: 100%; width: 60px;
    writing-mode: vertical-rl; text-orientation: mixed;
    color: #4a6a8a; font-size: 0.7rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    letter-spacing: 5px; opacity: 0.6;
}

.genesis-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    border-bottom: 3px solid #1a2a3a; padding-bottom: 30px; margin-bottom: 50px;
}

.genesis-logo {
    text-align: left;
}
.genesis-logo .corp-name { font-size: 1.8rem; font-weight: 900; letter-spacing: -1px; color: #1a2a3a; line-height: 1; }
.genesis-logo .slogan { font-size: 0.6rem; letter-spacing: 4px; color: #3498db; text-transform: uppercase; margin-top: 5px; }

.genesis-stamp {
    border: 3px double #e74c3c; color: #e74c3c;
    padding: 10px 20px; font-weight: 900; font-size: 1.2rem;
    transform: rotate(-10deg); opacity: 0.8;
}

.genesis-section { margin-bottom: 40px; }
.genesis-section h3 { font-size: 0.8rem; background: #f0f4f8; padding: 8px 15px; color: #2c3e50; border-left: 4px solid #3498db; margin-bottom: 20px; text-transform: uppercase; }

.genesis-data-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
.genesis-data-item { border-bottom: 1px solid #eee; padding-bottom: 15px; }
.genesis-data-item label { display: block; font-size: 0.65rem; color: #95a5a6; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
.genesis-data-item span { font-size: 1rem; color: #1a2a3a; font-weight: 500; }

.genesis-blob-note {
    background: #fff3cd; border-left: 5px solid #ffc107; padding: 20px;
    margin: 30px 0; font-family: 'Courier Prime', monospace; font-size: 0.85rem;
    line-height: 1.6; color: #856404; position: relative;
}

.genesis-close {
    position: absolute; top: 20px; right: 20px;
    background: none; border: 1px solid #1a2a3a; padding: 8px 20px;
    font-size: 0.7rem; font-weight: 900; letter-spacing: 1px;
    cursor: pointer; transition: all 0.3s;
}
.genesis-close:hover { background: #1a2a3a; color: #fff; }

/* Corrupted Overlay */
.corrupted-text { color: #e74c3c; font-family: 'Permanent Marker', cursive; text-decoration: line-through; }

@media (max-width: 800px) {
    .genesis-paper { padding: 40px; border-left: none; }
    .genesis-paper::before { display: none; }
}
.null-scribble {
    position: absolute;
    font-family: 'Permanent Marker', cursive;
    color: #e74c3c;
    pointer-events: none;
    z-index: 100;
    opacity: 0.9;
    font-size: 1.4rem;
}
</style>

<div class="genesis-overlay" id="genesisOverlay" onclick="closeGenesis()">
    <div class="genesis-paper" id="genesisPaper" onclick="event.stopPropagation()">
        <!-- Obscurity Elements -->
        <div class="fold-mark fold-h" style="top: 33%;"></div>
        <div class="fold-mark fold-h" style="top: 66%;"></div>
        <div class="fold-mark fold-v" style="left: 50%;"></div>
        <div class="dust-spot" style="top: 15%; left: 45%; width: 2px; height: 2px;"></div>
        <div class="dust-spot" style="top: 60%; left: 80%; width: 3px; height: 3px;"></div>
        <div class="dust-spot" style="top: 85%; left: 20%; width: 2px; height: 2px;"></div>
        
        <div class="coffee-stain stain-1"></div>
        <div class="coffee-stain stain-2"></div>
        <div class="coffee-ring ring-1"></div>
        <div class="coffee-ring ring-2"></div>
        <div class="ink-splatter ink-1"></div>
        <div class="ink-splatter ink-2"></div>
        <div class="ink-splatter ink-3"></div>
        <div class="grease-spot grease-1"></div>
        <div class="grease-spot grease-2"></div>
        
        <button class="genesis-close" onclick="closeGenesis()">[ TERMINATE_SESSION ]</button>

        <header class="genesis-header">
            <div class="genesis-logo" style="display: flex; align-items: center; gap: 20px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="50" height="50">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#1a2a3a" stroke-width="2" opacity="0.2"/>
                    <path d="M75 35 A30 30 0 1 0 75 65" fill="none" stroke="#1a2a3a" stroke-width="8" stroke-linecap="round"/>
                    <path d="M75 50 L50 50" fill="none" stroke="#3498db" stroke-width="8" stroke-linecap="round"/>
                    <circle cx="50" cy="50" r="5" fill="#3498db">
                        <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite" />
                    </circle>
                </svg>
                <div>
                    <div class="corp-name" style="position: relative;">
                        GENESIS TECH
                        <span class="null-scribble" style="top: -5px; left: -10px; font-size: 2rem; transform: rotate(-5deg); white-space: nowrap;">GENESIS WRECK</span>
                    </div>
                    <div class="slogan">POWERING THE NEXT STEP</div>
                    <div class="null-scribble" style="top: 30px; left: 180px; font-size: 0.8rem; color: #2c3e50; opacity: 0.3; transform: rotate(2deg);">( it's called greed energy )</div>
                    <div style="font-size: 0.6rem; color: #95a5a6; margin-top: 10px; font-weight: 800; position: relative; width: max-content;">
                        DATE_DEPOSITED: 14_OCT_1975
                        <span class="null-scribble" style="top: -10px; left: 82px; transform: rotate(-5deg); border: 4px solid #e74c3c; border-radius: 48% 52% 45% 55% / 40% 60% 40% 60%; width: 70px; height: 35px; box-shadow: 0 0 5px rgba(231, 76, 60, 0.3);"></span>
                        <span class="null-scribble" style="top: 25px; left: 105px; font-size: 1rem; transform: rotate(4deg); white-space: nowrap;">The year I learned to stare back.</span>
                    </div>
                </div>
            </div>
            <div class="genesis-stamp">PROJECT_SHUTDOWN // SITE-ALPHA</div>
        </header>

            <!-- Facility Log Access: Authorized -->
            <div class="genesis-section">
                <h3>FACILITY_LOG: ALPHA-01 // ENERGY_DISTRIBUTION</h3>
                <div class="genesis-data-grid">
                    <div class="genesis-data-item"><label>Primary Energy Asset</label><span>PROJECT_G (Status: <b style="color:#27ae60">CORE_RESONANCE_STABLE</b>)</span></div>
                    <div class="genesis-data-item"><label>Grid Coverage</label><span>SITE_ALPHA + SECTOR_7_MUNICIPALITY</span></div>
                    <div class="genesis-data-item"><label>Extraction Method</label><span>ANALOG_STRESS_HARVESTING</span></div>
                    <div class="genesis-data-item"><label>Subject Viscosity</label><span>4.2 cP (1975_OPTIMAL_BAND)</span></div>
                </div>

                <p style="line-height: 1.8; color: #34495e; font-size: 0.95rem;">
                    Project G (Subject 0882) remains the cornerstone of Site Alpha's power infrastructure. Unlike standard nuclear or thermal sources, Null generates power through <b>Continuous Silhouette Reconfiguration</b>. By forcing the Subject's morphic mass into non-euclidean geometries through magnetic interference, we harvest the massive amounts of trans-dimensional kinetic energy released during the "snap-back" as the Subject attempts to reform its preferred beastial silhouette. 
                    <br><br>
                    <i>Caution: Prolonged exposure to the Subject's resonance causes mild liquidification of inanimate objects in the immediate vicinity.</i>
                </p>
            </div>

            <div class="genesis-section">
                <h3>CONTAINMENT_MAINTENANCE: SILHOUETTE_RETENTION</h3>
                <div style="font-size: 0.85rem; background: #f9f9f9; border: 1px solid #ddd; padding: 25px; position: relative;">
                    <div style="position: absolute; top: 10px; right: 20px; color: #3498db; font-weight: 800; font-size: 0.6rem;">SEC_LEVEL_4_ONLY // 1975_PROTOCOL</div>
                    <p><b>ANCHORING_PROTOCOL_77:</b> The Subject does not require biological or digital sustenance. Instead, it maintains physical cohesion by <b>Overwriting Context</b>. Without a constant stream of external "shapes" to stabilize against, the morphic core undergoes Viscosity Burnout, where the Subject ceases to be an individual and begins to permeate the facility's molecular structure directly.</p>
                    <p style="margin-top: 15px;"><b>STABILIZATION_METHOD:</b> We do not "feed" the Subject; we distract it. By providing complex geometric lattices and rotating biological templates, we keep the Subject's predatory focus occupied on the process of becoming, preventing it from realizing its own total lack of boundaries.</p>
                </div>
            </div>

            <div class="genesis-section">
                <h3>RELATED_ASSETS: PROJECT_BRANCHES</h3>
                <div style="font-size: 0.85rem; background: #fff; border: 1px solid #eee; padding: 20px;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 12px 0; border-bottom: 1px dashed #eee; position: relative;">
                            <b onclick="openVyper()" style="cursor:pointer; color: #1a2a3a; text-decoration: underline;">PROJECT_V (Vyper)</b>: <span style="color: #e67e22;">PHASE_1_FAILURE</span>. Subject exhibited extreme cellular instability and "Ghost-Massing"—the ability to become physically intangible while remaining visually present. Decommissioned after the <span class="redacted-bar">SECTOR_7_INCIDENT</span> incident. [ <a href="#" onclick="openVyper(); return false;" style="color: #3498db;">VIEW_DOSSIER</a> ]
                            <div class="null-scribble" style="top: 10px; right: -40px; font-size: 0.8rem; transform: rotate(-10deg); opacity: 0.4;">The year we blurred. (75)</div>
                        </li>
                        <li style="padding: 12px 0; position: relative;">
                            <b onclick="openZeke()" style="cursor:pointer; color: #1a2a3a; text-decoration: underline;">SUBJECT_Z (Zeke)</b>: <span style="color: #27ae60">STABLE_CONTAINMENT</span>. Primary asset for the <b>Midas Touch Initiative</b>. Subject demonstrates localized metallurgical reconfiguration (Black/White Steel). A separate genetic branch from Project G, Zeke was acquired via external abduction. [ <a href="#" onclick="openZeke(); return false;" style="color: #3498db;">VIEW_DOSSIER</a> ]
                            <div class="null-scribble" style="top: 20px; right: -50px; font-size: 0.8rem; transform: rotate(8deg); opacity: 0.4;">The year I forgot the weight of silver. (75)</div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="genesis-blob-note">
                <b>INTERNAL_MEMO_77-X // DR. ARDEN</b><br>
                "The municipality sees 'Project G' as a miracle of green energy, but they don't see the ink-black beast screaming in the resonance tube. Null is no longer satisfied with our biological silhouettes; he is <b>Overwriting</b> the context of the lab itself to test his range. I found a 'second' version of my glasses on my desk this morning—they looked perfect until I touched them and the 'frames' began to growl. The Board refuses to fund the <span class="redacted-bar">REALITY_ANCHORING_LATTICES</span> because 'the grid is stable,' but they aren't the ones who have to worry about their own hands becoming viscous if they spend too long in the observation room. Keep the pattern-saturation loops at 110% intensity. If he loses interest in the templates, he’ll start looking at the staff as the next shape to try on."
            </div>

            <div class="genesis-section" style="background: rgba(231, 76, 60, 0.05); border: 1px solid rgba(231, 76, 60, 0.2); padding: 25px;">
                <h3 style="background: #e74c3c; color: #fff;">SECURITY_ADVISORY: THE MORPHIC CASCADE</h3>
                <p style="font-size: 0.85rem; color: #c0392b; font-weight: 700;">
                    WARNING: EMERGENCY_PURGE_FAILURE // TOTAL_ASSET_DISPERSAL
                </p>
                <div style="font-size: 0.85rem; line-height: 1.6; color: #1a2a3a;">
                    The breach at Site Alpha was triggered by a catastrophic power surge initiated by Subject 0882, resulting in a <b>Morphic Cascade</b> across the Alpha Wing. 
                    <br><br>
                    A <b>Morphic Cascade</b> is defined as a chain-reaction of total physical fluidization. Subject 0882's resonance reached a "Critical Viscosity" point, causing the "Rule of <span style="text-decoration: line-through; opacity: 0.5;">Fixed</span> <span style="position: relative;">Shape <span class="null-scribble" style="top: -10px; left: -20px; font-size: 1.2rem; transform: rotate(-5deg);">FLEXED</span></span> to fail for all matter within a 400-meter radius. Containment walls didn't just break—they forgot how to be solid. Subjects didn't just escape; they dissolved through the floor and manifested elsewhere. 
                    <br><br>
                    While Null, Zeke, and the remains of Project Vyper are the only assets currently being tracked, the surge effectively "un-made" the containment infrastructure for dozens of uncatalogued experiments. An <span style="position: relative;"><b>UNKNOWN NUMBER</b> <span class="null-scribble" style="top: 15px; left: -10px; font-size: 0.7rem; transform: rotate(-2deg); opacity: 0.3;">(1975 resonance confirmed for all signatures)</span></span> of signatures have successfully departed the facility. They are currently active within the municipality. They are UNCATALOGUED, PREDATORY, and currently UNCONTAINED.
                </div>
                <div class="null-scribble" style="bottom: -20px; left: 60px; transform: rotate(2deg);">WE FLOW. YOU BREAK.</div>
            </div>

            <div style="margin-top: 40px; border-top: 3px double #1a2a3a; padding-top: 20px; font-style: italic; color: #7f8c8d; font-size: 0.75rem; text-align: center;">
                "THE RAIN ISN'T WATER. IT'S THE SHED SKIN OF THE ONES WHO LEARNED TO WALK."
            </div>

            <div style="position: absolute; bottom: 40px; right: 40px; opacity: 0.05; pointer-events: none;">
                <img src="genesis_seal.png" style="width: 200px;">
            </div>
    </div>
</div>

<script>
window.verifyAccess = function(type) {
    let code = "";
    let expected = "";
    let callback = null;

    if (type === 'VYPER') { 
        expected = "ITERATION_1"; 
        code = prompt("GENESIS_SECURITY_TERMINAL: ENTER PURGE_OVERRIDE_CODE");
        callback = window.openVyper;
    } else if (type === 'ZEKE') { 
        expected = "SUITE-C"; 
        code = prompt("GENESIS_SECURITY_TERMINAL: ENTER CONTAINMENT_SUITE_ID");
        callback = window.openZeke;
    }

    if (code && code.toUpperCase().trim() === expected) {
        callback();
    } else if (code !== null) {
        Swal.fire({
            title: 'PROTOCOL BREACH',
            text: 'ACCESS_DENIED: INVALID_AUTHENTICATION_KEY',
            icon: 'error',
            confirmButtonText: 'RETRY'
        });
    }
};

window.openGenesis = function() {
    document.getElementById('genesisOverlay').classList.add('active');
    localStorage.setItem('genesis_archive_viewed', 'true');
};
window.closeGenesis = function() {
    document.getElementById('genesisOverlay').classList.remove('active');
};
</script>
