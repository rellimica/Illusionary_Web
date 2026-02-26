# Illusionary API Documentation

The Illusionary API provides programmatic access to card collection data, trading systems, and game mechanics. It supports both session-based authentication for web users and API key-based access for bot services.

## 🔑 Authentication

Authentication is handled via the `X-API-Key` HTTP header. All requests must be made over HTTPS (or loopback for local bots).

### Access Tiers (Scopes)
| Tier | Key Requirement | Description |
| :--- | :--- | :--- |
| **read** | `READ_API_KEY` | View vaults, statistics, and public card data. |
| **write** | `WRITE_API_KEY` | Propose/respond to trades, draw cards, and claim mana. |
| **admin** | `ADMIN_API_KEY` | System-wide administrative operations. |

---

## 🏗️ Base URL
`https://illusionary.bigwyvern.com/api/` (or your local configured address)

---

## 📂 Endpoints

### 1. Collection API (`collection.php`)

#### **GET** `hydrate_collection`
Retrieves a user's vault data, including card details and serial numbers.

**Parameters:**
- `action=hydrate_collection` (Required)
- `user_id` (Required for bots): The Discord ID of the user to query.
- `page` (Optional): Pagination index (10 cards per page).

**Response Excerpt:**
```json
{
    "stats": {
        "total": 157,
        "unique": 29,
        "completion": 87.9
    },
    "cards": [
        {
            "name": "Green_Terrible_Terror",
            "count": "4",
            "sns": [
                { "id": 1660, "sn": 706544925 },
                { "id": 1661, "sn": 722030788 }
            ]
        }
    ]
}
```

---

### 2. Trade API (`trade.php`)

#### **POST** `get_selection_data`
Gets a list of active collectors and their total card counts.
*Required Scope: `read`*

#### **POST** `get_inventories`
Gets the comparative inventories of yourself and a trading partner.
*Required Scope: `read`*

#### **POST** `propose_trade`
Initiates a trade request between two users.
*Required Scope: `write`*

**Parameters:**
- `partner_id`: Target user's Discord ID.
- `my_offer`: JSON string of instance IDs you are giving.
- `their_offer`: JSON string of instance IDs you are requesting.

---

### 3. Draw API (`draw.php`)

#### **POST** `draw_card`
Spends Mana to draw a new random card into the bot's inventory.
*Required Scope: `write`*

---

## 🐍 Python Client Example

Use the provided `bot_api_client.py` for easy integration:

```python
from bot_api_client import IllusionaryAPI

# Initialize
bot = IllusionaryAPI(api_key="your_read_key")

# Query a vault
data = bot.get_vault(user_id="332684782888550410")
print(data['stats']['total'])
```

## ⚠️ Security Notes
- **Keep your API keys secret.** Do not commit your `.env` file to version control.
- **BOT_DISCORD_ID**: Actions performed by the bot (like drawing cards) are attributed to the ID defined in your local `.env`.
- **Serial Numbers**: The API returns both human-readable `sn` (scrambled) and database-level `id` (raw). Use `id` for internal logic/trades and `sn` for display.
