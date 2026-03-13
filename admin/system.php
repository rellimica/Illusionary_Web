<?php
$page_title = 'System & Notifications';
require_once 'init.php';
require_once 'header.php';
?>

<div class="system-view-wrapper">
    <!-- ============================================
         SECTION 1: SYSTEM & SECURITY (preserved)
         ============================================ -->
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

    <!-- ============================================
         SECTION 2: NOTIFICATION COMPOSER
         ============================================ -->
    <section class="stat-card" style="margin-top: 40px;">
        <h2 class="section-title">📣 Notification Composer</h2>
        <p class="section-subtitle">Send notifications to individual users or broadcast to everyone.</p>

        <div class="notif-composer" style="margin-top: 25px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="admin-form-group">
                    <label>Type</label>
                    <select id="notifType" class="admin-input">
                        <option value="system">📣 System</option>
                        <option value="admin_gift">🎁 Admin Gift</option>
                        <option value="welcome">🌟 Welcome</option>
                        <option value="milestone">🏆 Milestone</option>
                        <option value="trade_cancelled">❌ Trade Cancelled</option>
                        <option value="pawn_complete">🏪 Pawn Complete</option>
                        <option value="rare_draw">🔥 Rare Draw</option>
                        <option value="variation_unlock">✨ Variation Unlock</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label>Title</label>
                    <input type="text" id="notifTitle" class="admin-input" placeholder="Notification title...">
                </div>
            </div>

            <div class="admin-form-group" style="margin-top: 15px;">
                <label>Message</label>
                <textarea id="notifMessage" class="admin-input" style="height: 80px; resize: none;" placeholder="Notification body text..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div class="admin-form-group">
                    <label>Link <span style="color: var(--text-muted); font-weight: 400;">(optional)</span></label>
                    <input type="text" id="notifLink" class="admin-input" placeholder="e.g. draw.php, trade.php">
                </div>
                <div class="admin-form-group">
                    <label>Target Discord ID</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" id="notifTargetId" class="admin-input" placeholder="Discord ID..." style="flex: 1;">
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between;">
                <label class="notif-broadcast-toggle">
                    <input type="checkbox" id="notifBroadcast" onchange="toggleBroadcast()">
                    <span class="notif-broadcast-label">BROADCAST TO ALL USERS</span>
                </label>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <label class="notif-broadcast-toggle" style="background: rgba(255, 78, 78, 0.05); border-color: rgba(255, 78, 78, 0.2);">
                        <input type="checkbox" id="notifUrgent">
                        <span class="notif-broadcast-label" style="color: #ff4e4e;">MARK AS URGENT</span>
                    </label>
                    <button onclick="sendNotification()" class="admin-submit" style="width: 200px;">SEND NOTIFICATION</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 3: USER NOTIFICATION MANAGEMENT
         ============================================ -->
    <section class="stat-card" style="margin-top: 40px;">
        <h2 class="section-title">🔍 User Notification Management</h2>
        <p class="section-subtitle">Look up and manage notifications for a specific user.</p>

        <div class="user-search-bar-unified" style="margin-top: 25px;">
            <div class="search-input-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="notifUserSearch" placeholder="Enter Discord ID to view their notifications..." class="unified-input" onkeydown="if(event.key==='Enter') lookupUserNotifs()">
            </div>
            <button onclick="lookupUserNotifs()" class="unified-submit">LOOKUP</button>
        </div>

        <div id="userNotifArea" style="margin-top: 25px;">
            <div class="placeholder-msg">
                <div class="radar-ping"></div>
                <p>Enter a Discord ID to view their notification inbox.</p>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 4: GLOBAL RECENT FEED
         ============================================ -->
    <section class="stat-card" style="margin-top: 40px;">
        <h2 class="section-title">📋 Global Notification Feed</h2>
        <p class="section-subtitle">The last 30 notifications sent across all users.</p>
        <div id="globalNotifFeed" style="margin-top: 25px;">
            <div class="placeholder-msg" style="padding: 40px;">Loading...</div>
        </div>
    </section>
</div>

