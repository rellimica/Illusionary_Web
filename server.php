<?php


/**
 * SERVER MEMBERSHIP REQUIREMENT PAGE
 * Displayed when a user authenticated via Discord but is not in the required guild.
 */
session_name('ILLUSIONARY_SID');
session_start();

$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';

function loadEnv($path) {
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || !strpos($line, '=')) continue;
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
    }
    return $env;
}

$env = loadEnv($ENV_PATH);
$GUILD_ID      = $env['GUILD_ID'] ?? '';
$DISCORD_TOKEN = $env['DISCORD_TOKEN'] ?? '';
$INVITE_LINK   = $env['DISCORD_INVITE'] ?? '#'; // Add DISCORD_INVITE to .env for a working link

$guild_name = "the Required Server";

// Optional: Fetch Guild Name to make it more personal
if (!empty($GUILD_ID) && !empty($DISCORD_TOKEN)) {
    $url = "https://discord.com/api/v10/guilds/$GUILD_ID";
    $options = [
        'http' => [
            'header' => "Authorization: Bot $DISCORD_TOKEN\r\n",
            'method' => 'GET',
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    if ($result) {
        $data = json_decode($result, true);
        if (isset($data['name'])) {
            $guild_name = $data['name'];
        }
    }
}

// Prepare Login UI Assets (reusing logic from auth.php)
$banner_images = [];
$img_dir = 'images/images/';
if (is_dir($img_dir)) {
    $files = scandir($img_dir);
    $exclude = ['back.png', 'null.png', 'background.png', 'red_mana.png'];
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg'])) {
            if (!in_array(strtolower($file), $exclude)) { 
                $banner_images[] = $img_dir . $file;
            }
        }
    }
    shuffle($banner_images);
    $banner_images = array_slice($banner_images, 0, 40);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Access Denied</title>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="shortcut icon" href="favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <?php 
    require_once 'theme-config.php';
    injectTheme($THEME);
    ?>
    <style>
        .login-container { 
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100dvh;
            display: grid;
            place-items: center;
            background: var(--bg-color); 
            overflow: hidden;
            z-index: 0;
        }

        .banner-wrap {
            position: absolute;
            top: -20%;
            left: -20%;
            width: 140%;
            height: 140%;
            transform: rotate(-15deg);
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            pointer-events: none;
            opacity: 0.1;
            filter: grayscale(1) blur(2px);
        }

        .banner-row {
            display: flex;
            width: fit-content;
            white-space: nowrap;
            will-change: transform;
            transform: translateZ(0);
        }

        .banner-row.scroll-left { animation: scroll-left 120s linear infinite; }
        .banner-row.scroll-right { animation: scroll-right 100s linear infinite; }

        @keyframes scroll-left { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        @keyframes scroll-right { 0% { transform: translateX(-50%); } 100% { transform: translateX(0); } }

        .banner-card {
            width: 180px;
            aspect-ratio: 2/3;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            flex-shrink: 0;
            margin-right: 20px;
        }

        .banner-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
            backface-visibility: hidden;
        }

        .bg-overlay {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, transparent 20%, var(--bg-color) 100%);
            z-index: 2;
        }

        .login-content-layer {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .login-box { 
            background: var(--login-box-bg); 
            padding: 4rem 3rem; 
            border-radius: 32px; 
            border: 1px solid rgba(255, 78, 78, 0.3); 
            text-align: center; 
            backdrop-filter: blur(40px); 
            -webkit-backdrop-filter: blur(40px);
            width: 100%; 
            max-width: 440px; 
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.9);
            pointer-events: auto;
        }

        .login-logo {
            width: 120px;
            margin-bottom: 2rem;
            filter: grayscale(1) drop-shadow(0 0 20px rgba(255, 78, 78, 0.3));
        }

        .login-title {
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            letter-spacing: 2px;
            color: #ff4e4e;
        }

        .login-subtitle {
            color: var(--text-muted);
            margin-bottom: 3rem;
            font-size: 1rem;
            line-height: 1.6;
            letter-spacing: 0.5px;
        }

        .server-highlight {
            color: #fff;
            font-weight: 800;
            display: block;
            margin-top: 10px;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .action-btn { 
            background: linear-gradient(135deg, #ff4e4e, #c0392b); 
            border: none; 
            padding: 16px 30px; 
            border-radius: 14px; 
            color: white; 
            font-weight: 700; 
            cursor: pointer; 
            width: 100%; 
            transition: all 0.3s; 
            font-size: 1rem;
            font-family: 'Outfit', sans-serif;
            margin-bottom: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 78, 78, 0.3);
        }

        .secondary-btn {
            background: transparent;
            border: 1px solid var(--glass-border);
            padding: 12px 20px;
            border-radius: 12px;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .secondary-btn:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        .bg-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255, 78, 78, 0.05) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
        }

        @media (max-width: 500px) {
            .login-box { padding: 3rem 2rem; border-radius: 0; border: none; height: 100vh; display: flex; flex-direction: column; justify-content: center; background: #05040a; }
        }
    </style>
</head>
<body class="login-container">
    <?php include 'mobile-block.php'; ?>
    <div class="banner-wrap">
        <?php for($r=0; $r<7; $r++): ?>
            <div class="banner-row <?php echo $r % 2 == 0 ? 'scroll-left' : 'scroll-right'; ?>">
                <?php 
                $row_set = [];
                $cards_per_row = 20; 
                for($i=0; $i<$cards_per_row; $i++) {
                    $img_index = ($i + ($r * $cards_per_row)) % (count($banner_images) ?: 1);
                    $row_set[] = !empty($banner_images) ? $banner_images[$img_index] : 'images/images/back.png';
                }
                $loop_set = array_merge($row_set, $row_set);
                foreach($loop_set as $img): 
                ?>
                    <div class="banner-card">
                        <img src="<?php echo $img; ?>" alt="Card Preview">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
    
    <div class="bg-overlay"></div>
    <div class="bg-glow"></div>
    
    <div class="login-content-layer">
        <div class="login-box">
            <img src="illusionary.png" alt="Illusionary Logo" class="login-logo">
            <h1 class="login-title">ACCESS DENIED</h1>
            <p class="login-subtitle">
                To explore your collection and trade cards, you must be a member of:
                <span class="server-highlight"><?php echo htmlspecialchars($guild_name); ?></span>
            </p>
            <br>
            <a href="auth.php" class="secondary-btn">Try Another Account</a>
        </div>
    </div>
    <?php include 'null-egg.php'; ?>
</body>
</html>
