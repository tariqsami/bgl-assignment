<?php

use PHPUnit\Framework\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Enums\SubscriptionType;
use App\Services\BillingService;
use App\Services\PrintService;

class BillingServiceTest extends TestCase
{
    private BillingService $billingService;
    private Customer $customer;
    private Product $product;
    private SubscriptionType $subscriptionType;

    // Initialize the service before each test
    protected function setUp(): void
    {
        $this->billingService = new BillingService($this->createMock(PrintService::class));
        $this->customer = new Customer(1, 'Adam');
        $this->product = new Product(1, 'Product A', 100.0);
        $this->subscriptionType = SubscriptionType::Monthly;
    }

    protected function tearDown(): void
    {
        unset($this->billingService);
        unset($this->customer);
        unset($this->product);
        unset($this->subscriptionType);
    }

    public function testCanSubscribeCustomerToProduct(): void
    {
        $this->billingService->subscribe($this->customer, $this->product, $this->subscriptionType);

        $this->assertTrue($this->billingService->hasActiveSubscription($this->customer, $this->product));
    }

    public function testCannotSubscribeWhenCustomerHasActiveSubscription(): void
    {
        // Subscribe once
        $this->billingService->subscribe($this->customer, $this->product, $this->subscriptionType);

        // Expect exception when trying to subscribe again with an active subscription
        $this->expectException(Exception::class);

        // Attempt to subscribe again
        $this->billingService->subscribe($this->customer, $this->product, $this->subscriptionType);
    }

    public function testCanRenewExpiredSubscription(): void
    {
        // Subscribe first
        $this->billingService->subscribe($this->customer, $this->product, $this->subscriptionType);

        // Simulate an expired subscription by directly setting the end date to a past date
        $this->expireSubscription($this->billingService->getSubscriptions()[0]);

        // Renew the subscription
        $this->billingService->renewSingleSubscription($this->billingService->getSubscriptions()[0]);

        // Assert that the subscription is now active
        $this->assertTrue($this->billingService->hasActiveSubscription($this->customer, $this->product));
    }

    public function testRenewAllExpiredSubscriptions(): void
    {
        // Subscribe first
        $this->billingService->subscribe($this->customer, $this->product, $this->subscriptionType);

        // Simulate an expired subscription
        $this->expireSubscription($this->billingService->getSubscriptions()[0]);

        // Renew all subscriptions
        $this->billingService->renewSubscriptions();

        // Assert that the subscription is now active
        $this->assertTrue($this->billingService->hasActiveSubscription($this->customer, $this->product));
    }

    // Helper function to simulate expiration of a subscription
    private function expireSubscription(Subscription $subscription): void
    {
        $subscription->setEndDate(new DateTime('yesterday'));
    }
}
