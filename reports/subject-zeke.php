<?php
// Gating Logic - STATUS: TEMPORARY_LOCKOUT
$is_unlocked = false;

if ($is_unlocked):
?>
<style>
/* --- SUBJECT PROFILE: ZEKE --- */
.zeke-overlay {
    position: fixed; inset: 0; z-index: 22000;
    background: rgba(0, 5, 0, 0.98);
    display: none; align-items: center; justify-content: center;
    padding: 20px;
}
.zeke-overlay.active { display: flex; animation: zekeFadeIn 0.4s cubic-bezier(0.19, 1, 0.22, 1); }

@keyframes zekeFadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

.zeke-dossier {
    width: 95%; max-width: 1000px; height: 85vh;
    background: #fdfdfd; color: #1e272e;
    padding: 60px; font-family: 'Courier Prime', monospace;
    border-left: 30px solid #2ecc71; box-shadow: 0 40px 80px rgba(0,0,0,0.8);
    position: relative; overflow-y: auto;
}

.zeke-header { border-bottom: 2px solid #2ecc71; padding-bottom: 20px; margin-bottom: 40px; }
.zeke-tag { font-size: 0.6rem; background: #2ecc71; color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 800; letter-spacing: 1px; }

.zeke-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-bottom: 40px; }
.zeke-data { border-bottom: 1px solid #eee; padding-bottom: 10px; }
.zeke-data label { display: block; font-size: 0.6rem; color: #bdc3c7; text-transform: uppercase; font-weight: 800; }
.zeke-data span { font-size: 1rem; color: #2c3e50; font-weight: 600; }

.zeke-photo {
    float: right; width: 200px; height: 200px;
    background: #000; border: 10px solid #fff;
    box-shadow: 2px 10px 20px rgba(0,0,0,0.1);
    margin-left: 30px; margin-bottom: 20px;
    transform: rotate(3deg);
}

.zeke-observation {
    background: #f9f9f9; border-radius: 10px; padding: 30px;
    font-size: 0.9rem; line-height: 1.8; color: #34495e;
    position: relative; margin-top: 20px;
}

.zeke-scribble {
    font-family: 'Caveat', cursive; font-size: 1.6rem; color: #2ecc71;
    margin-top: 20px; text-align: right; transform: rotate(-2deg);
}

.ability-tag {
    display: inline-block; background: #eee; padding: 2px 8px;
    font-size: 0.7rem; border-radius: 4px; margin-right: 5px; margin-bottom: 5px;
    color: #555; font-weight: 900;
}

.zeke-close {
    position: absolute; top: 20px; right: 20px;
    background: none; border: 1px solid #ddd; color: #bdc3c7;
    padding: 5px 15px; cursor: pointer; transition: all 0.2s;
}
.zeke-close:hover { border-color: #2ecc71; color: #2ecc71; }
</style>

<div class="zeke-overlay" id="zekeOverlay" onclick="closeZeke()">
    <div class="zeke-dossier" onclick="event.stopPropagation()">
        <button class="zeke-close" onclick="closeZeke()">[ CLOSE_FILE ]</button>

        <header class="zeke-header">
            <span class="zeke-tag">SITE_ALPHA // SUITE-C // ASSET_S-0894</span>
            <h1 style="margin: 10px 0 0 0; font-size: 2.5rem; font-weight: 900;">ZEKE // NULL'S PET</h1>
        </header>

        <div class="zeke-photo">
            <!-- Image placeholder or generated image -->
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#888; font-size:0.6rem;">[ PHOTO_MISSING ]</div>
        </div>

        <div class="zeke-grid">
            <div class="zeke-data"><label>Project Code</label><span>S-0894</span></div>
            <div class="zeke-data"><label>Estimated Age</label><span>19 CYCLES (Smoll)</span></div>
            <div class="zeke-data"><label>Genetic Origin</label><span>Vampiric Heritage / Kidnapped</span></div>
            <div class="zeke-data"><label>Color Palette</label><span>BLACK, GRAY, WHITE</span></div>
        </div>

        <div class="profile-section" style="margin-bottom: 30px;">
            <label style="font-size: 0.65rem; color: #bdc3c7; font-weight: 800; text-transform: uppercase;">Abilities</label>
            <div style="margin-top: 10px;">
                <span class="ability-tag">MAGNETIC_FORCE</span>
                <span class="ability-tag">MIDAS_TOUCH (Metal)</span>
                <span class="ability-tag">VAMPIRIC_HEALING</span>
            </div>
        </div>

        <div class="zeke-observation">
            <b>GENESIS_RESEARCH_SUMMARY:</b>
            Subject was abducted from non-traditional heritage lines for the **Midas Touch Initiative**. The experiment successfully integrated a transformation curse—anything the subject touches without suppressive layering turns to <b>Black/White Steel</b>. 
            <br><br>
            Subject is frequently bored and curious, demonstrating a talent for <b>weapon forging</b> using his own transformed materials. He exhibits a profound hatred for humans and biological family members, preferring the company of animals and other subjects.
            <br><br>
            <i>RECOVERY_NOTE:</i> Subject escaped before containment was finalized. He managed to steal a pair of suppressive gloves to control his "curse." Last reported sightings place him in company with <b>Subject S-0882 (Null)</b>.
        </div>

        <div class="zeke-scribble">
            "Curiosity didn't kill the cat. It just turned it to steel."
        </div>

        <div style="margin-top: 50px; border-top: 1px dashed #eee; padding-top: 20px;">
            <div class="zeke-grid">
                <div class="zeke-data"><label>Enjoys</label><span>Company, Animals</span></div>
                <div class="zeke-data"><label>Hates</label><span>Humans, Family, Being Ignored</span></div>
            </div>
        </div>
    </div>
</div>

<script>
window.openZeke = function() {
    document.getElementById('zekeOverlay').classList.add('active');
};
window.closeZeke = function() {
    document.getElementById('zekeOverlay').classList.remove('active');
};
</script>
<?php endif; ?>
