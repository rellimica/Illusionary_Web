<?php
/**
 * PAWNSHOP API
 * Handles the logic for trading card instances for Mana.
 */
require_once 'init.php';

// --- SHARED UTILITIES ---

/**
 * Returns the current deterministic demand for the day.
 */
function getDailyDemand($userId = null) {
    global $pdo, $PAWNSHOP_ROTATION_OFFSET, $CARD_RARITY_VALUES;
    $base_seed = (int)date('Ymd') + ($PAWNSHOP_ROTATION_OFFSET ?? 0);
    mt_srand($base_seed);

    // Hostility Check (Per User)
    $is_hostile = false;
    if ($userId) {
        $user_seed = crc32($base_seed . $userId);
        mt_srand($user_seed);
        if (mt_rand(1, 100) <= 5) $is_hostile = true;
        mt_srand($base_seed); // Reset to base seed for demand consistency
    }
    
    $all_rarities = array_map('ucfirst', array_keys($CARD_RARITY_VALUES));
    
    // 1. Determine Mode: 60% Rarity, 40% Specific Names
    $mode_roll = mt_rand(1, 100);
    $type = ($mode_roll <= 60) ? 'rarity' : 'name';
    
    if ($type === 'rarity') {
        $count = mt_rand(1, 4);
        $keys = (array)array_rand($all_rarities, min($count, count($all_rarities)));
        $selected = [];
        foreach ($keys as $k) $selected[] = $all_rarities[$k];
        
        return [
            'type' => 'rarity',
            'values' => $selected,
            'label' => strtoupper(implode(' & ', $selected)) . " Cards",
            'max_haggles' => mt_rand(0, 2),
            'mood_bias' => mt_rand(-10, 10) / 100,
            'is_stingy' => (mt_rand(1, 100) <= 10),
            'is_hostile' => $is_hostile
        ];
    } else {
        // Name Mode: Pick 5-10 random cards from the database
        $stmt = $pdo->query("SELECT id, name FROM cards WHERE is_trade = 1");
        $all_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$all_cards) {
            return ['type' => 'rarity', 'values' => ['Common'], 'label' => 'COMMON Cards'];
        }

        $count = mt_rand(1, 12);
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

        // --- MOOD DYNAMICS ---
        // 1. Max Haggles (0-2)
        $max_haggles = mt_rand(0, 2);
        
        // 2. Daily Mood (-10% to +10%)
        $mood_bias = mt_rand(-10, 10) / 100;
        
        // 3. Stingy Flag (10% chance)
        $is_stingy = mt_rand(1, 100) <= 10;

        return [
            'type' => 'name',
            'values' => $ids,
            'label' => $label,
            'max_haggles' => $max_haggles,
            'mood_bias' => $mood_bias,
            'is_stingy' => $is_stingy,
            'is_hostile' => $is_hostile
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
    
    // 0. Fetch User Status
    $user_stmt = $pdo->prepare("SELECT tokens, pawshop_open FROM users WHERE discord_id = ?");
    $user_stmt->execute([$my_id]);
    $user_data = $user_stmt->fetch();
    $user_tokens = (int)($user_data['tokens'] ?? 0);
    $pawshop_open = (int)($user_data['pawshop_open'] ?? 0);

    if (!$pawshop_open) {
        echo json_encode(['success' => false, 'message' => 'The Backroom remains sealed.']);
        exit;
    }

    $demand = getDailyDemand($my_id);
    if ($demand['is_hostile']) {
        $demand['label'] = "NULL HAS A BONE TO PICK WITH YOU. NO TRADES TODAY.";
        echo json_encode(['success' => true, 'cards' => [], 'demand' => $demand, 'balance' => $user_tokens]);
        exit;
    }

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
        'demand' => $demand,
        'balance' => $user_tokens
    ]);
    exit;
}

