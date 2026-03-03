<?php
/**
 * TERMS OF SERVICE PAGE
 * Provides legal and usage information for the Illusionary project.
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
    // If mobile/tos.php doesn't exist yet, we'll just stay here for now or redirect if it does
    // header("Location: mobile/tos.php");
    // exit;
}

require_once 'config.php';

// AUTHENTICATION CHECK
if (!isset($_SESSION['user_authenticated'])) {
    header("Location: auth.php?redirect=tos.php");
    exit;
}

$my_id = $_SESSION['user_data']['id'] ?? null;
if (!$my_id) {
    die("Error: User ID not found in session.");
}

// DATABASE CONNECTION
$BOT_DIR = '/root/Illusionary';
$ENV_PATH = $BOT_DIR . '/.env';
$env = loadEnv($ENV_PATH);

try {
    $host = $env['MYSQL_HOST'] ?? 'localhost';
    $db   = $env['MYSQL_DATABASE'] ?? '';
    $user = $env['MYSQL_USER'] ?? '';
    $pass = $env['MYSQL_PASSWORD'] ?? '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// HANDLE ACCEPTANCE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_tos'])) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET tos_accepted = 1 WHERE discord_id = ?");
        $stmt->execute([$my_id]);
        $_SESSION['tos_accepted'] = 1; // Update session
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $db_error = "Failed to update Terms of Service status. Please ensure the database migration has been run.";
    }
}

// CHECK IF ALREADY ACCEPTED
$has_accepted = false;
try {
    $stmt = $pdo->prepare("SELECT tos_accepted FROM users WHERE discord_id = ?");
    $stmt->execute([$my_id]);
    $user_record = $stmt->fetch();
    $has_accepted = ($user_record && isset($user_record['tos_accepted']) && $user_record['tos_accepted'] == 1);
} catch (Exception $e) {
    // If column doesn't exist, we assume not accepted and handle it in the UI
    $column_missing = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary | Terms of Service</title>
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
        .tos-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .tos-content {
            width: 100%;
            max-width: 800px;
            background: rgba(12, 10, 21, 0.4);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            color: var(--text-main);
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .progress-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.05);
            z-index: 10;
        }
        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            box-shadow: 0 0 10px var(--accent-primary);
            transition: width 0.1s ease;
        }
        .tos-inner {
            padding: 40px;
        }
        .tos-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .tos-header img {
            width: 60px;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 20px var(--accent-primary));
        }
        .tos-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 800;
        }
        .tos-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .tos-section {
            margin-bottom: 30px;
        }
        .tos-section h2 {
            font-size: 1.1rem;
            color: var(--accent-primary);
            margin-bottom: 12px;
            font-family: 'Outfit', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tos-section h2::before {
            content: '';
            width: 4px;
            height: 18px;
            background: var(--accent-primary);
            border-radius: 10px;
        }
        .tos-section p {
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .tos-scroll {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 20px;
            margin-bottom: 30px;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-primary) rgba(255,255,255,0.05);
        }
        .tos-scroll::-webkit-scrollbar { width: 4px; }
        .tos-scroll::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .tos-scroll::-webkit-scrollbar-thumb { background: var(--accent-primary); border-radius: 10px; }
        
        .accept-box {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .btn-accept {
            background: var(--accent-primary);
            color: #fff;
            border: none;
            padding: 14px 50px;
            border-radius: 14px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 0 20px rgba(255, 0, 234, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-accept:hover:not(:disabled) {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 0 35px rgba(255, 0, 234, 0.6);
            filter: brightness(1.1);
        }
        .btn-accept:active:not(:disabled) {
            transform: scale(0.98);
        }
        .btn-accept:disabled {
            background: #2a2832;
            color: rgba(255,255,255,0.2);
            cursor: not-allowed;
            box-shadow: none;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* MOBILE OVERRIDES */
        @media (max-width: 768px) {
            .tos-inner { padding: 30px 20px; }
            .tos-header h1 { font-size: 1.8rem; }
            .tos-header img { width: 50px; }
            .tos-scroll { max-height: 55vh; padding-right: 10px; }
            .btn-accept { width: 100%; padding: 18px; }
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Terms of Service';
    if ($has_accepted) {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $is_mobile = preg_match('/iPhone|Android|webOS|BlackBerry|iPod/i', $ua);
        if ($is_mobile) {
            include 'mobile/nav.php';
        } else {
            include 'nav.php'; 
        }
    }
    ?>

    <main class="tos-container">
        <div class="tos-content">
            <div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>
            
            <div class="tos-inner">
                <div class="tos-header">
                    <img src="illusionary.png" alt="Illusionary Logo">
                    <h1 class="gradient-text">TERMS OF SERVICE</h1>
                    <p>Terms of Service & Engagement Guidelines</p>
                </div>

                <div class="tos-scroll" id="tosScroll">
                    <div class="tos-section">
                        <h2>1. Acceptance of Terms</h2>
                        <p>By accessing and using the Illusionary Dashboard, you agree to be bound by these Terms of Service. This platform is a digital companion for the Illusionary Discord Bot and requires a valid Discord account for authentication.</p>
                    </div>

                    <div class="tos-section">
                        <h2>2. Account Security</h2>
                        <p>Your session is managed via your <strong>ILLUSIONARY_SID</strong>. This identifier is sensitive. Sharing your session ID, cookies, or any authentication tokens with third parties is strictly prohibited. Illusionary is not responsible for losses resulting from shared credentials.</p>
                    </div>

                    <div class="tos-section">
                        <h2>3. Prohibited Activity</h2>
                        <p>Users are prohibited from attempting to bypass system limitations, engage in automated "botting" of the dashboard features, or perform any malicious actions that degrade service quality.</p>
                        <p><strong>Exploiting any system bugs, logical errors, or unintended vulnerabilities for personal gain, card duplication, or collection manipulation is strictly prohibited.</strong> Any such activity, or the failure to report a known critical bug while actively benefiting from it, will result in an immediate and permanent ban from the project.</p>
                    </div>

                    <div class="tos-section">
                        <h2>4. Digital Assets</h2>
                        <p>All cards, mana, and digital items are virtual assets tied to your Illusionary profile. These items have no real-world monetary value and cannot be exchanged for currency. Some assets used on this platform are the property of their original owners; the maintainer of this website assumes no responsibility for their design or content.</p>
                        <p>Illusionary reserves the right to adjust card balances, rarities, or availability for balancing purposes.</p>
                    </div>

                    <div class="tos-section">
                        <h2>5. Privacy</h2>
                        <p>We use essential cookies to maintain your session and security. We do not sell your data. We use Cloudflare and Turnstile for DDoS protection and security verification.</p>
                    </div>

                    <div class="tos-section">
                        <h2>6. Limitation of Liability</h2>
                        <p>Illusionary is provided "as is" without warranties. We are not liable for any service interruptions, loss of virtual assets, or data inconsistencies that may occur during the ongoing development of the platform.</p>
                    </div>
                </div>

                <div class="accept-box">
                    <?php if (!$has_accepted): ?>
                        <form method="POST">
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 25px; letter-spacing: 0.5px;">Scroll to the absolute bottom to authorize access.</p>
                            <button type="submit" name="accept_tos" id="acceptBtn" class="btn-accept" disabled>AGREE & ENTER</button>
                        </form>
                    <?php else: ?>
                        <p style="color: var(--accent-secondary); font-weight: 700; font-size: 0.9rem;">You have agreed to the terms of service.</p>
                        <a href="index.php" class="btn-accept" style="text-decoration: none; display: inline-block; margin-top: 15px;">RETURN TO DASHBOARD</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        const tosScroll = document.getElementById('tosScroll');
        const acceptBtn = document.getElementById('acceptBtn');
        const progressBar = document.getElementById('progressBar');
        
        if (tosScroll && progressBar) {
            tosScroll.addEventListener('scroll', () => {
                const scrollPos = tosScroll.scrollTop;
                const totalHeight = tosScroll.scrollHeight - tosScroll.clientHeight;
                const progress = (scrollPos / totalHeight) * 100;
                
                progressBar.style.width = progress + '%';
                
                if (acceptBtn && progress >= 99) {
                    acceptBtn.disabled = false;
                }
            });
        }
    </script>
    
    <?php include 'null-egg.php'; ?>
</body>
</html>
