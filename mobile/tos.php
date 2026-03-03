<?php
/**
 * MOBILE TERMS OF SERVICE PAGE
 * Restored separate mobile version with full wording.
 */
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

require_once __DIR__ . '/../config.php';

// AUTHENTICATION CHECK
if (!isset($_SESSION['user_authenticated'])) {
    header("Location: /auth.php?redirect=/mobile/tos.php");
    exit;
}

$my_id = $_SESSION['user_data']['id'];

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
    die("Database Connection Failed.");
}

// HANDLE ACCEPTANCE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_tos'])) {
    $stmt = $pdo->prepare("UPDATE users SET tos_accepted = 1 WHERE discord_id = ?");
    $stmt->execute([$my_id]);
    $_SESSION['tos_accepted'] = 1;
    header("Location: /mobile/index.php");
    exit;
}

// CHECK IF ALREADY ACCEPTED
$stmt = $pdo->prepare("SELECT tos_accepted FROM users WHERE discord_id = ?");
$stmt->execute([$my_id]);
$user_record = $stmt->fetch();
$has_accepted = ($user_record && $user_record['tos_accepted'] == 1);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Illusionary | Terms</title>
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
    <style>
        .m-tos-content {
            padding: 20px;
            background: rgba(12, 10, 21, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
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
            background: var(--accent-primary);
            box-shadow: 0 0 10px var(--accent-primary);
        }
        .m-tos-scroll {
            max-height: 55vh;
            overflow-y: auto;
            margin-bottom: 20px;
            padding-right: 10px;
            font-size: 0.85rem;
            line-height: 1.5;
            color: var(--text-muted);
        }
        .m-tos-section h2 {
            font-size: 0.9rem;
            color: var(--accent-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 20px 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .m-tos-section h2::before {
            content: '';
            width: 3px;
            height: 14px;
            background: var(--accent-primary);
            border-radius: 10px;
        }
        .m-accept-box {
            background: rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }
        .m-btn-accept {
            width: 100%;
            padding: 15px;
            background: var(--accent-primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit';
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 20px rgba(255, 0, 234, 0.2);
        }
        .m-btn-accept:disabled {
            background: #333;
            box-shadow: none;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Terms of Service';
    if ($has_accepted) {
        include 'nav.php'; 
    }
    ?>

    <main class="m-container" style="<?php echo !$has_accepted ? 'padding-top: 40px;' : ''; ?>">
        <div class="m-tos-content">
            <div class="progress-container"><div class="progress-bar" id="progressBar"></div></div>
            
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="/illusionary.png" width="40" style="filter: drop-shadow(0 0 10px var(--accent-primary));">
                <h1 class="gradient-text" style="font-size: 1.5rem; margin-top: 10px;">TERMS OF SERVICE</h1>
            </div>

            <div class="m-tos-scroll" id="tosScroll">
                <div class="m-tos-section">
                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing and using the Illusionary Dashboard, you agree to be bound by these Terms of Service. This platform is a digital companion for the Illusionary Discord Bot and requires a valid Discord account for authentication.</p>
                </div>
                <div class="m-tos-section">
                    <h2>2. Account Security</h2>
                    <p>Your session is managed via your <strong>ILLUSIONARY_SID</strong>. This identifier is sensitive. Sharing your session ID, cookies, or any authentication tokens with third parties is strictly prohibited. Illusionary is not responsible for losses resulting from shared credentials.</p>
                </div>
                <div class="m-tos-section">
                    <h2>3. Prohibited Activity</h2>
                    <p>Users are prohibited from attempting to bypass system limitations, engage in automated "botting" of the dashboard features, or perform any malicious actions that degrade service quality.</p>
                    <p><strong>Exploiting any system bugs, logical errors, or unintended vulnerabilities for personal gain, card duplication, or collection manipulation is strictly prohibited.</strong> Any such activity, or the failure to report a known critical bug while actively benefiting from it, will result in an immediate and permanent ban from the project.</p>
                </div>
                <div class="m-tos-section">
                    <h2>4. Digital Assets</h2>
                    <p>All cards, mana, and digital items are virtual assets tied to your Illusionary profile. These items have no real-world monetary value and cannot be exchanged for currency. Some assets used on this platform are the property of their original owners; the maintainer of this website assumes no responsibility for their design or content.</p>
                    <p>Illusionary reserves the right to adjust card balances, rarities, or availability for balancing purposes.</p>
                </div>
                <div class="m-tos-section">
                    <h2>5. Privacy</h2>
                    <p>We use essential cookies to maintain your session and security. We do not sell your data. We use Cloudflare and Turnstile for DDoS protection and security verification.</p>
                </div>
                <div class="m-tos-section">
                    <h2>6. Limitation of Liability</h2>
                    <p>Illusionary is provided "as is" without warranties. We are not liable for any service interruptions, loss of virtual assets, or data inconsistencies that may occur during the ongoing development of the platform.</p>
                </div>
            </div>

            <div class="m-accept-box">
                <?php if (!$has_accepted): ?>
                    <form method="POST">
                        <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 15px;">Scroll to the bottom to accept.</p>
                        <button type="submit" name="accept_tos" id="acceptBtn" class="m-btn-accept" disabled>ACCEPT & CONTINUE</button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--accent-secondary); font-size: 0.8rem; font-weight: 700;">Terms Accepted.</p>
                    <a href="/mobile/index.php" class="m-btn-accept" style="text-decoration: none; display: block; margin-top: 10px; text-align: center; line-height: 1.2;">GO TO DASHBOARD</a>
                <?php endif; ?>
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
</body>
</html>
