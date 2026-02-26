<?php
/**
 * ONE-TIME MIGRATION: Rename "You Tried" rarity to "Relic"
 */
require_once 'api/init.php';

checkScope('admin');

echo "Starting migration...\n";

try {
    $stmt = $pdo->prepare("UPDATE cards SET rarity_name = 'Relic' WHERE rarity_name = 'You Tried'");
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "Updated $count cards from 'You Tried' to 'Relic'.\n";
    
    echo "Migration completed successfully.";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
?>
