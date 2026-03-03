<?php
/**
 * MOBILE AUTH PAGE
 * Mobile-optimized login with Discord OAuth2.
 * Same OAuth logic as desktop, simplified visuals.
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

if (isset($_GET['logout'])) {
    session_destroy();
    $reason = isset($_GET['kick']) ? 'kicked=1' : 'logged_out=1';
    header("Location: /auth.php?$reason");
    exit;
}

require_once __DIR__ . '/../config.php';

$BOT_DIR = '/root/Illusionary';
$SYSTEM_ENV = $BOT_DIR . '/.env';
$LOCAL_ENV  = __DIR__ . '/../.env';

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

// If already authenticated, redirect to mobile index
if (isset($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] === true) {
    $target = $_SESSION['redirect_url'] ?? '/index.php';
    unset($_SESSION['redirect_url']);
    header("Location: " . $target);
    exit;
}

// Capture Redirect URL
if (isset($_GET['redirect'])) {
    $requested = $_GET['redirect'];
    if (strpos($requested, 'http') !== 0 && strpos($requested, '//') !== 0) {
        $_SESSION['redirect_url'] = $requested;
    }
}

// Handle OAuth Callback
if (isset($_GET['code'])) {
    $state = $_GET['state'] ?? '';
    $saved_state = $_SESSION['oauth2_state'] ?? '';

    if (empty($state) || $state !== $saved_state) {
        header("Location: /auth.php?reauth=1");
        exit;
    }
    
    unset($_SESSION['oauth2_state']);

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
            
            $target = $_SESSION['redirect_url'] ?? '/index.php';
            unset($_SESSION['redirect_url']);
            header("Location: " . $target);
            exit;
        }
    }
    
    $debug_error = "";
    if (isset($response['error_description'])) {
        $debug_error = " (Discord: " . $response['error_description'] . ")";
    } elseif (isset($response['error'])) {
        $debug_error = " (Error: " . $response['error'] . ")";
    }
    
    header("Location: /auth.php?login_failed=1&msg=" . urlencode($debug_error));
    exit;
}

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

// Prepare OAuth URL
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | Login</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="shortcut icon" href="/favicon/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/mobile/mobile.css">
    <link rel="stylesheet" href="/variations.css">
    <link rel="preload" href="/illusionary.png" as="image">
    <?php 
    require_once __DIR__ . '/../theme-config.php';
    injectTheme($THEME);
    ?>
</head>
<body class="m-login-container">
    <div class="m-login-glow"></div>
    
    <div class="m-login-box">
        <img src="/illusionary.png" alt="Illusionary Logo" class="m-login-logo" loading="lazy">
        <h1 class="gradient-text m-login-title">ILLUSIONARY</h1>
        <p class="m-login-subtitle">Authorize your Discord identity to access the Illusionary Dashboard.</p>
        
        <?php if (isset($error)): ?>
            <div class="m-error-box">
                <div class="m-error-title">Uh Oh!</div>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($info_message)): 
            $style = ($info_type ?? 'notice') === 'success' 
                ? 'background: rgba(0, 255, 157, 0.1); border: 1px solid rgba(0, 255, 157, 0.2); color: #00ff9d;' 
                : 'background: rgba(255, 0, 234, 0.1); border: 1px solid rgba(255, 0, 234, 0.2); color: #ffb1f9;';
        ?>
            <div class="m-info-box" style="<?php echo $style; ?>">
                <div class="m-info-title"><?php echo $info_title ?? 'Notice'; ?></div>
                <?php echo htmlspecialchars($info_message); ?>
            </div>
        <?php endif; ?>
        
        <a href="<?php echo $login_url; ?>" style="text-decoration: none;">
            <button class="m-discord-btn">
                <svg viewBox="0 0 24 24">
                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.419c0 1.334-.947 2.419-2.157 2.419zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.419c0 1.334-.946 2.419-2.157 2.419z"/>
                </svg>
                Login with Discord
            </button>
        </a>
    </div>

    <script>
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
