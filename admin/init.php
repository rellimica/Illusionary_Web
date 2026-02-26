<?php
/**
 * ADMIN UI INITIALIZATION
 * This file is included at the top of every page in the /admin/ directory.
 */
session_name('ILLUSIONARY_SID');
session_start();

// 1. BASE REDIRECTION
if (!isset($_SESSION['user_authenticated'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: ../auth.php?redirect=$redirect");
    exit;
}

// 2. CONFIG & PERMISSIONS
require_once __DIR__ . '/../config.php';

$user_id = $_SESSION['user_data']['id'];
if (!isAdmin($user_id)) {
    header("Location: ../index.php");
    exit;
}

// 3. ENV PATHS for UI reference (though API does the heavy lifting)
$IMAGES_PATH = '../images/images/';
?>
