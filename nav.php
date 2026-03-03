<?php
require_once 'config.php';
?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .swal2-container {
        z-index: 9999999 !important;
    }
    .swal2-popup {
        background: rgba(12, 10, 21, 0.9) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid var(--glass-border) !important;
        border-radius: 24px !important;
        color: #fff !important;
    }
    .swal2-title { color: #fff !important; font-family: 'Outfit', sans-serif !important; }
    .swal2-html-container { color: var(--text-muted) !important; }
    .swal2-confirm { background: var(--accent-primary) !important; border-radius: 12px !important; }
    .swal2-deny { border-radius: 12px !important; }
    .swal2-cancel { border-radius: 12px !important; }

    /* Notifications UI */
    .notif-wrapper { position: relative; display: flex; align-items: center; }
    .notif-bell { 
        cursor: pointer; position: relative; transition: color 0.2s; color: var(--text-muted); padding: 5px;
    }
    .notif-bell:hover { color: var(--accent-secondary); }
    .notif-badge {
        position: absolute; top: -5px; right: -5px; background: var(--accent-primary); color: #fff;
        font-size: 0.6rem; font-weight: 900; min-width: 18px; height: 18px; border-radius: 50%;
        display: none; align-items: center; justify-content: center; border: 2px solid var(--bg-color);
        box-shadow: 0 0 10px rgba(255, 0, 234, 0.4);
    }
    .notif-dropdown {
        position: absolute; top: calc(100% + 15px); right: 0; width: 320px; 
        background: rgba(12, 10, 21, 0.98); backdrop-filter: blur(20px); border: 1px solid var(--glass-border);
        border-radius: 16px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); z-index: 1000;
        display: none; flex-direction: column; overflow: hidden; animation: fadeInNotif 0.2s ease-out;
    }
    @keyframes fadeInNotif { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .notif-header { padding: 15px 20px; border-bottom: 1px solid var(--glass-border); font-family: 'Outfit'; font-weight: 800; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center; }
    .notif-list { max-height: 400px; overflow-y: auto; scrollbar-width: thin; }
    .notif-item { 
        padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.03); transition: all 0.2s; cursor: pointer; text-decoration: none; display: block;
    }
    .notif-item:hover { background: rgba(255,255,255,0.03); }
    .notif-item.unread { background: rgba(0, 229, 255, 0.03); border-left: 3px solid var(--accent-secondary); }
    .notif-item-title { font-weight: 800; font-size: 0.8rem; color: #fff; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
    .notif-item-msg { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }
    .notif-item-time { font-size: 0.6rem; color: #444; margin-top: 8px; text-transform: uppercase; font-weight: 800; }
    .notif-empty { padding: 40px 20px; text-align: center; color: #444; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }

    /* Giant Notice Banner */
    .giant-banner {
        width: 100%;
        padding: 12px 20px;
        background: linear-gradient(90deg, rgba(255, 0, 234, 0.1), rgba(0, 229, 255, 0.1));
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
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
        0%, 100% { box-shadow: inset 0 0 20px rgba(0, 229, 255, 0.05); }
        50% { box-shadow: inset 0 0 40px rgba(0, 229, 255, 0.1); }
    }

    @keyframes warningPulse {
        0%, 100% { box-shadow: inset 0 0 30px rgba(255, 128, 0, 0.1); }
        50% { box-shadow: inset 0 0 50px rgba(255, 128, 0, 0.2); }
    }

    @keyframes dangerPulse {
        0%, 100% { box-shadow: inset 0 0 40px rgba(255, 78, 78, 0.2); background: linear-gradient(90deg, rgba(255, 78, 78, 0.15), rgba(139, 0, 0, 0.2)); }
        50% { box-shadow: inset 0 0 70px rgba(255, 78, 78, 0.3); background: linear-gradient(90deg, rgba(255, 78, 78, 0.25), rgba(139, 0, 0, 0.35)); }
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
    
    .giant-banner.critical .banner-icon, .giant-banner.danger .banner-icon { color: #ff4e4e; filter: drop-shadow(0 0 8px #ff4e4e); }
    .giant-banner.critical .banner-text, .giant-banner.danger .banner-text { color: #ffbcbc; text-shadow: 0 0 10px rgba(255, 78, 78, 0.5); }

    .giant-banner.warning {
        background: linear-gradient(90deg, rgba(255, 128, 0, 0.15), rgba(255, 165, 0, 0.1));
        border-bottom: 1px solid rgba(255, 128, 0, 0.3);
    }
    
    .giant-banner.warning .banner-icon { color: #ff8000; filter: drop-shadow(0 0 8px #ff8000); }
    .giant-banner.warning .banner-text { color: #ffcc99; text-shadow: 0 0 10px rgba(255, 128, 0, 0.5); }

    .giant-banner.info {
        background: linear-gradient(90deg, rgba(0, 229, 255, 0.1), rgba(0, 100, 255, 0.1));
        border-bottom: 1px solid var(--accent-secondary);
    }
    
    .giant-banner.info .banner-icon { color: var(--accent-secondary); filter: drop-shadow(0 0 8px var(--accent-secondary)); }
    .giant-banner.info .banner-text { color: #ccf5ff; text-shadow: 0 0 10px rgba(0, 229, 255, 0.5); }

    .banner-icon {
        font-size: 1.2rem;
        display: flex;
        align-items: center;
    }

    .banner-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 0.85rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-align: center;
    }

    @media (max-width: 768px) {
        .banner-text { font-size: 0.7rem; }
        .giant-banner { padding: 10px 15px; }
    }
</style>
<?php
$user_id = $_SESSION['user_data']['id'] ?? null;
$is_admin = $user_id && isAdmin($user_id);

// Get current page and directory depth for relative paths
$current_page = basename($_SERVER['PHP_SELF']);
$is_in_subdir = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/mobile/') !== false);
$is_mobile_dir = strpos($_SERVER['REQUEST_URI'], '/mobile/') !== false;
$prefix = $is_in_subdir ? '../' : '';

// Metadata for navbar (can be overridden before including)
$nav_subtitle = $nav_subtitle ?? 'Dashboard';

// Banner logic
$banner_enabled = $GLOBAL_BANNER_ENABLED ?? false;
$banner_text    = $GLOBAL_BANNER_TEXT ?? '';
$banner_type    = $GLOBAL_BANNER_TYPE ?? 'info';
?>

<?php if ($banner_enabled && !empty($banner_text)): ?>
    <div class="giant-banner <?php echo htmlspecialchars($banner_type); ?>">
        <div class="banner-icon">
            <?php if ($banner_type === 'critical' || $banner_type === 'danger'): ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            <?php elseif ($banner_type === 'warning'): ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            <?php endif; ?>
        </div>
        <div class="banner-text"><?php echo htmlspecialchars($banner_text); ?></div>
        <div class="banner-icon">
            <?php if ($banner_type === 'critical' || $banner_type === 'danger'): ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            <?php elseif ($banner_type === 'warning'): ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<header class="navbar">
    <div style="justify-self: start; display: flex; align-items: center;">
        <div class="hamburger-menu" onclick="toggleSidebar()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
    <div class="logo" style="display: flex; align-items: center; justify-content: center; text-align: center;">
        <img src="<?php echo $prefix; ?>illusionary.png" alt="Illusionary Logo" style="width: 38px; height: 38px; margin-right: 12px; filter: drop-shadow(0 0 10px var(--accent-primary));">
        <div style="display: flex; flex-direction: column; align-items: flex-start;">
            <span class="gradient-text" style="font-family: 'Outfit'; font-weight: 800; font-size: 1.4rem; letter-spacing: 2px; line-height: 1;">ILLUSIONARY</span>
            <span style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; margin-top: 2px;"><?php echo htmlspecialchars($nav_subtitle); ?></span>
        </div>
    </div>

    <div class="nav-right" style="justify-self: end; display: flex; align-items: center; gap: 20px;">
        <div class="mana-pill" style="margin-right: 10px; display: none;" id="nav-mana-pill">
            <img src="<?php echo $prefix; ?>images/images/red_mana.png" class="mana-icon" alt="Mana">
            <span class="mana-label">Mana:</span>
            <span id="nav-mana-value" class="mana-value">0</span>
        </div>
        
        <div id="mana-claim-container" style="margin-right: 10px; display: none;">
            <button onclick="claimMana()" class="claim-btn" style="padding: 6px 15px; font-size: 0.75rem;">Daily Mana</button>
        </div>
        <div id="mana-claimed-badge" class="claimed-badge" style="margin-right: 10px; opacity: 0.6; font-size: 0.6rem; padding: 4px 10px; display: none;">MANA CLAIMED</div>

        <?php if (isset($_SESSION['user_data'])): 
            $nav_avatar = $_SESSION['user_data']['avatar'] 
                ? "https://cdn.discordapp.com/avatars/{$_SESSION['user_data']['id']}/{$_SESSION['user_data']['avatar']}.png?size=64" 
                : "https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y";
        ?>
            <div style="display: flex; align-items: center; gap: 12px; border-left: 1px solid var(--glass-border); padding-left: 20px;">
                <span style="font-size: 0.8rem; font-weight: 600;"><?php echo htmlspecialchars($_SESSION['user_data']['username']); ?></span>
                <img src="<?php echo $nav_avatar; ?>" class="tiny-avatar navbar-avatar" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--glass-border);">
            </div>

            <!-- Notifications Bell -->
            <div class="notif-wrapper">
                <div class="notif-bell" onclick="toggleNotifications()">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <div class="notif-badge" id="notifBadge">0</div>
                </div>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span>NOTIFICATIONS</span>
                        <div style="display: flex; gap: 15px;">
                            <a href="javascript:void(0)" onclick="markAllRead()" style="color: var(--accent-secondary); text-decoration: none; font-size: 0.6rem; letter-spacing: 1px;">MARK ALL READ</a>
                            <a href="javascript:void(0)" onclick="deleteAllNotifications()" style="color: #ff4e4e; text-decoration: none; font-size: 0.6rem; letter-spacing: 1px;">CLEAR ALL</a>
                        </div>
                    </div>
                    <div class="notif-list" id="notifList">
                        <div class="notif-empty">No new transmissions</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($is_in_subdir): ?>
            <span class="active-badge" style="background: rgba(0, 229, 255, 0.1); color: var(--accent-secondary); border: 1px solid var(--accent-secondary); font-size: 0.6rem; padding: 4px 10px; border-radius: 20px; font-weight: 800;">ADMIN SECURE</span>
        <?php endif; ?>
        
        <a href="<?php echo $prefix; ?>reports/null-os.php" class="nav-icon-btn" title="Terminal" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-secondary)'" onmouseout="this.style.color='var(--text-muted)'">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="4 17 10 11 4 5"></polyline>
                <line x1="12" y1="19" x2="20" y2="19"></line>
            </svg>
        </a>
        
        <a href="<?php echo $is_mobile_dir ? 'auth.php?logout=1' : $prefix . 'auth.php?logout=1'; ?>" class="logout-btn" onclick="sessionStorage.removeItem('null_clicks'); sessionStorage.removeItem('null_song');">Logout</a>
    </div>
</header>

<div class="admin-sidebar" id="adminSidebar">
    <h2 class="gradient-text" style="font-size: 1.2rem; margin-bottom: 2rem; padding: 0 20px;">NAVIGATE</h2>
    <?php 
    $index_link = $is_mobile_dir ? 'index.php' : $prefix . 'index.php';
    $is_index_active = (!$is_in_subdir && $current_page === 'index.php') || ($is_mobile_dir && $current_page === 'index.php');
    ?>
    <a href="<?php echo $index_link; ?>" id="navIndex" class="sidebar-link <?php echo $is_index_active ? 'active' : ''; ?>">Collection View</a>
    <a href="<?php echo $is_mobile_dir ? 'draw.php' : $prefix . 'draw.php'; ?>" id="navForge" class="sidebar-link <?php echo $current_page === 'draw.php' ? 'active' : ''; ?>">
        Card Forge <span style="font-size: 0.7rem; margin-left: 5px; opacity: 0.6;"></span>
    </a>
    <a href="<?php echo $is_mobile_dir ? 'trade.php' : $prefix . 'trade.php'; ?>" id="navTrade" class="sidebar-link <?php echo $current_page === 'trade.php' ? 'active' : ''; ?>">Trading Center</a>
    <a href="<?php echo $is_mobile_dir ? 'pawnshop.php' : $prefix . 'pawnshop.php'; ?>" id="navPawn" class="sidebar-link <?php echo $current_page === 'pawnshop.php' ? 'active' : ''; ?>">Null's Pawnshop</a>
    <a href="<?php echo $is_mobile_dir ? 'about.php' : $prefix . 'about.php'; ?>" id="navAbout" class="sidebar-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>">About Illusionary</a>
    <a href="<?php echo $is_mobile_dir ? 'tos.php' : $prefix . 'tos.php'; ?>" id="navTos" class="sidebar-link <?php echo $current_page === 'tos.php' ? 'active' : ''; ?>">Terms of Service</a>
    
    <?php if ($is_admin): ?>
        <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 15px 20px;"></div>
        <h3 style="font-size: 0.6rem; color: var(--text-muted); padding: 0 20px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">Administration</h3>
        <a href="<?php echo $is_in_subdir ? 'index.php' : 'admin/index.php'; ?>" class="sidebar-link <?php echo ($is_in_subdir && $current_page === 'index.php') ? 'active' : ''; ?>">Overview</a>
        <a href="<?php echo $is_in_subdir ? 'explorer.php' : 'admin/explorer.php'; ?>" class="sidebar-link <?php echo $current_page === 'explorer.php' ? 'active' : ''; ?>">Card Explorer</a>
        <a href="<?php echo $is_in_subdir ? 'users.php' : 'admin/users.php'; ?>" class="sidebar-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">User Management</a>
        <a href="<?php echo $is_in_subdir ? 'system.php' : 'admin/system.php'; ?>" class="sidebar-link <?php echo $current_page === 'system.php' ? 'active' : ''; ?>">System Settings</a>
    <?php endif; ?>

    <div style="margin-top: auto; padding: 30px 20px; border-top: 1px solid rgba(255,255,255,0.05); text-align: center;">
        <img src="<?php echo $prefix; ?>illusionary.png" style="width: 50px; opacity: 0.15; filter: grayscale(1); margin-bottom: 15px;">
        <div style="font-size: 0.5rem; color: #333; margin-top: 8px; letter-spacing: 2px; font-weight: 800;">BUILD v2.9.0</div>
        <a href="javascript:void(0)" onclick="reportIssue()" style="display: inline-block; margin-top: 10px; font-size: 0.6rem; color: #ff4e4e; text-decoration: none; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
            Report a Bug
        </a>
    </div>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<script>
    function toggleSidebar(forceState = null) {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (forceState === true) {
            sidebar?.classList.add('active');
            overlay?.classList.add('active');
        } else if (forceState === false) {
            sidebar?.classList.remove('active');
            overlay?.classList.remove('active');
        } else {
            sidebar?.classList.toggle('active');
            overlay?.classList.toggle('active');
        }
    }
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
            throw new Error(data.error || data.message || `System Error ${response.status}`);
        }
        return data;
    }

    async function updateManaStatus() {
        // Disable player-specific mana sync if we are in the admin panel
        if (window.location.pathname.includes('/admin/')) return;
        
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
                    position: 'top-end',
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

    async function reportIssue() {
        const userId = '<?php echo $_SESSION['user_data']['id'] ?? 'Anonymous'; ?>';
        const page = '<?php echo $current_page; ?>';
        const fullUrl = window.location.href;
        const isAdmin = '<?php echo $is_admin ? "YES" : "NO"; ?>';
        const build = 'v2.9.0'; // Hardcoded sync with sidebar
        const timestamp = '<?php echo date('Y-m-d H:i:s'); ?>';
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const userAgent = navigator.userAgent;
        const resolution = `${window.screen.width}x${window.screen.height}`;

        const payload = `**BUG REPORT DIAGNOSTICS**\n` +
                        `Build: ${build}\n` +
                        `User ID: ${userId}\n` +
                        `Admin: ${isAdmin}\n` +
                        `Page: ${page}\n` +
                        `Full URL: ${fullUrl}\n` +
                        `Browser: ${userAgent}\n` +
                        `Resolution: ${resolution}\n` +
                        `Timezone: ${timezone}\n` +
                        `Timestamp: ${timestamp}`;

        Swal.fire({
            title: 'Report a Bug',
            html: `
                <div style="text-align: left; padding: 10px; font-family: 'Inter', sans-serif;">
                    <p style="margin-bottom: 20px; font-size: 0.85rem; line-height: 1.5;">Found a glitch or bug in the website? Please report it to the <b>Discord Server</b>.</p>
                    
                    <div style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); position: relative;">
                        <div style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; display: flex; justify-content: space-between;">
                            <span>Diagnostic Payload</span>
                            <span style="color: var(--accent-secondary);">BUILD ${build}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 0.7rem; color: var(--text-muted);">User / Admin:</span>
                            <span style="font-size: 0.7rem; color: #fff; font-family: monospace;">${userId} (${isAdmin})</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Location:</span>
                            <span style="font-size: 0.7rem; color: #fff; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;" title="${fullUrl}">${page}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Browser/Device:</span>
                            <span style="font-size: 0.7rem; color: #fff; text-align: right; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;" title="${userAgent}">${userAgent}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Resolution:</span>
                            <span style="font-size: 0.7rem; color: #fff;">${resolution}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Timezone:</span>
                            <span style="font-size: 0.7rem; color: #fff;">${timezone}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.7rem; color: var(--text-muted);">Timestamp:</span>
                            <span style="font-size: 0.7rem; color: #fff;">${timestamp}</span>
                        </div>

                        <button onclick="copyToClipboard(\`${payload.replace(/`/g, '\\`')}\`, this)" style="margin-top: 15px; width: 100%; padding: 8px; background: rgba(0, 229, 255, 0.1); border: 1px solid var(--accent-secondary); color: var(--accent-secondary); border-radius: 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; transition: all 0.2s;">
                            COPY DATA TO CLIPBOARD
                        </button>
                    </div>
                </div>
            `,
            background: 'rgba(12, 10, 21, 0.95)',
            color: '#fff',
            showConfirmButton: false,
            showCloseButton: true
        });
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalText = btn.innerText;
            btn.innerText = 'COPIED TO CLIPBOARD!';
            btn.style.background = 'var(--accent-secondary)';
            btn.style.color = '#000';
            setTimeout(() => {
                btn.innerText = originalText;
                btn.style.background = 'rgba(0, 229, 255, 0.1)';
                btn.style.color = 'var(--accent-secondary)';
            }, 2000);
        });
    }

    // --- NOTIFICATION COMPONENT ---
    async function updateNotifStatus() {
        if (!'<?php echo $user_id; ?>') return;
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'get_unread_count');
            
            const d = await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            const badge = document.getElementById('notifBadge');
            if (badge) {
                if (d.count > 0) {
                    badge.innerText = d.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        } catch (e) { console.error("Notif update failed", e); }
    }

    function toggleNotifications() {
        const dropdown = document.getElementById('notifDropdown');
        const isVisible = dropdown.style.display === 'flex';
        
        // Close other dropdowns if any
        
        if (isVisible) {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'flex';
            hydrateNotifications();
        }
    }

    async function hydrateNotifications() {
        const list = document.getElementById('notifList');
        list.innerHTML = '<div style="padding:40px; text-align:center;"><div class="skeleton" style="height:20px; width:80%; margin:0 auto 10px;"></div><div class="skeleton" style="height:40px; width:90%; margin:0 auto;"></div></div>';
        
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'get_recent');
            const d = await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            
            if (d.notifications.length === 0) {
                list.innerHTML = '<div class="notif-empty">No notifications available</div>';
                return;
            }

            list.innerHTML = d.notifications.map(n => `
                <div class="notif-item-wrapper" style="position: relative;">
                    <a href="${n.link || 'javascript:void(0)'}" class="notif-item ${n.is_read == 0 ? 'unread' : ''}" onclick="markAsRead(${n.id})">
                        <div class="notif-item-title">
                            ${n.type === 'trade_request' ? '📢' : (n.type.includes('accepted') ? '✅' : 'ℹ️')}
                            ${n.title}
                        </div>
                        <div class="notif-item-msg">${n.message}</div>
                        <div class="notif-item-time">${formatTimeAgo(n.time_sec)}</div>
                    </a>
                    <button onclick="event.stopPropagation(); deleteNotification(${n.id})" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: #444; font-size: 1rem; cursor: pointer; padding: 5px; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='#ff4e4e'" onmouseout="this.style.color='#444'">×</button>
                </div>
            `).join('');
        } catch (e) { list.innerHTML = `<div class="notif-empty" style="color:#ff4e4e">Failed to load notifications: ${e.message}</div>`; }
    }

    async function markAsRead(id) {
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'mark_read');
            fd.append('notification_id', id);
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifStatus();
        } catch (e) { console.error(e); }
    }

    async function deleteNotification(id) {
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('notification_id', id);
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifStatus();
            hydrateNotifications();
        } catch (e) { console.error(e); }
    }

    async function deleteAllNotifications() {
        if (!confirm("Permanently delete all notifications?")) return;
        try {
            const prefix = '<?php echo $prefix; ?>';
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('notification_id', 'all');
            await secureFetch(`${prefix}api/notifications.php`, { method: 'POST', body: fd });
            updateNotifStatus();
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
            updateNotifStatus();
            hydrateNotifications();
        } catch (e) { console.error(e); }
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

    // Close on click outside
    document.addEventListener('click', (e) => {
        const wrapper = document.querySelector('.notif-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('notifDropdown').style.display = 'none';
        }
    });

    // --- FORCE UNREGISTER SERVICE WORKERS ---
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(registrations => {
            for (let registration of registrations) {
                registration.unregister();
                console.log('[PWA] ServiceWorker unregistered');
            }
        });
    }

    updateManaStatus();
    updateNotifStatus();
    setInterval(updateNotifStatus, 60000); // Check every minute
</script>
