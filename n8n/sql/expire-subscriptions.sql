UPDATE users_plans
SET payment_status = 'vencido'
WHERE payment_status != 'paid'
  AND end_date <= CURRENT_DATE;
