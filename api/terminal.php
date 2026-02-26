<?php
// api/terminal.php - The "Morphic Kernel" Backend
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$user = strtoupper($_POST['user'] ?? '');
$pass = strtoupper($_POST['pass'] ?? '');

// Helper to load system configuration
$configPath = __DIR__ . '/CMD-LORE/_SYSTEM/config.json';
$systemConfig = ['users' => [], 'subjects' => [], 'events' => []];
if (file_exists($configPath)) {
    $systemConfig = json_decode(file_get_contents($configPath), true);
}
$userData = $systemConfig['users'] ?? [];
$subjects = $systemConfig['subjects'] ?? [];
$loreEvents = $systemConfig['events'] ?? [];
$systemTasks = $systemConfig['tasks'] ?? [];
$commandPerms = $systemConfig['commandPermissions'] ?? [];

// Helper to scan CMD-LORE and load documents
$documents = [];
$baseLoreDir = __DIR__ . '/CMD-LORE';

if (is_dir($baseLoreDir)) {
    $categories = ['CORE', 'SUBJECTS', 'RESEARCH'];
    foreach ($categories as $cat) {
        $catPath = $baseLoreDir . DIRECTORY_SEPARATOR . $cat;
        if (is_dir($catPath)) {
            $files = scandir($catPath);
            foreach ($files as $file) {
                if (str_ends_with($file, '.json')) {
                    $jsonContent = file_get_contents($catPath . DIRECTORY_SEPARATOR . $file);
                    $decoded = json_decode($jsonContent, true);
                    if ($decoded) {
                        $originalName = pathinfo($file, PATHINFO_FILENAME);
                        $documents[$originalName] = $decoded;
                    }
                }
            }
        }
    }
}

/**
 * Renders a boxed document for the terminal
 */
function renderDocument($title, $content, $style = 'STANDARD') {
    $width = 60;
    $lines = [];
    
    // Select Border Style
    $chars = ['tl' => '┌', 'h' => '─', 'v' => '│', 'j' => '├', 'end' => 'END_OF_FILE'];
    
    switch ($style) {
        case 'PERSONAL':
            $chars = ['tl' => '╔', 'h' => '═', 'v' => '║', 'j' => '╠', 'end' => 'PERSONAL_LOG_FIN'];
            break;
        case 'TECHNICAL':
            $chars = ['tl' => '█', 'h' => '▀', 'v' => '█', 'j' => '█', 'end' => 'DATA_INTEGRITY_SAFE'];
            break;
        case 'WARNING':
            $chars = ['tl' => '!', 'h' => '=', 'v' => '!', 'j' => '!', 'end' => 'CLEARANCE_REQUIRED'];
            break;
        case 'SYSTEM':
            $chars = ['tl' => '▓', 'h' => '▓', 'v' => '▓', 'j' => '▓', 'end' => 'SYS_BUFFER_CLEAN'];
            break;
    }

    // Top Header
    $lines[] = $chars['tl'] . str_repeat($chars['h'], 4) . " [ " . strtoupper($title) . " ] " . str_repeat($chars['h'], 10);
    
    // Content Padding
    $lines[] = $chars['v'];
    
    // Content wrapping
    $wrapped = wordwrap($content, $width, "\n", true);
    $rawLines = explode("\n", $wrapped);
    foreach ($rawLines as $line) {
        $lines[] = $chars['v'] . "  " . trim($line);
    }
    
    // Final Padding
    $lines[] = $chars['v'];
    $lines[] = $chars['v'] . " [ " . ($chars['end'] ?? 'END_OF_FILE') . " ]";
    
    return $lines;
}

