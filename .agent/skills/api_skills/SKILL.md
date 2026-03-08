---
Name: API Skills
Description: Comprehensive guide to the Illusionary Web API system, including authentication, core utilities, and endpoint logic.

---

# Illusionary Web API System

The Illusionary Web API is a PHP-based backend designed for card collection, trading, and lore interaction. It uses a multi-tier scoping system for security and deterministic logic for its gameplay mechanics.

## Architecture & Security

### Initialization (`init.php`)
All endpoints must require `init.php` at the start. It handles:
- **Environment Loading**: Merges `.env` from `/root/Illusionary/` and the local project root.
- **Session Management**: Uses `ILLUSIONARY_SID` for browser-based authentication.
- **Multi-Tier Scoping**:
    - `public`: Unauthenticated access (minimal).
    - `read`: Can view inventory and stats. Often requires an `READ_API_KEY`.
    - `write`: Can perform actions like drawing cards or proposing trades.
    - `admin`: Full access, including bypasses of certain restrictions.
- **Authentication**: Checks `x-api-key` header or `$_POST['api_key']`, fallback to PHP sessions.

### Core Utilities (`config.php`)
- **`scrambleSN($id)`**: Deterministically converts an auto-incrementing database ID into a 9-digit Serial Number for display.
- **`isAdmin($userId)`**: Checks if a Discord ID is in the administrative whitelist.
- **`$CARD_RARITY_VALUES`**: Global mapping of rarity tiers to their base Mana value (e.g., Rare = 2, Legendary = 4).

## API Endpoints

### 1. Draw System (`draw.php`)
Handles card packs and single draws.
- **Action**: `draw_card` (POST)
- **Logic**: Deducts 1 mana atomically, performs a weighted rarity roll based on `rarity_tier` in the `cards` table, and assigns a new instance to the user.

### 2. Collection & Stats (`collection.php`)
Provides user data for the dashboard.
- **Action**: `hydrate_collection` (GET/POST)
- **Features**: Returns user stats (total, unique, completion %), current token balance, and a paginated list of owned cards with their Serial Numbers.
- **Action**: `claim_mana` (POST): Processes daily mana rewards.

### 3. Pawnshop ("The Backroom") (`pawnshop.php`)
A complex trading interface with deterministic NPC logic (Null).
- **Actions**: `get_inventory`, `get_valuation`, `pawn_cards`.
- **Mechanics**:
    - **Daily Demand**: Switches between Rarity-based and Name-based demand cycles.
    - **Valuation ("Finangle")**: Multi-step calculation involving rarity, inscriptions, set bonuses (Full House, High Order), and user wealth.
    - **Null's Hostility**: 5% chance per user per day to be barred from trading.
    - **Archive Dust**: Fractional mana remains from trades are stored as "Dust" and crystallize into Mana at 1,000 fragments.

### 4. Trading System (`trade.php`)
Peer-to-peer card exchange.
- **Actions**: `propose_trade`, `respond_trade`, `get_inventories`.
- **Security**: Requires Cloudflare Turnstile verification for proposals to prevent botting.
- **Integrity**: Uses `FOR UPDATE` in SQL transactions to ensure card ownership doesn't change during the response process.

### 5. Morphic Kernel (`terminal.php`)
The lore-heavy terminal interface.
- **Logic**: Handles virtual file systems (`DIR`, `READ`, `DECRYPT`) and simulated OS commands.
- **Permissions**: Documents have their own `level` requirements, mapped to the user's system privilege level.
