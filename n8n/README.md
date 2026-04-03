# n8n integration

This project uses n8n to keep subscription cycles in sync with the database history.

Each row in `users_plans` represents one billing cycle. A payment should settle the current cycle and queue the next one instead of overwriting the same row.

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
