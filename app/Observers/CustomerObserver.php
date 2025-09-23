<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\UtilityCosts\UtilityCostService;

class CustomerObserver
{
    public function created(Customer $customer)
    {
        app(UtilityCostService::class)->createDefaultUtilityCosts($customer->id);
    }
}
