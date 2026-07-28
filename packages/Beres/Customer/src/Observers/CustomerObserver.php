<?php

namespace Beres\Customer\Observers;

use Beres\Customer\Models\CustomerActivityLog;
use Webkul\Customer\Models\Customer;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        CustomerActivityLog::log('created', $customer, 'Customer account created');
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        $changes = $customer->getChanges();
        unset($changes['updated_at']);

        if (!empty($changes)) {
            CustomerActivityLog::log(
                'updated',
                $customer,
                'Customer profile updated',
                $changes,
                $customer->toArray()
            );
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        CustomerActivityLog::log('deleted', $customer, 'Customer account deleted');
    }
}
