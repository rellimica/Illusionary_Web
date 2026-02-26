<?php
/**
 * PAWNSHOP API
 * Handles the logic for trading card instances for Mana.
 * CURRENT STATUS: TEST MODE (No database mutation)
 */
require_once 'init.php';

// --- SHARED UTILITIES ---

/**
 * Returns the current deterministic demand for the day.
 */
function getDailyDemand() {
    global $pdo;
    $seed = (int)date('Ymd');
    mt_srand($seed);
    
    // 1. Determine Mode: 60% Rarity, 40% Specific Names
    $mode_roll = mt_rand(1, 100);
    $type = ($mode_roll <= 60) ? 'rarity' : 'name';
    
    if ($type === 'rarity') {
        $all_rarities = ['Common', 'Uncommon', 'Rare', 'Epic', 'Legendary', 'Mythic', 'Relic'];
        $count = mt_rand(2, 3);
        $keys = (array)array_rand($all_rarities, $count);
        $selected = [];
        foreach ($keys as $k) $selected[] = $all_rarities[$k];
        
        return [
            'type' => 'rarity',
            'values' => $selected,
            'label' => strtoupper(implode(' & ', $selected)) . " Cards"
        ];
    } else {
        // Name Mode: Pick 5-10 random cards from the database
        $stmt = $pdo->query("SELECT id, name FROM cards WHERE is_trade = 1");
        $all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$all_cards) {
            return ['type' => 'rarity', 'values' => ['Common'], 'label' => 'COMMON Cards'];
        }

        $count = mt_rand(6, 12);
        if ($count > count($all_cards)) $count = count($all_cards);
        
        // We need a stable random selection, so we sort by ID and then pick based on the seed
        usort($all_cards, function($a, $b) { return $a['id'] <=> $b['id']; });

        // Fisher-Yates shuffle with mt_srand is stable in the same request
        // But since we want to pick N cards, let's just shuffle and slice
        shuffle($all_cards); 
        $subset = array_slice($all_cards, 0, $count);
        
        $ids = array_map(function($c) { return (int)$c['id']; }, $subset);
        $names = array_map(function($c) { return str_replace('_', ' ', $c['name']); }, array_slice($subset, 0, 3));
        
        $label = "The Dealer's Choice: " . implode(', ', $names) . (count($subset) > 3 ? " & Others" : "");

        return [
            'type' => 'name',
            'values' => $ids,
            'label' => $label
        ];
    }
}

// --- ACTIONS ---

