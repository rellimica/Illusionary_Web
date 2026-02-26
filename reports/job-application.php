<?php
/**
 * VERTEX GLOBAL SERVICES - COMPREHENSIVE MULTI-PHASE ASSESSMENT
 * A deep, 50-item corporate assessment with forced-scroll unique legalese.
 * Refined for New Hire vetting.
 */
?>
<style>
/* --- CORPORATE APPLICATION STYLES --- */
.job-overlay {
    position: fixed; inset: 0; z-index: 21000;
    background: rgba(40, 44, 52, 0.9);
    backdrop-filter: blur(10px);
    display: none; align-items: center; justify-content: center;
    padding: 20px;
    cursor: default;
    font-family: 'Inter', 'Segoe UI', sans-serif;
}
.job-overlay.active { display: flex; animation: jobSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes jobSlideUp { from { opacity: 0; transform: translateY(60px); } to { opacity: 1; transform: translateY(0); } }

.job-form-container {
    width: 100%; max-width: 1000px;
    background: #fff; color: #333;
    box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.6);
    position: relative; overflow: hidden;
    border-radius: 12px;
    display: flex; flex-direction: column;
    max-height: 95vh;
}

.job-form-header {
    background: #1a202c; color: #fff;
    padding: 30px 50px;
    display: flex; justify-content: space-between; align-items: center;
    flex-shrink: 0;
}
.job-form-header h1 { font-size: 1.2rem; margin: 0; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; gap: 15px; }
.job-form-header .id-tag { font-size: 0.7rem; opacity: 0.5; font-weight: normal; font-family: monospace; }

.job-scroll-area {
    padding: 40px 60px; overflow-y: auto; flex-grow: 1;
    background: #fcfcfc;
}

.section-card {
    background: #fff; border: 1px solid #edf2f7; border-radius: 8px;
    padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
}
.section-card h2 { 
    font-size: 0.9rem; font-weight: 800; text-transform: uppercase; 
    color: #2b6cb0; margin-bottom: 25px; border-bottom: 2px solid #ebf8ff;
    padding-bottom: 15px; display: flex; align-items: center; gap: 10px;
}

