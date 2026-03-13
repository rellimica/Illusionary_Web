<?php
/**
 * ABOUT PAGE
 * Provides information about the Illusionary project.
 */
// Set secure session parameters
$session_lifetime = 10 * 24 * 60 * 60;
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path'     => '/',
    'domain'   => '', 
    'secure'   => true, 
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_name('ILLUSIONARY_SID');
session_start();

// Mobile Detection
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua)) {
    header("Location: mobile/about.php");
    exit;
}

require_once 'config.php';

if (isset($_GET['logout'])) {
    session_destroy();
    $kick = isset($_GET['kick']) ? '&kick=1' : '';
    header("Location: auth.php?logout=1$kick"); 
    exit;
}

// AUTHENTICATION CHECK
if (!isset($_SESSION['user_authenticated'])) {
    header("Location: auth.php?redirect=about.php");
    exit;
}

// FETCH DEVELOPER DATA
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

// Add Null as a special collaborator
$collaborators[] = [
    'username' => 'Null',
    'avatar' => 'happynull.png'
];

$IMAGES_PATH = 'images/images/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | About</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="variations.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        .about-content {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: rgba(12, 10, 21, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            color: var(--text-main);
        }
        .about-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .about-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .about-section {
            margin-bottom: 30px;
        }
        .about-section h2 {
            font-size: 1.5rem;
            color: var(--accent-primary);
            margin-bottom: 15px;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .about-section p {
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 15px;
        }
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .feature-item {
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
        }
        .feature-item h3 {
            font-size: 1rem;
            margin-bottom: 8px;
            color: #fff;
        }
        .feature-item p {
            font-size: 0.85rem;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <?php include 'mobile-block.php'; ?>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'About Illusionary';
    include 'nav.php'; 
    ?>

    <main class="container">
        <div class="about-content">
            <div class="about-header">
                <img src="illusionary.png" alt="Illusionary Logo" style="width: 80px; margin-bottom: 20px; filter: drop-shadow(0 0 20px var(--accent-primary));">
                <h1 class="gradient-text">ILLUSIONARY</h1>
                <p>Digital Card Collection & Trading Platform</p>
            </div>

            <div class="about-section" style="text-align: center;">
                <h2>The Project</h2>
                <p>Illusionary is a web-based extension of the <strong>Illusionary Discord Bot</strong>. This dashboard provides a premium experience for collectors to browse their vaults, manage their collections, and trade with other users in a secure, visual environment.</p>
                <p>All cards, mana, and progress are synced in real-time between the Discord bot and this platform.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 30px;">
                <div class="disclaimer-box">
                    <h3 style="font-size: 0.7rem; color: #ff4e4e; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Security</h3>
                    <p style="font-size: 0.75rem; line-height: 1.4; color: var(--text-muted); margin: 0;">Never share your <strong>ILLUSIONARY_SID</strong>. It provides full access to your collection.</p>
                </div>
                <div class="disclaimer-box">
                    <h3 style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Legal</h3>
                    <p style="font-size: 0.75rem; line-height: 1.4; color: var(--text-muted); margin: 0;">Independent project unaffiliated with Discord Inc. Assets belong to their creators.</p>
                </div>
                <div class="disclaimer-box">
                    <h3 style="font-size: 0.7rem; color: var(--accent-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Privacy</h3>
                    <p style="font-size: 0.75rem; line-height: 1.4; color: var(--text-muted); margin: 0;">Uses essential cookies (SID) and Cloudflare/Turnstile for security and DDoS protection.</p>
                </div>
            </div>

            <div class="about-section" style="text-align: center; margin-top: 50px;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                    <!-- Main Developer -->
                    <div style="display: inline-flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.05); padding: 8px 16px; border-radius: 50px; border: 1px solid var(--glass-border);">
                        <?php if (!empty($dev_data['avatar'])): ?>
                            <img src="<?php echo $dev_data['avatar']; ?>" style="width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--accent-primary);">
                        <?php endif; ?>
                        <span style="font-size: 0.85rem; color: #fff;">Developed by <strong><?php echo htmlspecialchars($dev_data['username'] ?? 'certified_pen'); ?></strong></span>
                    </div>

                    <!-- Collaborators -->
                    <?php if (!empty($collaborators)): ?>
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 10px; margin-top: 5px;">
                        <span style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">In collaboration with</span>
                        <div style="display: flex; gap: 10px;">
                            <?php foreach ($collaborators as $collab): ?>
                                <div style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.03); padding: 4px 10px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.05);">
                                    <?php if (!empty($collab['avatar'])): ?>
                                        <img src="<?php echo $collab['avatar']; ?>" style="width: 20px; height: 20px; border-radius: 50%;">
                                    <?php endif; ?>
                                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.7);"><?php echo htmlspecialchars($collab['username']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <p style="font-size: 0.7rem; opacity: 0.4; margin-top: 25px;">Version 2.9.0<span style="opacity: 0.3;">_M4SS</span> &copy; <?php echo date('Y'); ?> Illusionary. All rights reserved.</p>
            </div>
        </div>
    </main>

    <?php include 'null-egg.php'; ?>
</body>
</html>
