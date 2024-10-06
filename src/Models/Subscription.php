<?php

namespace App\Models;

use DateTime;
use App\Enums\SubscriptionType;
use InvalidArgumentException;

class Subscription
{
    private DateTime $startDate;
    private DateTime $endDate;

    public function __construct(
        private Customer $customer,
        private Product $product,
        private SubscriptionType $subscriptionType
    ) {
        $this->startDate = new DateTime();
        $this->endDate = $this->calculateEndDate();
    }

    private function calculateEndDate(): DateTime
    {
        $endDate = clone $this->startDate;

        return match ($this->subscriptionType) {
            SubscriptionType::Monthly => $endDate->modify('+1 month'),
            SubscriptionType::Annual => $endDate->modify('+1 year'),
            default => throw new InvalidArgumentException("Invalid subscription type"),
        };
    }

    public function renewSubscription(): void
    {
        $this->startDate = new DateTime();
        $this->endDate = $this->calculateEndDate();
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $date): void
    {
        $this->endDate = $date;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getSubscriptionType(): SubscriptionType
    {
        return $this->subscriptionType;
    }
}
