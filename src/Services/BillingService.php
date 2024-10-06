<?php

namespace App\Services;

use DateTime;
use Exception;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Enums\SubscriptionType;
use App\Services\PrintService;

class BillingService
{
    private array $subscriptions = [];
    private static int $billCounter = 1;
    private PrintService $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    public function hasActiveSubscription(Customer $customer, Product $product): bool
    {
        return $this->findSubscription($customer, $product, true) !== null;
    }

    public function hasExpiredSubscription(Customer $customer, Product $product): ?Subscription
    {
        return $this->findSubscription($customer, $product, false);
    }

    public function subscribe(Customer $customer, Product $product, SubscriptionType $subscriptionType): void
    {
        // Check if customer is already subscribed to the product
        if ($this->hasActiveSubscription($customer, $product)) {
            throw new Exception("\n{$customer->getName()} is already subscribed to {$product->getName()} ({$subscriptionType->toString()})");
        }

        // Check if customer has an expired subscription, then renew it, otherwise create a new subscription
        $subscription = $this->hasExpiredSubscription($customer, $product);

        if ($subscription !== null) {
            $subscription->renewSubscription();
            $bill = $this->issueBill($subscription);
            $this->printService->printBill($bill);
            echo "\n[Renewal] {$customer->getName()} has renewed the subscription to {$product->getName()} ({$subscriptionType->toString()})\n";
            return;
        } else {
            $this->subscriptions[] = new Subscription($customer, $product, $subscriptionType);
        }
    }

    public function renewSubscriptions(): void
    {
        foreach ($this->subscriptions as $subscription) {
            if (new DateTime() >= $subscription->getEndDate()) {
                $this->renewSingleSubscription($subscription);
            }
        }
    }

    public function renewSingleSubscription(Subscription $subscription): void
    {
        $subscription->renewSubscription();
        $bill = $this->issueBill($subscription);
        $this->printService->printBill($bill);
    }

    private function issueBill(Subscription $subscription): Bill
    {
        return new Bill(self::$billCounter++, $subscription);
    }

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }

    private function findSubscription(Customer $customer, Product $product, bool $isActive): ?Subscription
    {
        foreach ($this->subscriptions as $subscription) {
            if ($subscription->getCustomer() === $customer && 
                $subscription->getProduct() === $product) {
                $now = new DateTime();
                if ($isActive && $now < $subscription->getEndDate()) {
                    return $subscription;
                } elseif (!$isActive && $now >= $subscription->getEndDate()) {
                    return $subscription;
                }
            }
        }
        return null;
    }
}
