<?php
/**
 * AUTHENTICATION PAGE
 * Handles Discord OAuth2 flow and displays the login UI.
 * 
 * SECURE SYSTEM WARNING: 
 * NEVER share your session ID or cookies (like ILLUSIONARY_SID) with anyone. 
 * Sharing this ID is like giving someone your house keys; they can access 
 * your account and steal your cards.
 */
// Set secure session parameters for a 10-day lifetime
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

// Mobile Detection — redirect to mobile auth unless processing OAuth callback or logout
if (!isset($_GET['code']) && !isset($_GET['logout'])) {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua)) {
        $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        header("Location: mobile/auth.php" . $qs);
        exit;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    // Check if we were kicked by Null or logged out manually
    $reason = isset($_GET['kick']) ? 'kicked=1' : 'logged_out=1';
    header("Location: /auth.php?$reason");
    exit;
}

require_once __DIR__ . '/config.php';

$BOT_DIR = '/root/Illusionary';
$SYSTEM_ENV = $BOT_DIR . '/.env';
$LOCAL_ENV  = __DIR__ . '/.env';

// Load system env first, then local env to allow overrides
$env = array_merge(
    loadEnv($SYSTEM_ENV),
    loadEnv($LOCAL_ENV)
);

$DISCORD_CLIENT_ID     = $env['DISCORD_CLIENT_ID'] ?? '';
$DISCORD_CLIENT_SECRET = $env['DISCORD_CLIENT_SECRET'] ?? '';
$GUILD_ID              = $env['GUILD_ID'] ?? '';
$DISCORD_TOKEN         = $env['DISCORD_TOKEN'] ?? '';
$DISCORD_REDIRECT_URI  = 'https://illusionary.bigwyvern.com/callback/';

// If already authenticated, go to destination or index
if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $mobile_default = preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua) ? '/mobile/index.php' : '/index.php';
    $target = $_SESSION['redirect_url'] ?? $mobile_default;
    unset($_SESSION['redirect_url']);
    header("Location: " . $target);
    exit;
}

// 0. Capture Redirect URL
if (isset($_GET['redirect'])) {
    $requested = $_GET['redirect'];
    // Allow relative paths (starting with /) or local .php files with query strings
    // Prevents off-site redirects by ensuring it doesn't start with http/https or //
    if (strpos($requested, 'http') !== 0 && strpos($requested, '//') !== 0) {
        $_SESSION['redirect_url'] = $requested;
    }
}

