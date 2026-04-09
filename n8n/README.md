# n8n integration

This project uses n8n to keep subscription cycles in sync with the database history.

Each row in `users_plans` represents one billing cycle. A payment should settle the current cycle and queue the next one instead of overwriting the same row.

## Administrative access update

The project now has a dedicated administrator table named `adms` to control who can access the management area.

What was added:

- administrator bootstrap consolidated in `init.sql`
- table `adms` with the columns `id`, `name`, `email`, and `password`
- an initial administrator seed with:
- name: `Administrador Principal`
- email: `admin@gymmanager.local`
- password: `admin123` (change it after the first login in a real environment)
- a login screen shown before the dashboard
- session-based protection so the dashboard and management routes require authenticated admin access

### Password hash strategy

The administrator password seed is now generated in PHP using `PDO::prepare()` and the correct constant `PASSWORD_ARGON2ID`:

```php
$statement = $connection->prepare($sql);
$statement->bindValue(':password', password_hash($password, PASSWORD_ARGON2ID), PDO::PARAM_STR);
$statement->execute();
```

Why `PASSWORD_ARGON2ID` is safer than `PASSWORD_DEFAULT` in this case:

- `PASSWORD_ARGON2ID` explicitly uses Argon2id, which is memory-hard and makes brute-force attacks more expensive
- `PASSWORD_DEFAULT` depends on the PHP version and environment, so the algorithm can vary over time
- in many environments, `PASSWORD_DEFAULT` may still resolve to bcrypt, which is good, but does not add the same memory-cost protection as Argon2id
- by choosing `PASSWORD_ARGON2ID` directly, the project keeps a predictable and stronger password policy for administrator access

Important:

- if the database volume already existed before this change, recreate the PostgreSQL volume or run the `adms` table creation and initial administrator insert that now live in `init.sql`
- use `password_verify()` to validate the stored hashes during login

## Importable workflows

The folder `n8n/workflows` contains ready-to-import workflow JSON files:

- `gymGatewayPaid.json`
- `payment-received-webhook.json`
- `generate-next-cycle-cron.json`
- `expire-subscriptions-cron.json`

After importing:

1. Create a Postgres credential in n8n pointing to the same database used by the app.
2. Open each Postgres node and select that credential.
3. Activate the workflows.

## Suggested workflow structure

### Payment received webhook

- `Webhook`
- `Postgres`
- `Respond to Webhook`

Expected payload example:

```json
{
  "subscription_id": 12
}
```

Behavior:

- updates the informed cycle to `paid`
- calculates the next cycle using the plan duration
- inserts the next cycle as `pending` if it does not exist yet

Default webhook path after activation:

- `POST /webhook/gym/payment-received`

The file `gymGatewayPaid.json` is a practical example of the same payment flow with a manual trigger for testing.

### Generate next cycle daily

- `Schedule Trigger`
- `Postgres`

This workflow is a safety net. It creates missing `pending` cycles for paid subscriptions that still do not have a successor row.

Recommended schedule:

- Every day at `01:00`

### Daily expiration cron

- `Schedule Trigger`
- `Postgres`

This workflow marks overdue pending cycles as `vencido`.

Recommended schedule:

- Every day at `00:05`

## Dashboard behavior

The PHP app now treats statuses consistently:

- `paid`: contributes to monthly revenue
- `pending`: next cycle created and awaiting payment
- `vencido`: pending cycle whose due date has passed

Any update executed by n8n is reflected automatically in:

- dashboard monthly revenue
- dashboard due/expired card
- financial filters
