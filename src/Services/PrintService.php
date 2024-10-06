<?php

namespace App\Services;

use App\Models\Bill as Bill;
use App\Models\Subscription;

class PrintService
{
    public function printBill(Bill $bill): void
    {
        echo "Bill ID: {$bill->getId()} \n";
        echo "Customer: {$bill->getSubscription()->getCustomer()->getName()}\n";
        echo "Product:{$bill->getSubscription()->getProduct()->getName()}\n";
        echo "Subscription Type: {$bill->getSubscription()->getSubscriptionType()->toString()}\n";
        echo "Issue Date: {$bill->getIssueDate()->format('Y-m-d')}\n";
        echo "\n";
    }

    public function printSubscription(Subscription $subscription): void
    {
        echo "Customer: {$subscription->getCustomer()->getName()}\n";
        echo "Product: {$subscription->getProduct()->getName()}\n";
        echo "Subscription Type: {$subscription->getSubscriptionType()->toString()}\n";
        echo "Start Date: {$subscription->getStartDate()->format('Y-m-d')}\n";
        echo "End Date: {$subscription->getEndDate()->format('Y-m-d')}\n";
    }

    public function printError(string $errorMessage): void
    {
        echo "\n---------------------------------------\n";
        echo "Error: $errorMessage\n";
        echo "\n---------------------------------------\n";
    }
}