/**
 * 1.5 ADMIN: GET VALUATION GUIDE
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_guide') {
    checkScope('admin');
    $guide = [
        "section1" => [
            "title" => "Null's Discernment (Entry)",
            "text" => "The Dealer does not accept trifles. He seeks exactly three cards of history that resonate with today's Archive demand. Anything less—or anything irrelevant—is beneath his notice."
        ],
        "section2" => [
            "title" => "A Volatile Temperament (Moods)",
            "text" => "Null is not a machine. He is a ghost of the Archive, and his resonance shifts with the sunrise. Some days he is distant and stingy (10% chance of -5% penalty), and his patience for your 'finangling' (0-2 attempts) is as unpredictable as his daily temperament (-10% to +10%)."
        ],
        "sectionHOSTILE" => [
            "title" => "Personal Resonance (Hostility)",
            "text" => "Null's recognition of you is fickle. Every cycle, there is a 5% chance he decides he simply does not like YOU today. In this state, he will refuse all trades, clear his table of your offerings, and bar you from the Backroom until the next sunrise."
        ],
        "section3" => [
            "title" => "The Burden of Boredom (Penalties)",
            "text" => "Offer him nothing but commonalities, and you risk his utter boredom (60% chance of -20% penalty). Push your luck with too many haggles, and he may impose a hidden Greed Tax (50% chance of -15%) just to see you squirm."
        ],
        "section4" => [
            "title" => "Echoes of Inscription (Bonuses)",
            "text" => "Null values the weight of words. Inscribed cards carry a heavy bonus (+25% per card). Variation cards also resonate with him, granting unique bonuses based on their rarity (10% to 100%). Patterns like 'Full House' (3 of a kind, Rare+, +100% Bonus), 'High Order' (3 same rarity, +25% Bonus), and 'The Spectrum' (3 different rarities, +10% Bonus) further elevate the offer."
        ],
        "section5" => [
            "title" => "Fragments of the Archive (Dust)",
            "text" => "Null does not waste. For valuations that do not result in a whole Mana (e.g., 24.75), the fractional part is converted to Archive Dust (75 fragments). Once a user accumulates 1,000 fragments, they crystallize into 1 whole Mana on the next trade."
        ],
        "section6" => [
            "title" => "The Curator's Curiosities (Wealth)",
            "text" => "He watches your hoard. To the hollowed (under 25 Mana), he offers pity (+15% Bonus). From the bloated (over 75 Mana), he extracts a 'Whale's Tax' (20% Penalty), knowing they can afford his price."
        ],
        "section7" => [
            "title" => "The Dealer's Math (Example)",
            "text" => "Example: Providing (3) Rare cards (6 Mana base) with Inscriptions (+75%) as a 'Full House' (+100% Bonus) while nearly empty (+15% Bonus) yields: 6 * 1.75 * 2.0 * 1.15 = ~24 Mana (pre-mood variance). Any decimals are added to the user's Dust pool."
        ]
    ];
    echo json_encode(['success' => true, 'guide' => $guide]);
    exit;
}

/**
 * calculateValuation() - Centralized, deterministic logic for card valuation.
 */
function calculateValuation($instances, $my_id, $user_tokens, $haggle_count, $instance_ids, $demand) {
    global $CARD_RARITY_VALUES, $VARIATION_BONUSES;
    
    $base_mana = 0;
    $bonuses = [];
    $total_multiplier = 1.0;
    $names = [];
    $rarities = [];
    $inscription_count = 0;

    foreach ($instances as $inst) {
        $base_mana += $CARD_RARITY_VALUES[strtolower($inst['rarity_name'])] ?? 1;
        $names[] = $inst['name'];
        $rarities[] = $inst['rarity_name'];
        if (!empty(trim($inst['message'] ?? ''))) $inscription_count++;
    }

    if ($inscription_count > 0) {
        $total_multiplier += ($inscription_count * 0.25);
        $bonuses[] = "Inscribed History (+" . ($inscription_count * 25) . "%)";
    }

    foreach ($instances as $inst) {
        $var = $inst['variations'] ?? null;
        if ($var && isset($VARIATION_BONUSES[$var])) {
            $bonus = $VARIATION_BONUSES[$var];
            $total_multiplier += $bonus;
            $bonuses[] = "Variation: " . ucfirst($var) . " (+" . ($bonus * 100) . "%)";
        }
    }

    $unique_names = array_unique($names);
    $unique_rarities = array_unique($rarities);

    if (count($unique_names) === 1) {
        if (!in_array(strtolower($rarities[0]), ['common', 'uncommon'])) {
            $bonuses[] = "Synergy: Full House (+100% Bonus)";
            $total_multiplier *= 2.0;
        } else {
            $bonuses[] = "Synergy: Common Set (No Bonus)";
        }
    } elseif (count($unique_rarities) === 1) {
        $bonuses[] = "Synergy: High Order (+25% Bonus)";
        $total_multiplier *= 1.25;
    } elseif (count($unique_rarities) === 3) {
        $bonuses[] = "Synergy: Diversity (+10% Bonus)";
        $total_multiplier *= 1.1;
    }

    if ($user_tokens < 25) {
        $bonuses[] = "Null's Pity (+15% Bonus)";
        $total_multiplier *= 1.15;
    } elseif ($user_tokens >= 75) {
        $bonuses[] = "The Whale's Tax (-20% Penalty)";
        $total_multiplier *= 0.8;
    }

    sort($instance_ids);
    $seed = crc32($my_id . implode('-', $instance_ids) . $haggle_count . date('Ymd'));
    mt_srand($seed);

    $finess_multiplier = (mt_rand(70, 130)) / 100;
    $total_multiplier += $demand['mood_bias'] ?? 0;

    if ($demand['is_stingy'] ?? false) {
        $bonuses[] = "Null is Distant (-5%)";
        $total_multiplier -= 0.05;
    }
    if ($haggle_count >= 2 && mt_rand(1, 100) <= 50) {
        $bonuses[] = "Greed Tax (-15%)";
        $total_multiplier -= 0.15;
        $finess_multiplier = (mt_rand(60, 110)) / 100;
    }
    
    $all_basic = true;
    foreach ($instances as $inst) { if (!in_array(strtolower($inst['rarity_name']), ['common', 'uncommon'])) { $all_basic = false; break; } }
    if ($all_basic && mt_rand(1, 100) <= 60) {
        $bonuses[] = "Null's Boredom (-20%)";
        $total_multiplier -= 0.20;
    }

    $raw_valuation = $base_mana * $total_multiplier * $finess_multiplier;
    if ($base_mana > 0 && $raw_valuation <= 0) $raw_valuation = 0.01;

    return [
        'raw' => (float)$raw_valuation,
        'mana' => (int)floor($raw_valuation),
        'fragments' => (int)round(($raw_valuation - floor($raw_valuation)) * 100),
        'bonuses' => $bonuses,
        'finess_percent' => round(($finess_multiplier - 1) * 100)
    ];
}

