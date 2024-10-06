<?php

namespace App\Enums;

enum SubscriptionType: string {
    case Monthly = 'Monthly';
    case Annual = 'Annual';

    public function toString(): string {
        return $this->value;
    }
}