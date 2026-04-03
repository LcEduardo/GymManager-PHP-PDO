UPDATE users_plans
SET payment_status = 'paid'
WHERE id = :subscription_id;
