<?php
/**
 * MOBILE ABOUT PAGE
 * Simplified layout for project info, credits, and disclaimers.
 */
$session_lifetime = 10 * 24 * 60 * 60;
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path'     => '/',
    'domain'   => '', 
    'secure'   => true, 
    'httponly'  => true,
    'samesite' => 'Lax'
]);

session_name('ILLUSIONARY_SID');
session_start();

require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php?redirect=/about.php");
    exit;
}

// Fetch developer data
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';


$env = loadEnv($ENV_PATH);
$token = $env['DISCORD_TOKEN'] ?? '';
$dev_data = getDiscordUser('332684782888550410', $token);

$collaborators = [];
$collab_ids = ['613130816809336842', '701867923358351511'];
foreach ($collab_ids as $id) {
    $collaborators[] = getDiscordUser($id, $token);
}

$collaborators[] = [
    'username' => 'Null',
    'avatar' => '/happynull.png'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | About</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <link rel="stylesheet" href="/variations.css">
    <?php 
    require_once __DIR__ . '/../theme-config.php';
    injectTheme($THEME);
    ?>
</head>
<body>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'About Illusionary';
    include 'nav.php'; 
    ?>

    <main class="m-container">
        <div class="m-about-content">
            <div class="m-about-header">
                <img src="/illusionary.png" alt="Illusionary Logo">
                <h1 class="gradient-text">ILLUSIONARY</h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Digital Card Collection & Trading Platform</p>
            </div>

            <div class="m-about-section" style="text-align: center;">
                <h2>The Project</h2>
                <p>Illusionary is a web-based extension of the <strong style="color:#fff;">Illusionary Discord Bot</strong>. This dashboard provides a premium experience for collectors to browse their vaults, manage their collections, and trade with other users in a secure, visual environment.</p>
                <p>All cards, mana, and progress are synced in real-time between the Discord bot and this platform.</p>
            </div>

            <!-- Credits -->
            <div class="m-about-section" style="text-align: center; margin-top: 30px;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                    <!-- Main Developer -->
                    <div style="display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.05); padding: 8px 14px; border-radius: 50px; border: 1px solid var(--glass-border);">
                        <?php if (!empty($dev_data['avatar'])): ?>
                            <img src="<?php echo $dev_data['avatar']; ?>" style="width: 24px; height: 24px; border-radius: 50%; border: 1px solid var(--accent-primary);">
                        <?php endif; ?>
                        <span style="font-size: 0.8rem; color: #fff;">Developed by <strong><?php echo htmlspecialchars($dev_data['username'] ?? 'certified_pen'); ?></strong></span>
                    </div>

                    <!-- Collaborators -->
                    <?php if (!empty($collaborators)): ?>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                        <span style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">In collaboration with</span>
                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
                            <?php foreach ($collaborators as $collab): ?>
                                <div style="display: flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.03); padding: 4px 8px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                                    <?php if (!empty($collab['avatar'])): ?>
                                        <img src="<?php echo $collab['avatar']; ?>" style="width: 18px; height: 18px; border-radius: 50%;">
                                    <?php endif; ?>
                                    <span style="font-size: 0.7rem; color: rgba(255,255,255,0.7);"><?php echo htmlspecialchars($collab['username']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Disclaimers -->
            <div class="m-disclaimer-grid">
                <div class="m-disclaimer-box">
                    <h3 style="color: #ff4e4e;">Security</h3>
                    <p>Never share your <strong>ILLUSIONARY_SID</strong>. It provides full access to your collection.</p>
                </div>
                <div class="m-disclaimer-box">
                    <h3 style="color: var(--text-muted);">Legal</h3>
                    <p>Independent project unaffiliated with Discord Inc. Assets belong to their creators.</p>
                </div>
                <div class="m-disclaimer-box">
                    <h3 style="color: var(--accent-secondary);">Privacy</h3>
                    <p>Uses essential cookies (SID) and Cloudflare/Turnstile for security and DDoS protection.</p>
                </div>
            </div>

            <p style="font-size: 0.65rem; opacity: 0.4; text-align: center; margin-top: 25px;">Version 2.6.0 &copy; <?php echo date('Y'); ?> Illusionary. All rights reserved.</p>
        </div>
    </main>
</body>
</html>
