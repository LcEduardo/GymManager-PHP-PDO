# n8n integration

This project uses n8n for two database automation flows:

1. Payment webhook
   Receives a payment confirmation payload and updates `users_plans.payment_status` to `paid`.
2. Daily expiration cron
   Runs once a day and updates `users_plans.payment_status` to `vencido` when `end_date <= CURRENT_DATE` and the row is not paid.

## Importable workflows

The folder `n8n/workflows` contains ready-to-import workflow JSON files:

- `payment-received-webhook.json`
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
  "subscription_id": 12,
  "payment_status": "paid"
}
```

Use the SQL from `n8n/sql/payment-received.sql`.

Default webhook path after activation:

- `POST /webhook/gym/payment-received`

### Daily expiration cron

- `Schedule Trigger`
- `Postgres`

Use the SQL from `n8n/sql/expire-subscriptions.sql`.

Recommended schedule:

- Every day at `00:05`

## Dashboard behavior

The PHP app now treats statuses consistently:

- `paid`: contributes to monthly revenue
- `pending`: active unpaid subscription
- `vencido`: expired unpaid subscription

Any update executed by n8n is reflected automatically in:

- dashboard monthly revenue
- dashboard due/expired card
- financial filters
