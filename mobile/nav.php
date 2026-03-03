<?php
/**
 * MOBILE NAVIGATION
 * Compact header + bottom tab bar for mobile views.
 * Includes core JS: secureFetch(), mana, notifications.
 */
require_once __DIR__ . '/../config.php';
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Giant Notice Banner - Mobile */
    .giant-banner {
        width: 100%;
        padding: 10px 15px;
        background: linear-gradient(90deg, rgba(255, 0, 234, 0.1), rgba(0, 229, 255, 0.1));
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        z-index: 2001;
        overflow: hidden;
    }
    
    .giant-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
    }

    /* Animation Keyframes */
    @keyframes bannerShine {
        0% { left: -100%; }
        20% { left: 100%; }
        100% { left: 100%; }
    }

    @keyframes infoPulse {
        0%, 100% { box-shadow: inset 0 0 15px rgba(0, 229, 255, 0.05); }
        50% { box-shadow: inset 0 0 25px rgba(0, 229, 255, 0.1); }
    }

    @keyframes warningPulse {
        0%, 100% { box-shadow: inset 0 0 20px rgba(255, 128, 0, 0.1); }
        50% { box-shadow: inset 0 0 35px rgba(255, 128, 0, 0.2); }
    }

    @keyframes dangerPulse {
        0%, 100% { box-shadow: inset 0 0 25px rgba(255, 78, 78, 0.2); background: linear-gradient(90deg, rgba(255, 78, 78, 0.15), rgba(139, 0, 0, 0.2)); }
        50% { box-shadow: inset 0 0 45px rgba(255, 78, 78, 0.3); background: linear-gradient(90deg, rgba(255, 78, 78, 0.25), rgba(139, 0, 0, 0.35)); }
    }

    /* Type-specific Animations */
    .giant-banner.info {
        animation: infoPulse 6s infinite ease-in-out;
    }
    .giant-banner.info::before {
        animation: bannerShine 12s infinite linear;
    }

    .giant-banner.warning {
        animation: warningPulse 3s infinite ease-in-out;
    }
    .giant-banner.warning::before {
        animation: bannerShine 8s infinite linear;
    }

    .giant-banner.critical, .giant-banner.danger {
        animation: dangerPulse 1.5s infinite ease-in-out;
    }
    .giant-banner.critical::before, .giant-banner.danger::before {
        animation: bannerShine 4s infinite linear;
    }

    .giant-banner.critical, .giant-banner.danger {
        background: linear-gradient(90deg, rgba(255, 78, 78, 0.15), rgba(139, 0, 0, 0.2));
        border-bottom: 1px solid rgba(255, 78, 78, 0.3);
    }
    
    .giant-banner.critical .banner-icon, .giant-banner.danger .banner-icon { color: #ff4e4e; filter: drop-shadow(0 0 5px #ff4e4e); }
    .giant-banner.critical .banner-text, .giant-banner.danger .banner-text { color: #ffbcbc; text-shadow: 0 0 8px rgba(255, 78, 78, 0.4); }

    .giant-banner.warning {
        background: linear-gradient(90deg, rgba(255, 128, 0, 0.15), rgba(255, 165, 0, 0.1));
        border-bottom: 1px solid rgba(255, 128, 0, 0.3);
    }
    
    .giant-banner.warning .banner-icon { color: #ff8000; filter: drop-shadow(0 0 5px #ff8000); }
    .giant-banner.warning .banner-text { color: #ffcc99; text-shadow: 0 0 8px rgba(255, 128, 0, 0.4); }

    .giant-banner.info {
        background: linear-gradient(90deg, rgba(0, 229, 255, 0.1), rgba(0, 100, 255, 0.1));
        border-bottom: 1px solid var(--accent-secondary);
    }
    
    .giant-banner.info .banner-icon { color: var(--accent-secondary); filter: drop-shadow(0 0 5px var(--accent-secondary)); }
    .giant-banner.info .banner-text { color: #ccf5ff; text-shadow: 0 0 8px rgba(0, 229, 255, 0.4); }

    .banner-icon {
        font-size: 1rem;
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .banner-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.65rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-align: center;
        line-height: 1.2;
    }
</style>
<?php
$user_id = $_SESSION['user_data']['id'] ?? null;
$is_admin = $user_id && isAdmin($user_id);
$current_page = basename($_SERVER['PHP_SELF']);
$prefix = '/';
$nav_subtitle = $nav_subtitle ?? 'Dashboard';

$nav_avatar = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';
if (isset($_SESSION['user_data']['avatar']) && $_SESSION['user_data']['avatar']) {
    $nav_avatar = "https://cdn.discordapp.com/avatars/{$_SESSION['user_data']['id']}/{$_SESSION['user_data']['avatar']}.png?size=64";
}

// Banner logic
$banner_enabled = $GLOBAL_BANNER_ENABLED ?? false;
$banner_text    = $GLOBAL_BANNER_TEXT ?? '';
$banner_type    = $GLOBAL_BANNER_TYPE ?? 'info';
?>

<?php if ($banner_enabled && !empty($banner_text)): ?>
    <div class="giant-banner <?php echo htmlspecialchars($banner_type); ?>">
        <div class="banner-icon">
            <?php if ($banner_type === 'critical' || $banner_type === 'danger'): ?>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            <?php elseif ($banner_type === 'warning'): ?>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            <?php endif; ?>
        </div>
        <div class="banner-text"><?php echo htmlspecialchars($banner_text); ?></div>
        <div class="banner-icon">
            <?php if ($banner_type === 'critical' || $banner_type === 'danger'): ?>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            <?php elseif ($banner_type === 'warning'): ?>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Mobile Header -->
<header class="m-header">
    <div class="m-header-logo">
        <img src="/illusionary.png" alt="Logo">
        <div>
            <span class="gradient-text m-title">ILLUSIONARY</span>
            <span class="m-subtitle"><?php echo htmlspecialchars($nav_subtitle); ?></span>
        </div>
    </div>
    <div class="m-header-right">
        <div class="m-mana-pill" id="nav-mana-pill" style="display: none;">
            <img src="/images/images/red_mana.png" alt="Mana">
            <span class="m-mana-val" id="nav-mana-value">0</span>
        </div>
        <div id="mana-claim-container" style="display: none;">
            <button onclick="claimMana()" class="m-claim-btn">Claim</button>
        </div>
        <div id="mana-claimed-badge" class="m-claimed-badge" style="display: none;">✓</div>
        <!-- Notification Bell -->
        <div class="m-notif-bell" onclick="toggleNotifSheet()">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <div class="m-notif-badge" id="mNotifBadge">0</div>
        </div>
        <img src="<?php echo $nav_avatar; ?>" class="m-avatar" alt="Avatar">
    </div>
</header>

<!-- Notification Sheet (full-screen overlay) -->
<div class="m-notif-sheet" id="mNotifSheet">
    <div class="m-notif-sheet-header">
        <span>NOTIFICATIONS</span>
        <div style="display: flex; gap: 14px; align-items: center;">
            <a href="javascript:void(0)" onclick="markAllRead()" class="m-notif-action">MARK READ</a>
            <a href="javascript:void(0)" onclick="deleteAllNotifications()" class="m-notif-action m-notif-action-danger">CLEAR ALL</a>
            <button class="m-notif-close" onclick="toggleNotifSheet()">✕</button>
        </div>
    </div>
    <div class="m-notif-list" id="mNotifList">
        <div class="m-notif-empty">No new transmissions</div>
    </div>
</div>

<!-- Bottom Tab Bar -->
<nav class="m-tab-bar">
    <a href="index.php" class="m-tab <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
        </svg>
        Cards
    </a>
    <a href="draw.php" class="m-tab <?php echo $current_page === 'draw.php' ? 'active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        Forge
    </a>
    <a href="trade.php" class="m-tab <?php echo $current_page === 'trade.php' ? 'active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="17 1 21 5 17 9"></polyline>
            <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
            <polyline points="7 23 3 19 7 15"></polyline>
            <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
        </svg>
        Trade
    </a>
    <a href="pawnshop.php" class="m-tab <?php echo $current_page === 'pawnshop.php' ? 'active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 3h18v18H3zM12 8v8M8 12h8"></path>
        </svg>
        Pawn
    </a>
    <a href="about.php" class="m-tab <?php echo $current_page === 'about.php' ? 'active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
        About
    </a>
    <a href="tos.php" class="m-tab <?php echo $current_page === 'tos.php' ? 'active' : ''; ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        Terms
    </a>
    <a href="/auth.php?logout=1" class="m-tab tab-logout" onclick="sessionStorage.removeItem('null_clicks'); sessionStorage.removeItem('null_song');">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        Logout
    </a>
</nav>

<script>
    /**
     * SECURE FETCH
     * Handles standard JSON responses and correctly parses 500/400 errors 
     * to show detailed system feedback to the user.
     */
    async function secureFetch(url, options = {}) {
        const response = await fetch(url, options);
        let data;
        try {
            data = await response.json();
        } catch (e) {
            if (!response.ok) throw new Error(`Server Connectivity Issue (${response.status})`);
            return null;
        }
        if (!response.ok) {
            throw new Error(data.error || `System Error ${response.status}`);
        }
        return data;
    }

    async function updateManaStatus() {
        try {
            const prefix = '<?php echo $prefix; ?>';
            const d = await secureFetch(`${prefix}api/collection.php?action=hydrate_collection&page=1`);
            if (d && d.user) {
                const pill = document.getElementById('nav-mana-pill');
                const val = document.getElementById('nav-mana-value');
                const btn = document.getElementById('mana-claim-container');
                const badge = document.getElementById('mana-claimed-badge');
                
                if (pill) pill.style.display = 'flex';
                if (val) val.innerText = d.user.tokens.toLocaleString();
                if (btn) btn.style.display = d.user.can_claim ? 'block' : 'none';
                if (badge) badge.style.display = d.user.can_claim ? 'none' : 'block';
            }
        } catch (e) { console.error("Mana update failed", e); }
    }

    async function claimMana() {
        const prefix = '<?php echo $prefix; ?>';
        try {
            const result = await secureFetch(`${prefix}api/collection.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=claim_mana'
            });
            if (result.success) {
                Swal.fire({
                    title: 'Mana Claimed!',
                    text: result.message,
                    icon: 'success',
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000
                });
                updateManaStatus();
                if (typeof hydrateCollection === 'function') hydrateCollection();
            }
        } catch (e) { 
            Swal.fire({
                title: 'Operation Failed',
                text: e.message,
                icon: 'error'
            });
        }
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

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.innerText;
            btn.innerText = 'COPIED!';
            btn.style.background = 'var(--accent-secondary)';
            btn.style.color = '#000';
            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.background = 'rgba(0, 229, 255, 0.1)';
                btn.style.color = 'var(--accent-secondary)';
            }, 2000);
        });
    }

    // Console Warning
    console.log('%cILLUSIONARY SECURITY WARNING', 'color: #ff4e4e; font-size: 20px; font-weight: bold;');
    console.log('%cDo NOT paste code here unless you understand what you are doing. Sharing session cookies can compromise your account.', 'color: #ff4e4e; font-size: 14px;');

    // --- NOTIFICATION SYSTEM ---
    async function updateNotifBadge() {
        if (!'<?php echo $user_id; ?>') return;
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'get_unread_count');
            const d = await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            const badge = document.getElementById('mNotifBadge');
            if (badge) {
                if (d.count > 0) {
                    badge.innerText = d.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        } catch (e) { console.error('Notif badge update failed', e); }
    }

    function toggleNotifSheet() {
        const sheet = document.getElementById('mNotifSheet');
        const isOpen = sheet.classList.contains('open');
        if (isOpen) {
            sheet.classList.remove('open');
            document.body.style.overflow = '';
        } else {
            sheet.classList.add('open');
            document.body.style.overflow = 'hidden';
            hydrateNotifications();
        }
    }

    async function hydrateNotifications() {
        const list = document.getElementById('mNotifList');
        list.innerHTML = '<div class="m-notif-empty"><div class="skeleton" style="height:18px;width:70%;margin:0 auto 10px;"></div><div class="skeleton" style="height:40px;width:90%;margin:0 auto;"></div></div>';
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'get_recent');
            const d = await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            if (d.notifications.length === 0) {
                list.innerHTML = '<div class="m-notif-empty">No notifications available</div>';
                return;
            }
            list.innerHTML = d.notifications.map(n => `
                <div class="m-notif-item-wrap">
                    <a href="${n.link || 'javascript:void(0)'}" class="m-notif-item ${n.is_read == 0 ? 'unread' : ''}" onclick="markAsRead(${n.id})">
                        <div class="m-notif-item-icon">
                            ${n.type === 'trade_request' ? '📢' : (n.type.includes('accepted') ? '✅' : (n.type.includes('declined') ? '❌' : 'ℹ️'))}
                        </div>
                        <div class="m-notif-item-body">
                            <div class="m-notif-item-title">${n.title}</div>
                            <div class="m-notif-item-msg">${n.message}</div>
                            <div class="m-notif-item-time">${formatTimeAgo(n.time_sec)}</div>
                        </div>
                    </a>
                    <button class="m-notif-delete" onclick="event.stopPropagation(); deleteNotification(${n.id})">✕</button>
                </div>
            `).join('');
        } catch (e) { list.innerHTML = `<div class="m-notif-empty" style="color:#ff4e4e">Failed to load notifications: ${e.message}</div>`; }
    }

    async function markAsRead(id) {
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'mark_read');
            fd.append('notification_id', id);
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifBadge();
        } catch (e) { console.error(e); }
    }

    async function deleteNotification(id) {
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('notification_id', id);
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifBadge();
            hydrateNotifications();
        } catch (e) { console.error(e); }
    }

    async function deleteAllNotifications() {
        if (!confirm('Permanently delete all notifications?')) return;
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('notification_id', 'all');
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifBadge();
            hydrateNotifications();
        } catch (e) { console.error(e); }
    }

    async function markAllRead() {
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'mark_read');
            fd.append('notification_id', 'all');
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifBadge();
            hydrateNotifications();
        } catch (e) { console.error(e); }
    }

    updateManaStatus();
    updateNotifBadge();
    setInterval(updateNotifBadge, 30000);
</script>