/**
 * 2. GET VALUATION (Finangle)
 */
if (isset($_GET['action']) && $_GET['action'] === 'get_valuation') {
    checkScope('read');
    
    $instance_ids = $_GET['instance_ids'] ?? [];
    if (!is_array($instance_ids) || count($instance_ids) !== 3) {
        errorResponse('The Backroom requires exactly three cards.', 400);
    }

    try {
        $demand = getDailyDemand($my_id);
        if ($demand['is_hostile'] ?? false) errorResponse("Null looks at you with utter contempt. 'I don't think I like YOU today,' he whispers.", 403);
        $haggle_count = (int)($_GET['haggle_count'] ?? 0);

        $user_row = $pdo->prepare("SELECT tokens, pawshop_open, archive_dust FROM users WHERE discord_id = ?");
        $user_row->execute([$my_id]);
        $user = $user_row->fetch();
        if (!$user || !(int)$user['pawshop_open']) errorResponse('The Backroom remains sealed.', 403);

        $placeholders = implode(',', array_fill(0, count($instance_ids), '?'));
        $stmt = $pdo->prepare("SELECT ci.id, ci.message, ci.variations, c.id as card_id, c.name, c.rarity_name FROM card_instances ci JOIN cards c ON ci.card_id = c.id WHERE ci.id IN ($placeholders) AND ci.user_discord_id = ?");
        $stmt->execute(array_merge($instance_ids, [$my_id]));
        $instances = $stmt->fetchAll();
        if (count($instances) !== 3) errorResponse('Cards are missing from the table.', 400);

        $val = calculateValuation($instances, $my_id, (int)$user['tokens'], $haggle_count, $instance_ids, $demand);

        echo json_encode([
            'success' => true,
            'valuation' => $val['mana'],
            'fragments' => $val['fragments'],
            'bonuses' => $val['bonuses'],
            'finess_msg' => ($val['finess_percent'] >= 0 ? "+".$val['finess_percent']."%" : $val['finess_percent']."%"),
            'finess_type' => $val['finess_percent'] >= 0 ? 'generous' : 'annoyed',
            'balance' => (int)$user['tokens'],
            'max_haggles' => $demand['max_haggles'] ?? 2,
            'archive_dust' => (int)$user['archive_dust']
        ]);
        exit;
    } catch (Exception $e) { errorResponse($e->getMessage(), 500); }
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
        echo json_encode(['success' => false, 'message' => 'Null requires exactly three cards.']);
        exit;
    }

    try {
        $demand = getDailyDemand($my_id);
        if ($demand['is_hostile'] ?? false) errorResponse("Null looks at you with utter contempt. 'I don't think I like YOU today,' he whispers.", 403);
        
        // 1. Final Validation & Access Check
        $user_stmt = $pdo->prepare("SELECT pawshop_open, tokens, archive_dust FROM users WHERE discord_id = ?");
        $user_stmt->execute([$my_id]);
        $user_row = $user_stmt->fetch();
        if (!$user_row || !(int)$user_row['pawshop_open']) errorResponse('The Backroom remains sealed.', 403);
        
        $current_dust = (int)($user_row['archive_dust'] ?? 0);
        $haggle_count = (int)($_POST['haggle_count'] ?? 0);

        $placeholders = implode(',', array_fill(0, count($instance_ids), '?'));
        $stmt = $pdo->prepare("SELECT ci.id, ci.card_id, ci.message, ci.variations, c.rarity_name, c.is_trade, c.name FROM card_instances ci JOIN cards c ON ci.card_id = c.id WHERE ci.id IN ($placeholders) AND ci.user_discord_id = ?");
        $stmt->execute(array_merge($instance_ids, [$my_id]));
        $instances = $stmt->fetchAll();

        if (count($instances) !== 3) errorResponse('The deal has collapsed. Cards are missing.', 400);

        // Security check for card eligibility
        foreach ($instances as $inst) {
            if (!$inst['is_trade']) errorResponse("'{$inst['name']}' is not for trade.", 403);
            if ($demand['type'] === 'rarity' && !in_array($inst['rarity_name'], $demand['values'])) errorResponse("Today Null only seeks {$demand['label']}.", 403);
            if ($demand['type'] === 'name' && !in_array((int)$inst['card_id'], $demand['values'])) errorResponse("Today Null has no interest in '{$inst['name']}'.", 403);
        }

        // --- TRANSACTION EXECUTION ---
        $pdo->beginTransaction();
        
        // 1. Re-calculate precise valuation on server
        $val = calculateValuation($instances, $my_id, (int)$user_row['tokens'], $haggle_count, $instance_ids, $demand);
        
        // 2. Lock and verifying posession
        $check_stmt = $pdo->prepare("SELECT card_id FROM card_instances WHERE id = ? AND user_discord_id = ? FOR UPDATE");
        $card_counts = [];
        foreach ($instance_ids as $iid) {
            $check_stmt->execute([$iid, $my_id]);
            $row = $check_stmt->fetch();
            if (!$row) { $pdo->rollBack(); errorResponse("Verification Failed: Card instance #$iid is no longer yours.", 400); }
            $cid = (int)$row['card_id'];
            $card_counts[$cid] = ($card_counts[$cid] ?? 0) + 1;
        }

        // 3. Remove instances
        $del_stmt = $pdo->prepare("DELETE FROM card_instances WHERE id = ? AND user_discord_id = ?");
        foreach ($instance_ids as $iid) $del_stmt->execute([$iid, $my_id]);

        // 4. Update card counts
        $sub_stmt = $pdo->prepare("UPDATE user_cards SET count = count - ? WHERE user_discord_id = ? AND card_id = ?");
        foreach ($card_counts as $cid => $qty) $sub_stmt->execute([$qty, $my_id, $cid]);
        $pdo->prepare("DELETE FROM user_cards WHERE count <= 0 AND user_discord_id = ?")->execute([$my_id]);

        // 5. Add Mana and Dust
        $total_mana_to_add = $val['mana'];
        $new_dust = $current_dust + $val['fragments'];
        
        $bonus_mana = 0;
        if ($new_dust >= 1000) {
            $bonus_mana = floor($new_dust / 1000);
            $new_dust %= 1000;
        }
        $final_mana_total = $total_mana_to_add + $bonus_mana;

        $update_user_stmt = $pdo->prepare("UPDATE users SET tokens = tokens + ?, archive_dust = ?, pawshop_open = GREATEST(0, CAST(pawshop_open AS SIGNED) - 1) WHERE discord_id = ?");
        $update_user_stmt->execute([$final_mana_total, $new_dust, $my_id]);

        $final_bal = $pdo->prepare("SELECT tokens, archive_dust FROM users WHERE discord_id = ?");
        $final_bal->execute([$my_id]);
        $final_data = $final_bal->fetch();

        $pdo->commit();

        echo json_encode([
            'success' => true, 
            'message' => "The Backroom has claimed your cards. You received $final_mana_total Mana." . ($bonus_mana > 0 ? " (A crystal has formed from the dust!)" : ""),
            'received' => $final_mana_total,
            'balance' => $final_data['tokens'],
            'archive_dust' => $final_data['archive_dust']
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'The void is unstable: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid Pawnshop action.']);
exit;
