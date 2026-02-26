<?php
/**
 * DRAW SYSTEM API 
 * Ported from bot Python logic for consistent drop rates.
 */
require_once 'init.php';

// --- PROTECTION ---
if ($_POST['action'] !== 'draw_card') {
    errorResponse('Invalid action', 400);
}
checkScope('write');

// Check Global Trade/Draw Toggles if any (none yet requested, but good to have)
// if (!($DRAWS_ENABLED ?? true)) {
//     errorResponse('The Drawing System is currently undergoing maintenance.', 503);
// }

// --- THE DRAW LOGIC ---
$pdo->beginTransaction();

try {
    // 1. ATOMIC MANA DEDUCTION
    $stmt = $pdo->prepare("UPDATE users SET tokens = tokens - 1 WHERE discord_id = ? AND tokens > 0");
    $stmt->execute([$my_id]);

    if ($stmt->rowCount() === 0) {
        // Double check balance for specific error message
        $stmt = $pdo->prepare("SELECT tokens FROM users WHERE discord_id = ?");
        $stmt->execute([$my_id]);
        $current = $stmt->fetchColumn() ?: 0;
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'You do not have enough mana to draw a card.', 'balance' => $current]);
        exit;
    }

    // 2. WEIGHTED RARITY SELECTION
    // Fetch unique tiers from categories (only from non-hidden cards)
    $stmt = $pdo->query("SELECT DISTINCT rarity_tier FROM cards WHERE is_hidden = 0 ORDER BY rarity_tier DESC");
    $tiers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tiers)) {
        throw new Exception("The card pool is currently empty.");
    }

    // Weighted Random Roll
    $totalWeight = array_sum($tiers);
    $roll = mt_rand(0, mt_getrandmax()) / mt_getrandmax() * $totalWeight;
    $cumulative = 0;
    $selectedTier = $tiers[0];

    foreach ($tiers as $tier) {
        $cumulative += $tier;
        if ($roll <= $cumulative) {
            $selectedTier = $tier;
            break;
        }
    }

    // 3. CARD SELECTION (only from non-hidden cards)
    $stmt = $pdo->prepare("SELECT * FROM cards WHERE rarity_tier = ? AND is_hidden = 0");
    $stmt->execute([$selectedTier]);
    $pool = $stmt->fetchAll();

    if (empty($pool)) {
        throw new Exception("No cards found in the selected rarity tier.");
    }

    $selectedCard = $pool[array_rand($pool)];

    // 4. OWNERSHIP & TRACKING
    
    // A. Create unique instance (Serial Number)
    $stmt = $pdo->prepare("INSERT INTO card_instances (card_id, user_discord_id, source) VALUES (?, ?, 'draw')");
    $stmt->execute([$selectedCard['id'], $my_id]);
    $serialNumber = $pdo->lastInsertId();

    // B. Check if they already own it (for UI "New!" badge)
    $stmt = $pdo->prepare("SELECT 1 FROM user_cards WHERE user_discord_id = ? AND card_id = ?");
    $stmt->execute([$my_id, $selectedCard['id']]);
    $isNew = ($stmt->fetch() === false);

    // C. Atomic increment in summary table
    $stmt = $pdo->prepare("
        INSERT INTO user_cards (user_discord_id, card_id, count) 
        VALUES (?, ?, 1)
        ON DUPLICATE KEY UPDATE count = count + 1
    ");
    $stmt->execute([$my_id, $selectedCard['id']]);

    // D. Get final balance
    $stmt = $pdo->prepare("SELECT tokens FROM users WHERE discord_id = ?");
    $stmt->execute([$my_id]);
    $newBalance = $stmt->fetchColumn();

    $pdo->commit();

    // Success Response
    echo json_encode([
        'success' => true,
        'card' => [
            'id' => $selectedCard['id'],
            'sn' => $serialNumber,
            'name' => str_replace('_', ' ', $selectedCard['name']),
            'rarity' => $selectedCard['rarity_name'],
            'tier' => $selectedCard['rarity_tier'],
            'filename' => $selectedCard['filename'],
            'is_new' => $isNew
        ],
        'balance' => $newBalance
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    errorResponse($e->getMessage(), 500);
}