<script>
    // ============================================
    // SYSTEM CONFIG (preserved from original)
    // ============================================
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

    // ============================================
    // NOTIFICATION ICON MAPPER
    // ============================================
    function getNotifIcon(type) {
        const icons = {
            trade_request: '📢', trade_accepted: '✅', trade_declined: '⛔', trade_cancelled: '❌',
            pawn_complete: '🏪', rare_draw: '🔥', variation_unlock: '✨',
            admin_gift: '🎁', welcome: '🌟', system: '📣', milestone: '🏆'
        };
        return icons[type] || 'ℹ️';
    }

    function formatTimeAgo(timestamp) {
        const seconds = Math.floor(Date.now() / 1000) - parseInt(timestamp);
        if (seconds < 2) return "Just now";
        if (seconds < 60) return seconds + "s ago";
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + "y ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + "mo ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + "d ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + "h ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + "m ago";
        return Math.floor(seconds) + "s ago";
    }

    // ============================================
    // COMPOSER LOGIC
    // ============================================
    function toggleBroadcast() {
        const isBroadcast = document.getElementById('notifBroadcast').checked;
        const targetInput = document.getElementById('notifTargetId');
        targetInput.disabled = isBroadcast;
        targetInput.style.opacity = isBroadcast ? '0.3' : '1';
        if (isBroadcast) targetInput.value = '';
    }

    async function sendNotification() {
        const type = document.getElementById('notifType').value;
        const title = document.getElementById('notifTitle').value.trim();
        const message = document.getElementById('notifMessage').value.trim();
        const link = document.getElementById('notifLink').value.trim();
        const targetId = document.getElementById('notifTargetId').value.trim();
        const broadcast = document.getElementById('notifBroadcast').checked;
        const urgent = document.getElementById('notifUrgent').checked;

        if (!title || !message) {
            Swal.fire({ title: 'Missing Fields', text: 'Title and message are required.', icon: 'warning' });
            return;
        }

        if (!broadcast && !targetId) {
            Swal.fire({ title: 'No Target', text: 'Enter a Discord ID or enable broadcast.', icon: 'warning' });
            return;
        }

        // Confirm broadcast
        if (broadcast) {
            const confirm = await Swal.fire({
                title: '⚡ Broadcast Confirmation',
                html: `This will send <b>"${title}"</b> to <b>ALL registered users</b>. Continue?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Broadcast',
                confirmButtonColor: '#00e5ff'
            });
            if (!confirm.isConfirmed) return;
        }

        const fd = new FormData();
        fd.append('ajax_action', 'send_notification');
        fd.append('notif_type', type);
        fd.append('notif_title', title);
        fd.append('notif_message', message);
        if (link) fd.append('notif_link', link);
        if (broadcast) {
            fd.append('broadcast', '1');
        } else {
            fd.append('target_user_id', targetId);
        }
        if (urgent) fd.append('urgent', '1');

        try {
            const res = await secureFetch('api/admin.php', { method: 'POST', body: fd });
            Swal.fire({
                title: 'Notification Sent',
                text: `Delivered to ${res.sent} user${res.sent !== 1 ? 's' : ''}.`,
                icon: 'success',
                toast: true,
                position: 'top-end',
                timer: 3000
            });
            // Clear form
            document.getElementById('notifTitle').value = '';
            document.getElementById('notifMessage').value = '';
            document.getElementById('notifLink').value = '';
            // Refresh feeds
            loadGlobalFeed();
        } catch (e) {
            Swal.fire({ title: 'Send Failed', text: e.message, icon: 'error' });
        }
    }

    // ============================================
    // USER NOTIFICATION LOOKUP
    // ============================================
    let currentLookupUid = null;

    async function lookupUserNotifs() {
        const uid = document.getElementById('notifUserSearch').value.trim();
        if (!uid) return;
        currentLookupUid = uid;

        const area = document.getElementById('userNotifArea');
        area.innerHTML = '<div class="placeholder-msg" style="padding: 30px;">Scanning inbox...</div>';

        try {
            const data = await secureFetch(`api/admin.php?action=get_user_notifications&uid=${uid}`);

            if (data.notifications.length === 0) {
                area.innerHTML = `
                    <div class="notif-user-header">
                        <img src="${data.user.avatar}" class="notif-user-avatar">
                        <div>
                            <div class="notif-user-name">${data.user.username}</div>
                            <code style="color: var(--text-muted); font-size: 0.75rem;">${uid}</code>
                        </div>
                    </div>
                    <div class="no-results-state" style="padding: 40px;">
                        <div class="no-results-icon">📭</div>
                        <div class="no-results-text">This user has no notifications.</div>
                    </div>`;
                return;
            }

            area.innerHTML = `
                <div class="notif-user-header">
                    <img src="${data.user.avatar}" class="notif-user-avatar">
                    <div style="flex: 1;">
                        <div class="notif-user-name">${data.user.username}</div>
                        <code style="color: var(--text-muted); font-size: 0.75rem;">${uid}</code>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <span class="notif-count-badge">${data.notifications.length} notification${data.notifications.length !== 1 ? 's' : ''}</span>
                        <button onclick="clearAllUserNotifs('${uid}')" class="notif-clear-all-btn">CLEAR ALL</button>
                    </div>
                </div>
                <div class="notif-feed-list">
                    ${data.notifications.map(n => renderNotifItem(n, true)).join('')}
                </div>`;
        } catch (e) {
            area.innerHTML = `<div class="no-results-state"><div class="no-results-icon">⚠️</div><div class="no-results-text" style="color:#ff4e4e;">Lookup failed: ${e.message}</div></div>`;
        }
    }

    function renderNotifItem(n, showDelete = false) {
        const readClass = n.is_read == 0 ? 'notif-feed-unread' : '';
        return `
            <div class="notif-feed-item ${readClass}" id="notif-item-${n.id}">
                <div class="notif-feed-icon">${getNotifIcon(n.type)}</div>
                <div class="notif-feed-body">
                    <div class="notif-feed-title">${n.title}</div>
                    <div class="notif-feed-msg">${n.message}</div>
                    <div class="notif-feed-meta">
                        <span class="notif-feed-type">${n.type}</span>
                        ${n.username ? `<span>→ ${n.username}</span>` : ''}
                        <span>${formatTimeAgo(n.time_sec)}</span>
                        ${n.is_read == 0 ? '<span class="notif-unread-dot">UNREAD</span>' : ''}
                        ${n.link ? `<span style="color: var(--accent-secondary);">${n.link}</span>` : ''}
                    </div>
                </div>
                ${showDelete ? `<button class="notif-feed-delete" onclick="adminDeleteNotif(${n.id})" title="Delete">✕</button>` : ''}
            </div>`;
    }

    async function adminDeleteNotif(id) {
        try {
            const fd = new FormData();
            fd.append('ajax_action', 'admin_delete_notification');
            fd.append('notification_id', id);
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            
            const el = document.getElementById(`notif-item-${id}`);
            if (el) {
                el.style.transition = 'all 0.3s';
                el.style.opacity = '0';
                el.style.transform = 'translateX(30px)';
                setTimeout(() => el.remove(), 300);
            }
        } catch (e) {
            Swal.fire({ title: 'Delete Failed', text: e.message, icon: 'error' });
        }
    }

    async function clearAllUserNotifs(uid) {
        const confirm = await Swal.fire({
            title: 'Clear All?',
            text: 'This will permanently delete all notifications for this user.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Clear All',
            confirmButtonColor: '#ff4e4e'
        });
        if (!confirm.isConfirmed) return;

        try {
            const fd = new FormData();
            fd.append('ajax_action', 'admin_delete_notification');
            fd.append('notification_id', 'all');
            fd.append('target_user_id', uid);
            await secureFetch('api/admin.php', { method: 'POST', body: fd });
            lookupUserNotifs();
        } catch (e) {
            Swal.fire({ title: 'Clear Failed', text: e.message, icon: 'error' });
        }
    }

    // ============================================
    // GLOBAL FEED
    // ============================================
    async function loadGlobalFeed() {
        const feed = document.getElementById('globalNotifFeed');
        try {
            const data = await secureFetch('api/admin.php?action=get_recent_notifications');
            if (data.notifications.length === 0) {
                feed.innerHTML = '<div class="no-results-state" style="padding: 40px;"><div class="no-results-icon">📭</div><div class="no-results-text">No notifications have been sent yet.</div></div>';
                return;
            }
            feed.innerHTML = `<div class="notif-feed-list">${data.notifications.map(n => renderNotifItem(n, false)).join('')}</div>`;
        } catch (e) {
            feed.innerHTML = `<div class="no-results-state"><div class="no-results-icon">⚠️</div><div class="no-results-text" style="color:#ff4e4e;">${e.message}</div></div>`;
        }
    }

    // Load global feed on page load
    window.addEventListener('load', loadGlobalFeed);
</script>

<?php require_once 'footer.php'; ?>
