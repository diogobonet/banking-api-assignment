# EBANX Take-Home Assignment

A simple banking API with deposit, withdraw and transfer operations. No database, the state lives in memory.

## Requirements

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Running the project

```bash
docker compose up -d --build
```

The API will be available at `http://localhost:8000`.

Other useful commands:

```bash
docker compose logs -f app          # view logs
docker compose down                 # stop the container
docker compose exec app php artisan test  # run tests
docker compose exec app bash        # access the shell
```

## Endpoints

### POST /reset

Clears all state. Useful for resetting between test runs.

```
200 OK
```

### GET /balance

Returns the balance for a given account. Returns `0` with status `404` if the account doesn't exist.

```
GET /balance?account_id=100
```

### POST /event

Handles three types of operations based on the `type` field.

**Deposit** — creates the account if it doesn't exist yet:
```json
{ "type": "deposit", "destination": "100", "amount": 10 }
```
```json
201 { "destination": { "id": "100", "balance": 10 } }
```

**Withdraw** — returns `404` if the account doesn't exist:
```json
{ "type": "withdraw", "origin": "100", "amount": 5 }
```
```json
201 { "origin": { "id": "100", "balance": 5 } }
```

**Transfer** — creates destination if it doesn't exist, returns `404` if origin doesn't exist:
```json
{ "type": "transfer", "origin": "100", "destination": "300", "amount": 15 }
```
```json
201 { "origin": { "id": "100", "balance": 0 }, "destination": { "id": "300", "balance": 15 } }
```

## Running the tests

```bash
docker compose exec app php artisan test
```

The test suite covers the main flows (deposit, withdraw, transfer), error cases like non-existing accounts and insufficient funds, and verifies that GET requests don't change any state.

## Technical decisions

The project is structured as a Modular Monolith with the `Banking` module split into three layers: `Core` for business rules, `Infrastructure` for the cache-based repository, and `UI` for the HTTP layer. The `Core` has no dependency on Laravel, it only knows about domain concepts.

Since no database is required, state is stored via Laravel's cache (file driver in local, array driver in tests). Transfers are handled atomically: the withdraw and deposit both happen before anything is persisted, so if the origin has insufficient funds, neither account is updated.