// Helper to send JSON with specific status
function sendResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if ($action === 'login') {
    if (isset($userData[$user]) && $userData[$user]['pass'] === $pass) {
        if (!$userData[$user]['enabled']) {
            sendResponse(['success' => false, 'msg' => 'CRITICAL_ERROR: ACCOUNT_DISABLED (0x00000005)'], 403);
        }
        $userLevel = $userData[$user]['level'];
        $accessibleDocs = [];
        foreach ($documents as $filename => $data) {
            if ($userLevel >= $data['level']) {
                $accessibleDocs[$filename] = $data;
            }
        }

        sendResponse([
            'success' => true,
            'msg' => $userData[$user]['msg'],
            'user' => $user,
            'userName' => $userData[$user]['name'],
            'level' => $userLevel,
            'docs' => $accessibleDocs
        ]);
    } else {
        sendResponse(['success' => false, 'msg' => 'LOGON_FAILURE: Unknown user name or bad password.'], 401);
    }
} elseif ($action === 'command') {
    $cmdParts = explode(' ', strtoupper($_POST['cmd'] ?? ''));
    $baseCmd = $cmdParts[0];
    $arg = isset($cmdParts[1]) ? $cmdParts[1] : null;
    $key = isset($cmdParts[2]) ? $cmdParts[2] : null;
    $currentUser = strtoupper($_POST['currentUser'] ?? 'GUEST');
    
    $userLevel = isset($userData[$currentUser]) ? $userData[$currentUser]['level'] : 0;

    // --- GLOBAL COMMAND PERMISSION CHECK ---
    if (isset($commandPerms[$baseCmd]) && $userLevel < $commandPerms[$baseCmd]) {
        sendResponse(['output' => ["ERROR: Administrative privileges required (EPERM).", "LEVEL_REQUIRED: " . $commandPerms[$baseCmd]]], 403);
    }

    // 429 Overload Simulation
    if (rand(1, 40) === 1) {
        sendResponse(['output' => ["ERROR: Resource exhaustion at 0x" . strtoupper(substr(md5(time()), 0, 8)), "STATUS: STACK_OVERFLOW"]], 429);
    }

    switch ($baseCmd) {
        case 'HELP':
            $available = [];
            foreach ($commandPerms as $cmd => $lvl) {
                if ($userLevel >= $lvl) $available[] = $cmd;
            }
            sendResponse(['output' => ["AVAILABLE_COMMANDS: " . implode(", ", $available)]]);
            break;
        case 'DIR':
        case 'LS':
            $lines = ["┌── DIRECTORY_OF_C:\\SYSTEM\\DATA\\ ──┐", "│                                   │"];
            foreach (['CORE', 'SUBJECTS', 'RESEARCH'] as $cat) {
                $lines[] = "├── [$cat]";
                foreach ($documents as $filename => $data) {
                    if ($userLevel >= $data['level'] && ($data['cat'] ?? '') === $cat) {
                        $lines[] = "│   " . str_pad($filename, 22) . " [FILE]";
                    }
                }
            }
            $lines[] = "└───────────────────────────────────┘";
            sendResponse(['output' => $lines]);
            break;
        case 'SCAN':
            $lines = ["┌── SYSTEM_USER_SECURITY_SCAN ──────┐", "│ ID      USER      PRIVILEGE   STATE   │", "├───────────────────────────────────┤"];
            foreach ($userData as $uID => $data) {
                $status = $data['enabled'] ? "ACTIVE " : "REVOKED";
                $lines[] = "│ " . str_pad($uID, 8) . str_pad($data['name'], 10) . str_pad($data['level'], 12) . $status . " │";
            }
            $lines[] = "├───────────────────────────────────┤";
            $lines[] = "│ TOTAL_ENTRIES: " . str_pad(count($userData), 19) . " │";
            $lines[] = "└───────────────────────────────────┘";
            sendResponse(['output' => $lines]);
            break;
        case 'READ':
        case 'CAT':
            if (!$arg) {
                sendResponse(['output' => ["SYNOPSIS: READ <FILE_PATH>"]], 400);
            } elseif (isset($documents[$arg])) {
                $doc = $documents[$arg];
                if (isset($doc['gone']) && $doc['gone']) {
                    sendResponse(['output' => ["ERROR: Illegal seek or corrupted sector."]], 410);
                }
                if (isset($doc['censored']) && $doc['censored']) {
                    sendResponse(['output' => ["ERROR: Administrative block active."]], 451);
                }
                if ($userLevel >= $doc['level']) {
                    if (isset($doc['encrypted']) && $doc['encrypted']) {
                        sendResponse(['output' => ["ERROR: Read-only or encrypted file-system.", "ADVISORY: DECRYPT <FILE> <PASSWORD>"]], 423);
                    } else {
                        $title = $doc['title'] ?? str_replace('.JSON', '', strtoupper($arg));
                        $style = $doc['style'] ?? 'STANDARD';
                        $content = $doc['content'] ?? '[ EMPTY_SECTOR ]';
                        
                        $outputLines = renderDocument($title, $content, $style);
                        sendResponse([
                            'stream' => $outputLines
                        ]);
                    }
                } else {
                    sendResponse(['output' => ["ERROR: Access violation at address 0x00000000."]], 403);
                }
            } else {
                sendResponse(['output' => ["ERROR: File move or not found on this drive."]], 404);
            }
            break;
        case 'DECRYPT':
            if (!$arg) {
                sendResponse(['output' => ["SYNOPSIS: DECRYPT <FILE_PATH> <AUTH_KEY>"]], 400);
            } elseif (isset($documents[$arg])) {
                $doc = $documents[$arg];
                if ($userLevel >= $doc['level'] && ($doc['encrypted'] ?? false)) {
                    if ($userLevel >= 4 || ($key && $key === strtoupper($doc['decryptionKey']))) {
                        sendResponse(['action' => 'DECRYPT_FLOW', 'filename' => $arg, 'content' => $doc['content'], 'output' => ["INITIALIZING..."]]);
                    } else {
                        sendResponse(['output' => ["ERROR: Decryption key mismatch (0x8004100E)."]], 401);
                    }
                } else {
                    sendResponse(['output' => ["ERROR: Permission denied (EACCES)."]], 403);
                }
            } else { sendResponse(['output' => ["ERROR: File not found."]], 404); }
            break;
        case 'ENABLE':
        case 'DISABLE':
            if (!$arg) {
                sendResponse(['output' => ["SYNOPSIS: $baseCmd <USER_IDENTIFIER>"]], 400);
            } elseif (!isset($userData[$arg])) {
                sendResponse(['output' => ["ERROR: No such user found in database."]], 404);
            } else {
                $state = ($baseCmd === 'ENABLE');
                sendResponse(['output' => [
                    "TARGET: $arg",
                    "ACTION: " . ($state ? "ENABLE" : "DISABLE"),
                    "STATUS: OK"
                ]]);
            }
            break;
        case 'TASKLIST':
            $lines = [
                "Image Name                     PID Session Name        Mem Usage",
                "========================= ======== ================ ============"
            ];
            foreach ($systemTasks as $t) {
                $lines[] = str_pad($t['image'], 25) . " " . 
                           str_pad($t['pid'], 8) . " " . 
                           str_pad($t['session'], 16) . " " . 
                           str_pad($t['mem'], 12, " ", STR_PAD_LEFT);
            }
            sendResponse(['output' => $lines]);
            break;
        case 'STATUS':
            sendResponse(['output' => ["CPU_LOAD: " . rand(10, 40) . "." . rand(0, 9) . "%", "MEM_USAGE: " . rand(40, 70) . "%", "NET_UPLINK: CONNECTED", "DISK_STATE: OPTIMAL", "THREAD_ID: 0x" . strtoupper(substr(md5(time()), 0, 8))]]);
            break;
        case 'LOGS':
            $lines = ["┌── SYSTEM_EVENT_LOG ──────────────┐"];
            foreach ($loreEvents as $e) {
                $e = str_replace('[CURRENT_TIME]', '[' . date('H:i:s') . ']', $e);
                $lines[] = "│ " . str_pad($e, 32) . " │";
            }
            $lines[] = "└──────────────────────────────────┘";
            sendResponse(['output' => $lines]);
            break;
        case 'SYSTEM':
            sendResponse(['output' => ["NullOS v8.2 // KERNEL: x64_LDR_3.0", "CPU: x64 GENERIC PROCESSOR @ 3.40GHz", "HARDWARE_UUID: " . strtoupper(substr(md5($currentUser), 0, 12))]]);
            break;
        case 'WHOAMI':
            sendResponse(['output' => ["CURRENT_USER: $currentUser (" . ($userData[$currentUser]['name'] ?? 'LOCAL_USER') . ")", "SECURITY_POLICY: " . ($userLevel >= 4 ? 'ROOT_ADMIN' : 'RESTRICTED_USER')]]);
            break;
        case 'BURGER':
            sendResponse(['output' => [
                "LAUNCHING: Burger-King-Kiosk.exe...",
                "STATUS: ATTEMPTING_TO_HAVE_IT_YOUR_WAY",
                "[=======]",
                "( ooooo )",
                "[=======]",
                "SEC_OVERRIDE: FLAME_BROILED_ACTIVE"
            ]]);
            break;
        case 'PING':
            $target = $arg ?? '127.0.0.1';
            $lines = ["Pinging $target with 32 bytes of data:"];
            for ($i = 0; $i < 4; $i++) {
                $lines[] = "Reply from $target: bytes=32 time=" . rand(1, 15) . "ms TTL=" . rand(50, 64);
            }
            sendResponse(['output' => $lines]);
            break;
        case 'DATE':
            sendResponse(['output' => ["Current date: " . date('Y-m-d')]]);
            break;
        case 'TIME':
            sendResponse(['output' => ["Current time: " . date('H:i:s')]]);
            break;
        case 'ECHO':
            $val = isset($_POST['cmd']) ? substr($_POST['cmd'], 5) : '';
            sendResponse(['output' => [$val]]);
            break;
        case 'PWD':
            sendResponse(['output' => ["C:\\SYSTEM\\DATA\\"]]);
            break;
        case 'HOSTNAME':
            sendResponse(['output' => ["NULL-OS-NODE-01"]]);
            break;
        case 'VER':
            sendResponse(['output' => ["NullOS Version 8.2.1975 [Standard Build]"]]);
            break;
        case 'RECOVERY':
        case 'INJECT':
            sendResponse(['output' => ["ERROR: Segmentation fault (core dumped)."]], 501);
            break;
        default:
            sendResponse(['output' => ["ERROR: Invalid syntax or unrecognized command."]], 400);
    }
} else { sendResponse(['success' => false, 'error' => 'ERROR: Invalid request format.'], 400); }
exit;
