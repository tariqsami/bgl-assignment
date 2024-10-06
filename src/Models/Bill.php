<?php

namespace App\Models;

use DateTime;
use App\Models\Subscription;

class Bill
{
    private DateTime $issueDate;

    public function __construct(
        private int $id,
        private Subscription $subscription
    ) {
        $this->issueDate = new DateTime();
    }

    public function getId(): int 
    {
        return $this->id;
    }

    public function getIssueDate(): DateTime
    {
        return $this->issueDate;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }
}
