# EBANX Take-Home Assignment

A simple banking API supporting deposit, withdraw, and transfer operations between accounts. State is kept in memory — no database required.

---

## Requirements

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Running the project

```bash
# Build and start the container
docker compose up -d --build

# The API will be available at:
# http://localhost:8000
```

```bash
# View logs
docker compose logs -f app

# Stop the container
docker compose down

# Run the test suite
docker compose exec app php artisan test

# Access the container shell
docker compose exec app bash
```

---

## Endpoints

### `POST /reset`

Clears all in-memory state.

**Response:** `200 OK`
```
OK
```

---

### `GET /balance`

Returns the balance of an account.

**Query params:** `account_id` (string)

**Responses:**
- `200` — account balance
- `404` — account not found

```bash
GET /balance?account_id=100

# Existing account → 200
10

# Non-existing account → 404
0
```

---

### `POST /event`

Executes a banking operation. The `type` field determines the operation.

**Headers:** `Content-Type: application/json`

#### Deposit

```json
{
    "type": "deposit",
    "destination": "100",
    "amount": 10
}
```

**Response `201`:**
```json
{
    "destination": {
        "id": "100",
        "balance": 10
    }
}
```

---

#### Withdraw

```json
{
    "type": "withdraw",
    "origin": "100",
    "amount": 5
}
```

**Response `201`:**
```json
{
    "origin": {
        "id": "100",
        "balance": 5
    }
}
```

**Response `404`** — origin account does not exist:
```
0
```

---

#### Transfer

```json
{
    "type": "transfer",
    "origin": "100",
    "destination": "300",
    "amount": 15
}
```

**Response `201`:**
```json
{
    "origin": {
        "id": "100",
        "balance": 0
    },
    "destination": {
        "id": "300",
        "balance": 15
    }
}
```

**Response `404`** — origin account does not exist:
```
0
```

---

## Tests

```bash
docker compose exec app php artisan test
```

Test coverage includes:

- Balance check for existing and non-existing accounts
- Deposit into a new and an existing account
- Withdraw with sufficient and insufficient funds
- Transfer between accounts (creating destination if it does not exist)
- Operations on non-existing accounts
- Verification that GET does not alter state

---

## Technical decisions

### Storage

State is kept using **Laravel Cache with the `array` driver** in tests and the **`file` driver** in local environment. This provides persistence across requests without requiring a database, as specified.

### Architecture

The project follows a **Modular Monolith** pattern with three layers inside the `Banking` module:

| Layer | Responsibility |
|---|---|
| `Core` | Business rules (no framework dependency) |
| `Infrastructure` | Technical implementation (cache, repositories) |
| `UI` | HTTP entry point (controllers, form requests, routes) |

Business logic lives in the `Core` and has no knowledge of Laravel, HTTP, or cache. Any technical detail can be replaced without touching the rules.

### Atomic transfers

`TransferUseCase` performs the withdraw from the origin and the deposit into the destination before persisting either account. If the withdraw fails due to insufficient funds, no state is saved — consistency is guaranteed.
