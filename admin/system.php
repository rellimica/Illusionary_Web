<?php
$page_title = 'System Configuration';
require_once 'init.php';
require_once 'header.php';
?>

<div class="system-view-wrapper">
    <section class="system-controls stat-card">
        <h2 class="section-title">System & Security</h2>
        <p class="section-subtitle">Manage global locks and basic site accessibility.</p>
        
        <div class="sys-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
            <div class="sys-item card-inner" style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div class="sys-label" style="font-weight: 800; color: #fff; margin-bottom: 5px;">Maintenance Mode</div>
                <div class="sys-desc" style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 15px;">Forces all non-admin users to the wait page.</div>
                <label class="switch">
                    <input type="checkbox" id="maintSwitch" <?php echo $GLOBAL_MAINTENANCE_LOCK ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="sys-item card-inner" style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--glass-border);">
                <div class="sys-label" style="font-weight: 800; color: #fff; margin-bottom: 5px;">Trading System</div>
                <div class="sys-desc" style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 15px;">Enable or disable the creation of new trades.</div>
                <label class="switch">
                    <input type="checkbox" id="tradesSwitch" <?php echo ($TRADES_ENABLED ?? true) ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <h2 class="section-title" style="margin-top: 50px;">Reveal Countdown</h2>
        <p class="section-subtitle">Configure the master countdown and transmission settings.</p>

        <div class="admin-form-group" style="margin-top: 20px;">
            <div class="sys-item card-inner" style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--glass-border); margin-bottom: 20px;">
                <div class="sys-label" style="font-weight: 800; color: #fff; margin-bottom: 5px;">Countdown Active</div>
                <div class="sys-desc" style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 15px;">Master toggle for the reveal countdown feature.</div>
                <label class="switch">
                    <input type="checkbox" id="revealSwitch" <?php echo ($ENABLE_REVEAL_COUNTDOWN ?? false) ? 'checked' : ''; ?>>
                    <span class="slider round"></span>
                </label>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="admin-form-group">
                    <label>Start Time (ISO 8601)</label>
                    <input type="text" id="revealStartInput" value="<?php echo $REVEAL_START_TIME ?? ''; ?>" class="admin-input" placeholder="YYYY-MM-DDTHH:MM:SS-Offset">
                </div>
                <div class="admin-form-group">
                    <label>Target Time (ISO 8601)</label>
                    <input type="text" id="revealTargetInput" value="<?php echo $REVEAL_TARGET_TIME ?? ''; ?>" class="admin-input" placeholder="YYYY-MM-DDTHH:MM:SS-Offset">
                </div>
            </div>

            <div class="admin-form-group" style="margin-top: 20px;">
                <label>Emergency Message (Dashboard Banner)</label>
                <textarea id="revealMsgInput" class="admin-input" style="height: 80px; resize: none;"><?php echo $REVEAL_EMERGENCY_MSG ?? ''; ?></textarea>
            </div>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--glass-border); display: flex; justify-content: flex-end;">
            <button onclick="saveConfig()" class="admin-submit" style="width: 200px;">SAVE CONFIGURATION</button>
        </div>
    </section>
</div>

<script>
    async function saveConfig() {
        const fd = new FormData();
        fd.append('admin_action', 'save_system_config');
        
        fd.append('maint_lock', document.getElementById('maintSwitch').checked ? 1 : 0);
        fd.append('trades_enabled', document.getElementById('tradesSwitch').checked ? 1 : 0);
        fd.append('reveal_countdown', document.getElementById('revealSwitch').checked ? 1 : 0);
        fd.append('reveal_start', document.getElementById('revealStartInput').value);
        fd.append('reveal_target', document.getElementById('revealTargetInput').value);
        fd.append('reveal_msg', document.getElementById('revealMsgInput').value);

        try {
            const res = await secureFetch('api/admin.php', { method: 'POST', body: fd });
            if (!res.success) throw new Error(res.error);
            Swal.fire({ 
                title: 'System Updated', 
                text: 'Global configuration has been successfully synchronized.', 
                icon: 'success', 
                toast: true, 
                position: 'top-end', 
                timer: 3000 
            });
        } catch (e) {
            Swal.fire({ title: 'Update Failed', text: e.message, icon: 'error' });
        }
    }
</script>

<?php require_once 'footer.php'; ?>
