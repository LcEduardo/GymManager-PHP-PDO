<?php

namespace App\Database;

class UserSubscription 
{
    private ?int $id;
    private int $user_id;
    private int $plan_id;
    private string $start_date;
    private string $end_date;
    private string $payment_status;
    
    public function __construct(?int $id, int $user_id, int $plan_id, string $start_date, string $end_date, string $payment_status)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->plan_id = $plan_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->payment_status = $payment_status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getPlanId(): int
    {
        return $this->plan_id;
    }

    public function getStartDate(): string
    {
        return $this->start_date;
    }

    // here it could have setters to start date and end date

    public function getEndDate(): string
    {
        return $this->end_date;
    }

    public function getPaymentStatus(): string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus($payment_status): void {
        $this->payment_status = $payment_status;
    }
}