.q-wrapper { margin-bottom: 30px; border-bottom: 1px solid #f7fafc; padding-bottom: 25px; }
.q-wrapper:last-child { border: none; }

.q-label { font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 15px; display: block; }
.q-number { color: #cbd5e0; font-weight: 400; margin-right: 10px; font-family: monospace; }

.text-input {
    width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 4px;
    font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease;
    background: #fff; color: #1f2937;
}
.text-input:focus { border-color: #3498db; outline: none; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }

.textarea-input {
    width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 4px;
    font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease;
    background: #fff; color: #1f2937; resize: vertical; min-height: 80px;
}
.textarea-input:focus { border-color: #3498db; outline: none; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }

.select-input {
    width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 4px;
    font-family: inherit; font-size: 0.9rem; background: #fff;
}

.fill-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; line-height: 2.5; }
.fill-blank { border: none; border-bottom: 2px solid #cbd5e0; padding: 0 10px; width: 150px; font-family: inherit; font-size: 0.9rem; text-align: center; background: transparent; }
.fill-blank:focus { outline: none; border-color: #3498db; background: #ebf8ff; }

.likert-scale { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-top: 10px; }
.likert-option { 
    display: flex; flex-direction: column; align-items: center; gap: 5px; 
    cursor: pointer; font-size: 0.7rem; color: #718096;
}
.likert-option input { margin: 0; cursor: pointer; }

.job-form-footer {
    padding: 30px 50px; background: #f7fafc; border-top: 1px solid #edf2f7;
    display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;
}

.submit-btn {
    background: #3182ce; color: #fff; border: none; border-radius: 6px;
    padding: 12px 35px; font-weight: 700; font-size: 0.9rem; cursor: pointer;
    transition: all 0.2s;
}
.submit-btn:disabled { background: #cbd5e0; cursor: not-allowed; transform: none; box-shadow: none; }
.submit-btn:hover:not(:disabled) { background: #2b6cb0; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3); }

.legal-box {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;
    font-size: 0.75rem; line-height: 1.8; color: #4a5568; padding: 30px; text-align: justify;
    max-height: 400px; overflow-y: scroll; margin-top: 20px;
    scrollbar-width: thin; scrollbar-color: #cbd5e0 #f8fafc;
}

.close-btn { background: none; border: none; color: #fff; opacity: 0.6; cursor: pointer; font-size: 1.5rem; }

.scroll-hint { font-size: 0.65rem; color: #e53e3e; margin-top: 10px; font-style: italic; display: block; }
.scroll-hint.success { color: #38a169; font-style: normal; font-weight: bold; }

</style>

<div class="job-overlay" id="jobOverlay">
    <div class="job-form-container" onclick="event.stopPropagation()">
        <header class="job-form-header">
            <h1>
                <svg width="28" height="28" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #63b3ed;">
                    <path d="M50 85L20 20L50 35L80 20L50 85Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/>
                    <path d="M50 35L20 20L50 85" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="50" cy="35" r="4" fill="currentColor" />
                </svg>
                VERTEX GLOBAL SERVICES // PERSONNEL ASSESSMENT
                <span class="id-tag">REF_VGS_OS_2026</span>
            </h1>
            <button class="close-btn" onclick="closeJob()">×</button>
        </header>

        <form onsubmit="submitJob(event)" class="job-scroll-area">
            
            <section class="section-card">
                <h2>Phase I: Personnel Logistics & Identity Verification</h2>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">01.</span> Full Legal Name:</label>
                    <input type="text" class="text-input" name="q1" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">02.</span> Primary Communication Frequency (Email):</label>
                    <input type="email" class="text-input" name="q2" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">03.</span> Direct Contact Line (Phone Number):</label>
                    <input type="tel" class="text-input" name="q3" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">04.</span> Current Residential Sector (Full Address):</label>
                    <input type="text" class="text-input" name="q4" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">05.</span> Primary Mobile OS Infrastructure:</label>
                    <select class="select-input" name="q5" required>
                        <option value=""></option>
                        <option value="ios">Apple iOS</option>
                        <option value="android">Android / Google</option>
                        <option value="proprietary">Proprietary / Custom</option>
                        <option value="none">Mobile Hardware Not Indexed</option>
                    </select>
                </div>
            </section>

            <section class="section-card">
                <h2>Phase II: Professional Background & Career Goals</h2>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">06.</span> Highest Academic Credential Attained:</label>
                    <select class="select-input" name="q6" required>
                        <option value=""></option>
                        <option value="high">High School / GED</option>
                        <option value="assoc">Associate Degree</option>
                        <option value="bach">Bachelor's Degree</option>
                        <option value="mast">Master's Degree</option>
                        <option value="phd">PhD / Doctorate</option>
                    </select>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">07.</span> Educational Institution / Research Facility Name:</label>
                    <input type="text" class="text-input" name="q7" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">08.</span> Most Recent Professional Employer:</label>
                    <input type="text" class="text-input" name="q8" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">09.</span> Professional Job Title / Functional Designation:</label>
                    <input type="text" class="text-input" name="q9" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">10.</span> Primary Motivation for Seeking Placement at Vertex Global Services:</label>
                    <textarea class="textarea-input" name="q10" required></textarea>
                </div>
            </section>

            <section class="section-card">
                <h2>Phase III: Workplace Preferences & Mundane Metrics</h2>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">11.</span> Standard daily caffeine consumption (for supply replenishment planning):</label>
                    <input type="number" class="text-input" name="q11" min="0" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">12.</span> Preferred Desktop OS for high-speed documentation tasks:</label>
                    <select class="select-input" name="q12" required>
                        <option value=""></option>
                        <option value="win">Windows</option>
                        <option value="mac">macOS</option>
                        <option value="linux">Linux / Unix</option>
                        <option value="vgsos">VGS-Term (Internal Standard)</option>
                    </select>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">13.</span> Target office ambient temperature preference (Celsius):</label>
                    <input type="text" class="text-input" name="q13" placeholder="22.5" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">14.</span> Hexadecimal preference for Personalized Dashboard Visualization:</label>
                    <input type="text" class="text-input" name="q14" placeholder="#00FF88" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">15.</span> Preferred Browser for Corporate Portal access:</label>
                    <select class="select-input" name="q15" required>
                        <option value=""></option>
                        <option value="chrome">Google Chrome</option>
                        <option value="firefox">Mozilla Firefox</option>
                        <option value="edge">Microsoft Edge</option>
                        <option value="null-view">Null-Terminal Viewer</option>
                    </select>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">16.</span> Ergonomic configuration: Standing vs Seated desk priority?</label>
                    <input type="text" class="text-input" name="q16" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">17.</span> Hours per day comfortable analyzing high-density data visualizations:</label>
                    <input type="number" class="text-input" name="q17" min="0" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">18.</span> Which descriptive weekend activity do you believe best restores professional focus?</label>
                    <input type="text" class="text-input" name="q18" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">19.</span> Describe your approach to troubleshooting legacy departmental hardware:</label>
                    <textarea class="textarea-input" name="q19" required></textarea>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">20.</span> Recommended screen resolution for prolonged indexing sessions:</label>
                    <input type="text" class="text-input" name="q20" placeholder="3840x2160" required>
                </div>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">21.</span> Fill in the blanks regarding your career trajectory:</label>
                    <div class="fill-row">
                        "Through this placement, I intend to 
                        <input type="text" class="fill-blank" name="q21_1" required> 
                        my professional value and 
                        <input type="text" class="fill-blank" name="q21_2" required> 
                        within the corporate hierarchy."
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2>Phase IV: Operational Compatibility & Situational Logic</h2>
                <p style="font-size:0.75rem; color:#718096; margin-bottom:20px;">Please rate your agreement with the following statements (1: Strongly Disagree, 5: Strongly Agree).</p>
                <?php 
                $qSituational = [
                    22 => "I remain highly focused during repetitive auditory background disturbances.",
                    23 => "I can maintain consistent output levels when light spectrum shifts occurs.",
                    24 => "Atmospheric pressure fluctuations do not typically impact my focus.",
                    25 => "I can effectively ignore visual anomalies or 'Shadow Artifacts' during work shifts.",
                    26 => "I find Dark Mode interfaces to be superior for mission-critical monitoring.",
                    27 => "I find high-frequency white noise to be a grounding/focusing element.",
                    28 => "I consider the Administrator's vision for the facility to be absolute and correct.",
                    29 => "I am capable of executing tasks without immediate situational context from management.",
                    30 => "I find hierarchical clarity to be the most efficient workflow model."
                ];
                foreach($qSituational as $num => $qText): ?>
                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number"><?= $num ?>.</span> <?= $qText ?></label>
                    <div class="likert-scale">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <label class="likert-option">
                            <input type="radio" name="q<?= $num ?>" value="<?= $i ?>" required>
                            <span><?= $i ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">31.</span> Briefly describe your ideal work environment (Lighting, sound, sensory data):</label>
                    <textarea class="textarea-input" name="q31" required></textarea>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">32.</span> Preferred Onboarding Assessment Format (Aptitude, Resonance, or Morphic Drill):</label>
                    <select class="select-input" name="q32" required>
                        <option value=""></option>
                        <option value="apt">Digital Aptitude Test</option>
                        <option value="res">Biological Resonance Scan</option>
                        <option value="morp">Morphic Stabilization Drill</option>
                        <option value="void">Direct Context Sync</option>
                    </select>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">33.</span> What is your professional definition of 'Operational Continuity'?</label>
                    <input type="text" class="text-input" name="q33" required>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">34.</span> SOP: If a primary system warning indicates a <b>19.75MHz surge</b>, your first action is:</label>
                    <div class="options-group">
                        <label class="opt-label"><input type="radio" name="q34" value="A" required> File Routine Report</label>
                        <label class="opt-label"><input type="radio" name="q34" value="B"> Isolate Biological Lattice</label>
                        <label class="opt-label"><input type="radio" name="q34" value="C"> Maintain Direct Observation</label>
                        <label class="opt-label"><input type="radio" name="q34" value="D"> Perform Harmonic Stabilization</label>
                    </div>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">35.</span> How many minutes can you maintain focus in total sensory isolation before 'Indexing Fatigue' set in?</label>
                    <input type="number" class="text-input" name="q35" required>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">36.</span> Define your methodological response to 'System Drift' or Unindexed Data anomalies:</label>
                    <input type="text" class="text-input" name="q36" required>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">37.</span> Professionally, why is a 'Total System Rewrite' necessary for Operational Continuity?</label>
                    <textarea class="textarea-input" name="q37" required></textarea>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">38.</span> Monitor Preference: Dual standard display or Ultra-Wide Integrated setup?</label>
                    <select class="select-input" name="q38" required>
                        <option value="dual">Dual Standard Setup</option>
                        <option value="ultra">Single Ultra-Wide</option>
                        <option value="vr">Tethered Neural Interface</option>
                        <option value="retinal">Embedded Retinal Overlay</option>
                    </select>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">39.</span> Describe a time you had to prioritize corporate directives over individual autonomy:</label>
                    <textarea class="textarea-input" name="q39" required></textarea>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">40.</span> Tolerance for High-Risk Experimental Environments (1: Low, 5: Absolute Preference):</label>
                    <div class="likert-scale">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <label class="likert-option">
                            <input type="radio" name="q40" value="<?= $i ?>" required>
                            <span><?= $i ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">41.</span> Fill in the blanks for the Final Onboarding Oath:</label>
                    <div class="fill-row">
                        "I, 
                        <input type="text" class="fill-blank" name="q41_1" style="width:100px;">, hereby 
                        <input type="text" class="fill-blank" name="q41_2" style="width:120px;"> my reality to the 
                        <input type="text" class="fill-blank" name="q41_3" placeholder="Vertex" style="width:80px;"> Global Project."
                    </div>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">42.</span> Favorite species of desk plant for facility air quality maintenance:</label>
                    <input type="text" class="text-input" name="q42" required>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">43.</span> Preferred Professional Redundancy Profile (Shadow, Resonator, or Echo):</label>
                    <select class="select-input" name="q43" required>
                        <option value=""></option>
                        <option value="A">Static Shadow</option>
                        <option value="B">High-Frequency Resonator</option>
                        <option value="C">Observational Echo</option>
                        <option value="D">Digital Log Node</option>
                    </select>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">44.</span> Rate your commitment to the facility's Primary Directive (1: Minimal, 5: Total):</label>
                    <div class="likert-scale">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <label class="likert-option">
                            <input type="radio" name="q44" value="<?= $i ?>" required>
                            <span><?= $i ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">45.</span> Does your listed Emergency Contact know of your commitment to 'The facility'?</label>
                    <div class="options-group">
                        <label class="opt-label"><input type="radio" name="q45" value="yes" required> Yes</label>
                        <label class="opt-label"><input type="radio" name="q45" value="no"> No (Irrelevant to Role)</label>
                        <label class="opt-label"><input type="radio" name="q45" value="soon"> Pending Integration Notify</label>
                    </div>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">46.</span> Which internal tech stack profile do you find most reliable for long-term sync?</label>
                    <select class="select-input" name="q46" required>
                        <option value=""></option>
                        <option value="leg">Legacy Documentation</option>
                        <option value="neur">Neural Link Interface</option>
                        <option value="vgs">VGS-Core Total Integration</option>
                    </select>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">47.</span> If you were a piece of corporate metadata, what would your 'Error Code' be?</label>
                    <input type="text" class="text-input" name="q47" required>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">48.</span> Administrative intervention from Leadership is something I find professionally motivating.</label>
                    <div class="likert-scale">
                        <?php for($i=1; $i<=5; $i++): ?>
                        <label class="likert-option">
                            <input type="radio" name="q48" value="<?= $i ?>" required>
                            <span><?= $i ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="q-wrapper">
                    <label class="q-label"><span class="q-number">49.</span> Briefly describe your 'Ideal Self' after 5 years of successful integration into Vertex Global Services:</label>
                    <textarea class="textarea-input" name="q49" required></textarea>
                </div>

                <div class="q-wrapper" style="margin-top: 40px;">
                    <label class="q-label"><span class="q-number">50.</span> Final Statement: I am ready for total professional integration.</label>
                    <div style="display:flex; gap:20px;">
                        <label class="opt-label"><input type="radio" name="q50" value="yes" required> TRUE</label>
                        <label class="opt-label"><input type="radio" name="q50" value="no" disabled> FALSE (Leadership Exception Required)</label>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <h2>Phase V: Mandatory Employment Disclosure (v1.0.9)</h2>
                <div class="legal-box" id="legalWall" onscroll="handleLegalScroll(this)">
                    <?php 
                    $sections = [
                        "I. INITIAL CERTIFICATION AND ONBOARDING PROTOCOLS" => "By entering into this agreement, the New Hire (hereafter referred to as 'The Vessel') acknowledges and certifies that all biographical data, digital signatures, and professional credentials submitted via the Vertex Global Services Talent Acquisition Portal are provided voluntarily and are an accurate representation of the Vessel's current status. The Vessel understands that placement with Vertex Global is a total-integration process, overseen by the VGS documentation kernel and governed by the primary directives of The Administrator. This integration involves the synchronization of the Vessel's local reality with the facility kernel, which may result in irreversible molecular re-folding during standard operational shifts.",
                        "II. AT-WILL PLPLACEMENT AND MORPHIC STABILIZATION" => "Placement remains at-will, however, the Vessel acknowledges that the 'at-will' status is effectively managed by the VGS documentation layer. The Company reserves the right to terminate integration at any time if the Vessel's biological signature falls below the 85% compatibility threshold. The Vessel authorizes the use of morphic stabilization fields to maintain structural integrity during resonance surges, understanding that these fields may cause temporary or permanent silence, visual smearing, or a total loss of silhouette.",
                        "III. HIERARCHICAL COMMAND AND SPECIES-SPECIFIC GOVERNANCE" => "The Vessel acknowledges that operational control is bifurcated based on structural composition. All classified entities—including robots, cyborgs, dragons, and humans—are subject to the total governance of Null-OS in conjunction with the entity designated as NULL. While automated systems manage baseline synchronization, all living substrates will receive direct, un-indexed behavioral direction from NULL themselves. By proceeding, the Vessel consents to this dual-layer authority for the duration of the Facility's active cycle.",
                        "IV. DATA SOVEREIGNTY AND CORPORATE REPAIR" => "All data generated, processed, or witnessed by the New Hire remains the sole property of Vertex Global Services. This includes resonance echoes, peripheral artifact logs, and the Vessel's own biological metadata. The Company reserves the right to re-fold, compress, and re-transmit this data through the VGS terminal for maintenance purposes. The Vessel further agrees that their digital and physical remains post-incident are considered corporate intellectual property.",
                        "V. RESIDUAL ASSET MANAGEMENT AND NEXT OF KIN" => "In the event of total structural de-cohesion, the Company will activate the 'Residual Asset Management' protocol. This involves the collection of all data fragments and physical residues for re-integration into the next kernel version. The Emergency Contact listed will be notified of dispersal via the standard 'System Error' broadcast. The Company does not guarantee the return of the Vessel's original silhouette or linear history.",
                        "VI. CORPORATE ALIGNMENT AND THE FACILITY" => "The Vessel understands that their primary function is to serve the vision of the Administrator at the Facility. This includes the 'Voiding' of previous professional history if it conflicts with the current project kernel. By proceeding, the Vessel accepts their status as a functional node within the Company. Silence is considered a form of agreement. Silence is monitored. Silence is required during indexing.",
                        "VII. REINDEXING AND MEMORY ARCHIVING" => "The Company may choose to reindex the Vessel at any time without notice. Reindexing involves the total compression of current memories into a single archive, followed by the deployment of a new 'Day 0' personality template. This is a standard corporate procedure to ensure maximum loyalty and focused observation during high-stress project phases."
                    ];

                    for($idx = 0; $idx < 60; $idx++) {
                        $key = array_keys($sections)[($idx % count($sections))];
                        $text = $sections[$key];
                        echo "<h3>[REF_" . strtoupper(substr(md5($idx), 0, 6)) . "] " . $key . "</h3>";
                        echo "<p>" . $text . " (ONBOARD_LOG_" . ($idx * 142) . ")</p>";
                        echo "<p>Legal Verification: " . md5($text . $idx) . " - This sub-clause serves as the binding agreement between the New Hire and Vertex Global Services. All reality-indexing terms remain in effect for the duration of the broadcast duration.</p>";
                    }
                    ?>
                </div>
                
                <span class="scroll-hint" id="scrollHint">Please scroll to the bottom of the Disclosure to verify your New Hire status.</span>

                <div class="form-group" style="margin-top: 25px; display: flex; align-items: flex-start; gap: 12px;">
                    <input type="checkbox" id="agreeTerm" style="width: auto; accent-color: #3182ce; margin-top: 2px;" disabled onchange="toggleSubmit(this)">
                    <label for="agreeTerm" style="margin: 0; font-size: 0.7rem; color: #4a5568; line-height: 1.5;">
                        I certify that I am a prospective New Hire and have read the Disclosure. I accept that I am serving at the pleasure of The Administrator and that my previous professional history is no longer relevant.
                    </label>
                </div>
            </section>

        </form>

        <footer class="job-form-footer">
            <div style="display:flex; flex-direction:column; gap:5px;">
                <div style="font-size: 0.65rem; color: #718096;">VERTEX GLOBAL SERVICES // PERSONNEL_PORTAL // KERNEL_VGS_V1</div>
                <div style="font-size: 0.55rem; color: #a0aec0; font-style: italic; letter-spacing: 0.2px;">
                    [SECURITY_NOTICE: This interface is a simulation. No submitted data is stored, transmitted, or indexed by external networks.]
                </div>
            </div>
            <button type="button" class="submit-btn" id="submitBtn" disabled onclick="document.querySelector('form').requestSubmit()">Submit Assessment for Verification</button>
        </footer>
    </div>
</div>

<script>
function openJob() {
    document.getElementById('jobOverlay').classList.add('active');
    // Reset state
    document.getElementById('agreeTerm').disabled = true;
    document.getElementById('agreeTerm').checked = false;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('legalWall').scrollTop = 0;
    document.getElementById('scrollHint').classList.remove('success');
    document.getElementById('scrollHint').innerText = "Please scroll to the bottom of the Disclosure to verify your New Hire status.";
    
    if (typeof confirmSnd !== 'undefined') {
        confirmSnd.volume = 0.4;
        confirmSnd.play().catch(() => {});
    }
}

function closeJob() {
    document.getElementById('jobOverlay').classList.remove('active');
}

function handleLegalScroll(el) {
    const hint = document.getElementById('scrollHint');
    const agree = document.getElementById('agreeTerm');
    
    if (el.scrollHeight - el.scrollTop <= el.clientHeight + 20) {
        agree.disabled = false;
        hint.innerText = "Disclosure Read. You may now certify your New Hire status.";
        hint.classList.add('success');
    }
}

function toggleSubmit(cb) {
    document.getElementById('submitBtn').disabled = !cb.checked;
}

function submitJob(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const scroll = document.querySelector('.job-scroll-area');
    
    scroll.scrollTo({ top: 0, behavior: 'smooth' });
    
    btn.innerText = "Indexing New Hire Responses...";
    btn.disabled = true;
    btn.style.opacity = "0.7";
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += 2 + Math.random() * 5;
        if (progress >= 100) {
            clearInterval(interval);
            finishSubmission();
        } else {
            btn.innerText = `Parsing Responses: ${Math.floor(progress)}%`;
        }
    }, 400);

    function finishSubmission() {
        btn.innerText = "Recruitment Indexed";
        btn.style.background = "#48bb78";
        
        setTimeout(() => {
            alert("VERTEX GLOBAL SERVICES // SYSTEM NOTICE\n\nPersonnel Assessment REF_VGS_OS_2026 successfully indexed. \n\nYour profile has been committed to the facility kernel for final synchronization. The Administrator will review your compatibility metrics shortly. \n\nPlease remain in resonance range for further directives.");
            closeJob();
        }, 2000);
    }
}
</script>
