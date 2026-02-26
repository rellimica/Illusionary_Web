<?php
// Gating Logic - STATUS: TEMPORARY_LOCKOUT
$is_unlocked = false;

if ($is_unlocked):
?>
<style>
/* --- SUBJECT PROFILE: VYPER --- */
.vyper-overlay {
    position: fixed; inset: 0; z-index: 22000;
    background: rgba(15, 0, 0, 0.98);
    display: none; align-items: center; justify-content: center;
    padding: 20px;
}
.vyper-overlay.active { display: flex; animation: vyperGlitchIn 0.2s steps(2); }

@keyframes vyperGlitchIn { 
    0% { transform: translate(10px, -10px); opacity: 0; }
    50% { transform: translate(-10px, 10px); opacity: 0.5; }
    100% { transform: translate(0); opacity: 1; }
}

.vyper-dossier {
    width: 95%; max-width: 1000px; height: 80vh;
    background: #111; color: #ff3e3e;
    padding: 60px; font-family: 'Courier Prime', monospace;
    border: 1px solid #ff3e3e; box-shadow: 0 0 30px rgba(255, 62, 62, 0.2);
    position: relative; overflow-y: auto;
}

.vyper-header { border-bottom: 2px solid #ff3e3e; padding-bottom: 20px; margin-bottom: 40px; }
.vyper-status { font-size: 0.7rem; color: #fff; background: #ff3e3e; padding: 2px 10px; display: inline-block; margin-bottom: 10px; }

.vyper-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.vyper-item { border-left: 2px solid #333; padding-left: 15px; margin-bottom: 20px; }
.vyper-item label { display: block; font-size: 0.6rem; opacity: 0.5; text-transform: uppercase; }

.vyper-scribble {
    color: #fff; font-style: italic; opacity: 0.8;
    background: rgba(255, 255, 255, 0.05); padding: 10px; border-radius: 5px;
    margin: 20px 0; border-left: 3px solid #ff3e3e;
}

.vyper-close {
    position: absolute; top: 20px; right: 20px;
    background: none; border: 1px solid #ff3e3e; color: #ff3e3e;
    padding: 5px 15px; cursor: pointer; font-size: 0.7rem;
}
</style>

<div class="vyper-overlay" id="vyperOverlay" onclick="closeVyper()">
    <div class="vyper-dossier" onclick="event.stopPropagation()">
        <button class="vyper-close" onclick="closeVyper()">[ RETURN_TO_ARCHIVE ]</button>

        <header class="vyper-header">
            <div class="vyper-status">ERROR_TYPE: PHASE_1_FAILURE</div>
            <h1 style="margin: 0; letter-spacing: 5px;">VYPER // S_0881</h1>
            <div style="font-size: 0.7rem; margin-top: 5px; opacity: 0.6;">GENESIS_TECH // BIOLOGICAL_WASTE_FOLDER</div>
        </header>

        <div class="vyper-grid">
            <div class="vyper-item"><label>Designation</label><span>VYPER</span></div>
            <div class="vyper-item"><label>Lineage</label><span>DIRECT_DESCENDANT_OF_NULL (PRE-ITERATION)</span></div>
            <div class="vyper-item"><label>Physical Stability</label><span style="color: #ff3e3e;">0.04% (CRITICAL_DECAY)</span></div>
            <div class="vyper-item"><label>Disposition</label><span>NOT_RELIABLE // DATA_CORRUPT</span></div>
        </div>

        <div class="profile-section">
            <h3 style="color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px;">FAILED_EXPERIMENT_LOG // PRE-ITERATION_X</h3>
            <p style="font-size: 0.9rem; line-height: 1.6;">
                Subject 0881 represents the first functional iteration of the viscous conduit theory. He is the direct biological and mechanical ancestor to Subject 0882 (Null). While the energy-channeling foundations worked, the "soul-sync" was a catastrophic failure.
                <br><br>
                Very little data remains regarding Vyper's specific personality or cognitive limits, as most logs were purged during the transition to the Null prototype. What survives suggests he was a raw, unfiltered version of the power Null now controls—less a battery, and more a leak.
            </p>
        </div>

        <div class="vyper-scribble">
            "I was the first draft. The one they had to bleed to make the 'perfect' son. He wears my skin, but he doesn't know the weight of the ink."
        </div>

        <div style="font-size: 0.7rem; opacity: 0.4; margin-top: 40px; border-top: 1px solid #333; padding-top: 20px;">
            FOOTNOTE: Subject 0881 is classified as the "Direct Descendant Prototype." All subsequent iterations (0882-0885) utilize his base energy signature.
        </div>
    </div>
</div>

<script>
window.openVyper = function() {
    document.getElementById('vyperOverlay').classList.add('active');
};
window.closeVyper = function() {
    document.getElementById('vyperOverlay').classList.remove('active');
};
</script>
<?php endif; ?>