/**
 * 1. GET FULL INVENTORY
 * Returns all tradable cards owned by the authenticated user in one go, plus current demand.
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_inventory') {
    checkScope('read');
    
    $demand = getDailyDemand();

    // 1. Fetch all cards with counts
    $cards_stmt = $pdo->prepare("
        SELECT c.*, uc.count 
        FROM cards c
        JOIN user_cards uc ON c.id = uc.card_id
        WHERE uc.user_discord_id = ? AND c.is_trade = 1
        ORDER BY c.card_order ASC
    ");
    $cards_stmt->execute([$my_id]);
    $cards = $cards_stmt->fetchAll();

    // 2. Fetch ALL instances for these cards
    if (!empty($cards)) {
        $inst_stmt = $pdo->prepare("
            SELECT id, card_id, message, variations 
            FROM card_instances 
            WHERE user_discord_id = ? 
            ORDER BY id ASC
        ");
        $inst_stmt->execute([$my_id]);
        $all_instances = $inst_stmt->fetchAll();

        foreach ($cards as &$card) {
            $card['sns'] = array_values(array_map(function($i) { 
                return [
                    'id' => (int)$i['id'],
                    'sn' => scrambleSN((int)$i['id']),
                    'message' => $i['message'] ?? null,
                    'variant' => $i['variations'] ?? null
                ];
            }, array_filter($all_instances, function($i) use ($card) {
                return $i['card_id'] == $card['id'];
            })));
        }
    }

    echo json_encode([
        'success' => true,
        'cards' => $cards,
        'demand' => $demand
    ]);
    exit;
}

/**
 * 2. GET VALUATION (Finangle)
 * Returns a randomized yield for the selected cards.
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_valuation') {
    checkScope('read');
    
    $instance_ids = $_GET['instance_ids'] ?? [];
    if (!is_array($instance_ids) || count($instance_ids) !== 3) {
        echo json_encode(['success' => false, 'message' => 'The Backroom requires exactly three offerings for a valuation.']);
        exit;
    }

    try {
        $demand = getDailyDemand();
        $haggle_count = (int)($_GET['haggle_count'] ?? 0);

        // 1. Validate relics
        $placeholders = implode(',', array_fill(0, count($instance_ids), '?'));
        $stmt = $pdo->prepare("
            SELECT ci.id, c.rarity_name
            FROM card_instances ci
            JOIN cards c ON ci.card_id = c.id
            WHERE ci.id IN ($placeholders) AND ci.user_discord_id = ?
        ");
        $params = array_merge($instance_ids, [$my_id]);
        $stmt->execute($params);
        $instances = $stmt->fetchAll();

        if (count($instances) !== 3) {
            echo json_encode(['success' => false, 'message' => 'Cards are missing from the table.']);
            exit;
        }

        // 2. Base Calculation
        global $CARD_RARITY_VALUES;
        $base_mana = 0;
        $id_string = "";
        sort($instance_ids); // Sort IDs to ensure stable seed regardless of slot order
        foreach ($instances as $inst) {
            $base_mana += $CARD_RARITY_VALUES[strtolower($inst['rarity_name'])] ?? 1;
        }
        $id_string = implode('-', $instance_ids);

        // 3. SECURE SEEDING (Anti-Exploit)
        // Seed = (User ID + Card IDs + Haggle Attempt + Date)
        $date_seed = date('Ymd');
        $seed = crc32($my_id . $id_string . $haggle_count . $date_seed);
        mt_srand($seed);

        // 4. The "Finess" (Randomized Multiplier ±30%)
        $finess_multiplier = (mt_rand(70, 130)) / 100;
        $final_mana = floor($base_mana * $finess_multiplier);
        if ($base_mana > 0 && $final_mana <= 0) $final_mana = 1;

        $finess_percent = round(($finess_multiplier - 1) * 100);
        $finess_type = $finess_percent >= 0 ? 'generous' : 'annoyed';

        echo json_encode([
            'success' => true,
            'valuation' => $final_mana,
            'finess_msg' => ($finess_percent >= 0 ? "+$finess_percent%" : "$finess_percent%"),
            'finess_type' => $finess_type,
            'seed_debug' => $seed // Optional: helps us verify it's working
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

/**
 * 3. PAWN CARDS
 * Processes the final exchange.
 */
if (isset($_POST['action']) && $_POST['action'] === 'pawn_cards') {
    checkScope('write');
    
    $instance_ids = $_POST['instance_ids'] ?? [];
    $final_mana = (int)($_POST['final_mana'] ?? 0);

    if (!is_array($instance_ids) || count($instance_ids) !== 3) {
        echo json_encode(['success' => false, 'message' => 'Null requires exactly three offerings.']);
        exit;
    }

    try {
        $demand = getDailyDemand();

        // 1. Final Validation
        $placeholders = implode(',', array_fill(0, count($instance_ids), '?'));
        $stmt = $pdo->prepare("
            SELECT ci.id, ci.card_id, c.rarity_name, c.is_trade, c.name
            FROM card_instances ci
            JOIN cards c ON ci.card_id = c.id
            WHERE ci.id IN ($placeholders) AND ci.user_discord_id = ?
        ");
        $params = array_merge($instance_ids, [$my_id]);
        $stmt->execute($params);
        $instances = $stmt->fetchAll();

        if (count($instances) !== 3) {
            echo json_encode(['success' => false, 'message' => 'The deal has collapsed. Cards are missing.']);
            exit;
        }

        foreach ($instances as $inst) {
            if (!$inst['is_trade']) {
                echo json_encode(['success' => false, 'message' => "'{$inst['name']}' is not for trade."]);
                exit;
            }
            if ($demand['type'] === 'rarity' && !in_array($inst['rarity_name'], $demand['values'])) {
                echo json_encode(['success' => false, 'message' => "Today Null only seeks {$demand['label']}."]);
                exit;
            }
            if ($demand['type'] === 'name' && !in_array((int)$inst['card_id'], $demand['values'])) {
                echo json_encode(['success' => false, 'message' => "Today Null has no interest in '{$inst['name']}'. He seeks specific cards."]);
                exit;
            }
        }

        // --- TEST MODE BLOCK ---
        echo json_encode([
            'success' => true, 
            'message' => "TEST MODE: The Backroom has claimed your cards. You received $final_mana Mana.",
            'received' => $final_mana
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'The void is unstable: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Pawnshop action.']);
exit;
