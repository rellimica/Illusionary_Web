<?php
/**
 * API INITIALIZATION & SECURITY GATEKEEPER
 * Centralized script to handle authentication, environment loading, 
 * and database connection for all API endpoints.
 */
define('IS_API', true);

// 0. GLOBAL ERROR HANDLING
set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: application/json');
    $errorMsg = ($e instanceof PDOException) ? 'A database error occurred. Operation aborted.' : 'A critical system error occurred. Please try again later.';
    echo json_encode([
        'success' => false,
        'error' => $errorMsg
    ]);
    exit;
});

// 1. ENVIRONMENT LOADING
$SYSTEM_ENV = '/root/Illusionary/.env';
$LOCAL_ENV  = __DIR__ . '/../.env';

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

$env = array_merge(loadEnv($SYSTEM_ENV), loadEnv($LOCAL_ENV));

// 2. SESSION CONFIGURATION
$session_lifetime = 30 * 24 * 60 * 60;
ini_set('session.gc_maxlifetime', $session_lifetime);
ini_set('session.cookie_lifetime', $session_lifetime);
session_name('ILLUSIONARY_SID');
session_start();

// 3. SECURITY GATE: Multi-Tier Scoping
$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$api_key = $headers['x-api-key'] ?? $_POST['api_key'] ?? null;

$is_authenticated = isset($_SESSION['user_authenticated']);
$request_scope = 'public'; // Default
$authorized = false;

if ($is_authenticated) {
    $authorized = true;
    $user_id = (string)$_SESSION['user_data']['id'];
    // Check if browser user is admin via config.php helper
    require_once __DIR__ . '/../config.php';
    $request_scope = isAdmin($user_id) ? 'admin' : 'write'; // Regular users can write to their own data
} elseif ($api_key) {
    // Check for Scoped API Keys in .env
    // Format: ADMIN_API_KEY, WRITE_API_KEY, READ_API_KEY
    if (isset($env['ADMIN_API_KEY']) && hash_equals($env['ADMIN_API_KEY'], $api_key)) {
        $request_scope = 'admin';
        $authorized = true;
    } elseif (isset($env['WRITE_API_KEY']) && hash_equals($env['WRITE_API_KEY'], $api_key)) {
        $request_scope = 'write';
        $authorized = true;
    } elseif (isset($env['READ_API_KEY']) && hash_equals($env['READ_API_KEY'], $api_key)) {
        $request_scope = 'read';
        $authorized = true;
    }
}

if (!$authorized) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Authentication Required (403 Forbidden).']);
    exit;
}

/**
 * checkScope() - Ensures the current request has the required permission level
 * Hierarchy: admin > write > read
 */
function checkScope($required) {
    global $request_scope;
    $levels = ['read' => 1, 'write' => 2, 'admin' => 3];
    $current_val = $levels[$request_scope] ?? 0;
    $required_val = $levels[$required] ?? 4; // unknown requirement fails

    if ($current_val < $required_val) {
        http_response_code(403);
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'error' => "Insufficent Permissions. Scope '$required' required, you have '$request_scope'."]));
    }
}

// 4. IDENTITY & TOKENS
$DISCORD_TOKEN = $env['DISCORD_TOKEN'] ?? '';
// If authenticated as a bot (non-session), use the BOT_DISCORD_ID from .env
if ($request_scope !== 'public' && !isset($_SESSION['user_authenticated'])) {
    $my_id = $env['BOT_DISCORD_ID'] ?? '0'; // '0' represents the System/Bot
} else {
    $my_id = (string)($_SESSION['user_data']['id'] ?? '0');
}

// 5. DATABASE CONNECTION
try {
    $dsn = "mysql:host=" . ($env['MYSQL_HOST'] ?? 'localhost') . ";dbname=" . ($env['MYSQL_DATABASE'] ?? '') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $env['MYSQL_USER'] ?? '', $env['MYSQL_PASSWORD'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => true
    ]);
} catch (Exception $e) { 
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database Connection Failed.']);
    exit;
}

// 6. GLOBAL CONFIG & UTILS
require_once __DIR__ . '/../config.php';

/**
 * guard() - Validates incoming data against a set of rules.
 */
function guard($source, $rules) {
    foreach ($rules as $field => $requirements) {
        $reqs = explode('|', $requirements);
        if (in_array('required', $reqs) && (!isset($source[$field]) || $source[$field] === '')) {
            errorResponse("Missing required field: $field", 400);
        }
        if (!isset($source[$field])) continue;
        foreach ($reqs as $r) {
            $val = $source[$field];
            if ($r === 'num' && !is_numeric($val)) errorResponse("Field $field must be numeric", 400);
            if ($r === 'id' && !preg_match('/^\d+$/', (string)$val)) errorResponse("Field $field must be a valid ID", 400);
            if ($r === 'json') {
                json_decode($val);
                if (json_last_error() !== JSON_ERROR_NONE) errorResponse("Field $field must be valid JSON", 400);
            }
        }
    }
}

function errorResponse($msg, $code = 403) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

/**
 * createNotification() - Injects a system alert for a specific user.
 */
function createNotification($pdo, $uid, $type, $title, $message, $link = null, $urgent = 0) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link, urgent) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$uid, $type, $title, $message, $link, $urgent]);
    } catch (Exception $e) {
        return false;
    }
}