// 1. Handle OAuth Callback
if (isset($_GET['code'])) {
    $state = $_GET['state'] ?? '';
    $saved_state = $_SESSION['oauth2_state'] ?? '';

    // AUTH RECOVERY LOGIC:
    // If the state is missing or doesn't match, it's usually because the 
    // session expired while the user was on the Discord page. 
    // Instead of showing a technical error, we send them back to /auth.php 
    // so they can try again with a fresh session.
    if (empty($state) || $state !== $saved_state) {
        header("Location: /auth.php?reauth=1");
        exit;
    }
    
    unset($_SESSION['oauth2_state']); // One-time use

    $code = $_GET['code'];
    $token_url = 'https://discord.com/api/oauth2/token';
    
    $data = [
        'client_id' => $DISCORD_CLIENT_ID,
        'client_secret' => $DISCORD_CLIENT_SECRET,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $DISCORD_REDIRECT_URI,
        'scope' => 'identify'
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . 
                         "User-Agent: IllusionaryDashboard (1.0)\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'ignore_errors' => true
        ]
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($token_url, false, $context);
    $response = json_decode($result, true);

    if (isset($response['access_token'])) {
        $user_url = 'https://discord.com/api/users/@me';
        $options = [
            'http' => [
                'header' => "Authorization: Bearer " . $response['access_token'] . "\r\n" .
                            "User-Agent: IllusionaryDashboard (1.0)\r\n",
                'method' => 'GET',
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $user_result = file_get_contents($user_url, false, $context);
        $user = json_decode($user_result, true);

        if (isset($user['id'])) {
            /**
             * GUILD MEMBERSHIP CHECK
             * We verify the user is physically present in the target Discord server
             * using the Bot's privileged access.
             */
            if (!empty($GUILD_ID) && !empty($DISCORD_TOKEN)) {
                $member_url = "https://discord.com/api/v10/guilds/$GUILD_ID/members/" . $user['id'];
                $m_options = [
                    'http' => [
                        'header' => "Authorization: Bot $DISCORD_TOKEN\r\nUser-Agent: IllusionaryDashboard (1.0)\r\n",
                        'method' => 'GET',
                        'ignore_errors' => true
                    ]
                ];
                $m_context = stream_context_create($m_options);
                $m_result = file_get_contents($member_url, false, $m_context);
                $m_data = json_decode($m_result, true);

                if (!isset($m_data['user'])) {
                    header("Location: /server.php");
                    exit;
                }
            }

            $_SESSION['user_authenticated'] = true;
            $_SESSION['user_data'] = $user;
            
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $mobile_default = preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua) ? '/index.php' : '/index.php';
            $target = $_SESSION['redirect_url'] ?? $mobile_default;
            unset($_SESSION['redirect_url']);
            header("Location: " . $target);
            exit;
        }
    }
    
    // ERROR DIAGNOSTICS
    $debug_error = "";
    if (isset($response['error_description'])) {
        $debug_error = " (Discord: " . $response['error_description'] . ")";
    } elseif (isset($response['error'])) {
        $debug_error = " (Error: " . $response['error'] . ")";
    }
    
    header("Location: /auth.php?login_failed=1&msg=" . urlencode($debug_error));
    exit;
}

// Clear errors if the user lands here again
if (isset($_GET['login_failed'])) {
    $error = "Authentication failed. Please try again." . ($_GET['msg'] ?? "");
}

if (isset($_GET['reauth'])) {
    $info_message = "Your security session has expired. Please sign in again to continue.";
    $info_title = "Security Notice";
    $info_type = "notice";
}

if (isset($_GET['logged_out'])) {
    $info_message = "You have successfully logged out, thanks for using the Illusionary Dashboard.";
    $info_title = "Success";
    $info_type = "success";
}

if (isset($_GET['kicked'])) {
    $error = "CONNECTION TERMINATED BY SUBJECT NULL. DO NOT PERSIST.";
}



// 2. Prepare Login UI Assets
$banner_images = [];
$img_dir = '/images/images/';
$img_physical_dir = __DIR__ . '/images/images/';
if (is_dir($img_physical_dir)) {
    $files = scandir($img_physical_dir);
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

// Generation of a random 'state' for CSRF protection
if (empty($_SESSION['oauth2_state'])) {
    $_SESSION['oauth2_state'] = bin2hex(random_bytes(16));
}

$params = [
    'client_id' => $DISCORD_CLIENT_ID, 
    'redirect_uri' => $DISCORD_REDIRECT_URI, 
    'response_type' => 'code', 
    'scope' => 'identify',
    'state' => $_SESSION['oauth2_state'],
    'prompt' => 'none'
];



$login_url = 'https://discord.com/oauth2/authorize?' . http_build_query($params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Login</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/variations.css">
    <?php 
    require_once __DIR__ . '/theme-config.php';
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

        /* Banner Background Logic */
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
            opacity: 0.15;
            filter: grayscale(0.5) blur(1px);
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
            opacity: 0.8;
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
            border: 1px solid var(--glass-border); 
            text-align: center; 
            backdrop-filter: blur(40px); 
            -webkit-backdrop-filter: blur(40px);
            width: 100%; 
            max-width: 440px; 
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.9);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: auto;
        }

        .login-box:hover {
            transform: translateY(-8px) scale(1.01);
            border-color: rgba(255, 0, 234, 0.3);
            box-shadow: 0 50px 120px -30px rgba(255, 0, 234, 0.15);
        }

        .login-logo {
            width: 140px;
            margin-bottom: 2.5rem;
            filter: drop-shadow(0 0 30px var(--accent-primary));
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(2deg); }
        }

        .login-title {
            font-size: 2.8rem;
            margin-bottom: 0.5rem;
            letter-spacing: 4px;
        }

        .login-subtitle {
            color: var(--text-muted);
            margin-bottom: 3.5rem;
            font-size: 0.95rem;
            letter-spacing: 1px;
            font-weight: 400;
        }

        .discord-btn { 
            background: linear-gradient(135deg, #5865F2, #4752C4); 
            border: none; 
            padding: 18px 32px; 
            border-radius: 14px; 
            color: white; 
            font-weight: 700; 
            cursor: pointer; 
            width: 100%; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(88, 101, 242, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .discord-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(88, 101, 242, 0.4);
            filter: brightness(1.1);
        }

        .discord-btn:active {
            transform: translateY(0);
        }

        .bg-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255, 0, 234, 0.1) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 3;
            pointer-events: none;
        }

        .discord-icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
            flex-shrink: 0;
            display: block;
        }

        @media (max-width: 500px) {
            .login-box { padding: 3rem 2rem; border-radius: 0; border: none; height: 100vh; display: flex; flex-direction: column; justify-content: center; background: #05040a; }
            .banner-wrap { opacity: 0.1; }
        }
    </style>
</head>
<body class="login-container">
    <?php include __DIR__ . '/mobile-block.php'; ?>
    
    <div class="banner-wrap">
        <?php for($r=0; $r<7; $r++): ?>
            <div class="banner-row <?php echo $r % 2 == 0 ? 'scroll-left' : 'scroll-right'; ?>">
                <?php 
                $row_set = [];
                $cards_per_row = 20;
                for($i=0; $i<$cards_per_row; $i++) {
                    $img_index = ($i + ($r * $cards_per_row)) % (count($banner_images) ?: 1);
                    $row_set[] = !empty($banner_images) ? $banner_images[$img_index] : '/images/images/back.png';
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
        <?php if (isset($ENABLE_REVEAL_COUNTDOWN) && $ENABLE_REVEAL_COUNTDOWN && !empty($REVEAL_EMERGENCY_MSG)): ?>
            <!-- Hidden by default, VoxTakeover handles the aesthetic center screen -->
            <div id="countdownCenterMsg" style="display: none; text-align: center; z-index: 100; pointer-events: none;">
                <h2 class="glitch-text" style="font-size: 3.5rem; letter-spacing: 20px; text-shadow: 0 0 40px var(--accent-primary); color: #fff; margin-bottom: 25px; text-transform: uppercase;" data-text="<?php echo htmlspecialchars($REVEAL_EMERGENCY_MSG); ?>">
                    <?php echo htmlspecialchars($REVEAL_EMERGENCY_MSG); ?>
                </h2>
                <div style="width: 150px; height: 2px; background: var(--accent-primary); margin: 0 auto; box-shadow: 0 0 20px var(--accent-primary); opacity: 0.6;"></div>
            </div>
        <?php endif; ?>

        <div class="login-box" id="mainLoginBox">
            <img src="/illusionary.png" alt="Illusionary Logo" class="login-logo">
            <h1 class="gradient-text login-title">ILLUSIONARY</h1>
            <p class="login-subtitle">Authorize your Discord identity to access the Illusionary Dashboard.</p>
            
            <?php 
            $raw_redirect = $_SESSION['redirect_url'] ?? '';
            $is_not_home = ($raw_redirect !== '/' && $raw_redirect !== '/index.php' && $raw_redirect !== 'index.php');
            
            if (isset($_GET['redirect']) && $is_not_home): ?>
                <?php 
                // Strip query parameters for the display label (e.g. /trade.php?id=1 -> Trade)
                $clean_path = explode('?', $raw_redirect)[0];
                $display_target = ucfirst(str_replace(['/', '.php'], '', $clean_path)); 
                ?>
                <p style="margin-bottom: 2.5rem; font-size: 0.75rem; color: var(--text-muted); font-style: italic; opacity: 0.8;">
                    You will be automatically redirected to <span style="color: var(--accent-secondary); font-weight: 700; font-style: normal;"><?php echo htmlspecialchars($display_target); ?></span> after authentication.
                </p>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div style="background: rgba(255, 78, 78, 0.1); border: 1px solid rgba(255, 78, 78, 0.2); padding: 15px; border-radius: 12px; margin-bottom: 2rem; color: #ff4e4e; font-size: 0.85rem; line-height: 1.4; text-align: left;">
                    <div style="font-weight: 800; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 1px;">Uh Oh!</div>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($info_message)): 
                $style = ($info_type ?? 'notice') === 'success' 
                    ? 'background: rgba(0, 255, 157, 0.1); border: 1px solid rgba(0, 255, 157, 0.2); color: #00ff9d;' 
                    : 'background: rgba(255, 0, 234, 0.1); border: 1px solid rgba(255, 0, 234, 0.2); color: #ffb1f9;';
            ?>
                <div style="<?php echo $style; ?> padding: 15px; border-radius: 12px; margin-bottom: 2rem; font-size: 0.85rem; line-height: 1.4; text-align: left;">
                    <div style="font-weight: 800; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 1px;"><?php echo $info_title ?? 'Notice'; ?></div>
                    <?php echo htmlspecialchars($info_message); ?>
                </div>
            <?php endif; ?>
            
            <a href="<?php echo $login_url; ?>" style="text-decoration: none;">
                <button class="discord-btn">
                    <svg class="discord-icon" viewBox="0 0 24 24">
                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.419c0 1.334-.947 2.419-2.157 2.419zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.419c0 1.334-.946 2.419-2.157 2.419z"/>
                    </svg>
                    Login with Discord
                </button>
            </a>
        </div>
    </div>
    <script>
        // CLEANUP: If there are status parameters in the URL, clear them after 5 seconds
        // so the URL stays clean and the messages don't persist on manual refresh.
        const params = new URLSearchParams(window.location.search);
        if (params.has('logged_out') || params.has('kicked')) {
            setTimeout(() => {
                window.location.href = 'auth.php';
            }, 5000);
        }
    </script>
</body>
</html>
<?php exit; ?>
