<?php
/**
 * NOTIFICATION SYSTEM API
 * Handles real-time alerts, read states, and delivery of system messages.
 */
require_once 'init.php';

// --- PROTECTION ---
guard($_POST, ['action' => 'required']);

// --- ACTIONS ---
if ($_POST['action'] === 'get_recent') {
    checkScope('read');
    $uid = $_POST['target_id'] ?? $my_id;
    
    $stmt = $pdo->prepare("
        SELECT *, UNIX_TIMESTAMP(created_at) as time_sec FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 20
    ");
    $stmt->execute([$uid]);
    $items = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'notifications' => $items]);
    exit;
}

if ($_POST['action'] === 'get_unread_count') {
    checkScope('read');
    $uid = $_POST['target_id'] ?? $my_id;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$uid]);
    $count = $stmt->fetchColumn();
    
    echo json_encode(['success' => true, 'count' => (int)$count]);
    exit;
}

if ($_POST['action'] === 'mark_read') {
    checkScope('write');
    $nid = $_POST['notification_id'] ?? null;
    
    if ($nid === 'all') {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$my_id]);
    } else {
        guard($_POST, ['notification_id' => 'num|required']);
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->execute([$nid, $my_id]);
    }
    
    echo json_encode(['success' => true]);
    exit;
}

if ($_POST['action'] === 'delete') {
    checkScope('write');
    $nid = $_POST['notification_id'] ?? null;
    
    if ($nid === 'all') {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->execute([$my_id]);
    } else {
        guard($_POST, ['notification_id' => 'num|required']);
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->execute([$nid, $my_id]);
    }
    
    echo json_encode(['success' => true]);
    exit;
}

errorResponse('Invalid Action', 400);
