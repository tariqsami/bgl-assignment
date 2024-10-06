<?php
require './vendor/autoload.php';

use App\Models\Bill as Bill;
use App\Models\Customer as Customer;
use App\Models\Product as Product;
use App\Enums\SubscriptionType as SubscriptionType;
use App\Services\BillingService as BillingService;
use App\Services\PrintService as PrintService;

// Mock data and test cases
if (php_sapi_name() === 'cli') {



    $billingService = new BillingService(new PrintService());

    echo "\nCreating customers and products...\n";

    $customer1 = new Customer(1, "Adam");
    $customer2   = new Customer(2, "Michael");

    // Creating product objects
    $productA = new Product(1, "Product A", 10.0);
    $productB = new Product(2, "Product B", 100.0);
    
    // Creating subscriptions
    echo "\nCreating subscriptions...\n";

    try {
        $billingService->subscribe($customer1, $productA, SubscriptionType::Monthly);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $billingService->subscribe($customer2, $productB, SubscriptionType::Annual);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    // creating a subscription for a customer who already has an active subscription
    echo "\nCreating a subscription for a customer who already has an active subscription...\n";
    try {
        $billingService->subscribe($customer2, $productB, SubscriptionType::Annual);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    // Simulating time passage for testing renewals
    echo "\n--- Simulating subscription renewals ---\n";
    $billingService->renewSubscriptions(); // No bills should be issued yet

    // Manually set end dates for testing (to simulate expiration)
    $subscriptions = $billingService->getSubscriptions();

    $subscriptions[0]->setEndDate((new DateTime())->modify('-1 day')); // Alice's subscription is overdue
    $subscriptions[1]->setEndDate((new DateTime())->modify('-1 day')); // Bob's subscription is overdue

    // try to create a new subscription for a customer who has an expired subscription, it will renew the subscription
    try {
        $billingService->subscribe($customer2, $productB, SubscriptionType::Annual);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    // Should issue bills for both
    $billingService->renewSubscriptions(); 
}
