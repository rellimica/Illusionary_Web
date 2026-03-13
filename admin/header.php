<?php
/**
 * ADMIN HEADER
 * Handles the <head> section and the primary navigation for the admin panel.
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illusionary Admin | <?php echo $page_title ?? 'Dashboard'; ?></title>
    
    <!-- FAVICONS -->
    <link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="../favicon/favicon.ico">
    
    <!-- STYLES -->
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php 
    require_once '../theme-config.php';
    injectTheme($THEME);
    ?>
</head>
<body>
    <?php include '../mobile-block.php'; ?>
    <div class="glass-bg"></div>
    
    <?php 
    $nav_subtitle = 'Admin Control';
    include '../nav.php'; 
    ?>

    <main class="container">
        <!-- ADMIN NAVIGATION -->
        <nav class="admin-tabs">
            <a href="index.php" class="tab-btn <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Overview</a>
            <a href="explorer.php" class="tab-btn <?php echo $current_page == 'explorer.php' ? 'active' : ''; ?>">Explorer</a>
            <a href="users.php" class="tab-btn <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">Users</a>
            <a href="system.php" class="tab-btn <?php echo $current_page == 'system.php' ? 'active' : ''; ?>">System & Notifs</a>
        </nav>
