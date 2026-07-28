<?php

namespace Beres\Permission\Policies;

use Beres\Permission\Services\PermissionService;
use Webkul\User\Models\Admin;

class CustomerPolicy
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Determine whether the user can view the customer.
     */
    public function view(Admin $user): bool
    {
        return $this->permissionService->hasPermission($user, 'customers.view');
    }

    /**
     * Determine whether the user can create a customer.
     */
    public function create(Admin $user): bool
    {
        return $this->permissionService->hasPermission($user, 'customers.create');
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(Admin $user, $customer): bool
    {
        return $this->permissionService->hasPermission($user, 'customers.update');
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(Admin $user, $customer): bool
    {
        return $this->permissionService->hasPermission($user, 'customers.delete');
    }
}
