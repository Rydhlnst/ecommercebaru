<?php

namespace Beres\Permission\Policies;

use Beres\Permission\Services\PermissionService;
use Webkul\User\Models\Admin;

class OrderPolicy
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Determine whether the user can view the order.
     */
    public function view(Admin $user): bool
    {
        return $this->permissionService->hasPermission($user, 'orders.view');
    }

    /**
     * Determine whether the user can create an order.
     */
    public function create(Admin $user): bool
    {
        return $this->permissionService->hasPermission($user, 'orders.create');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(Admin $user, $order): bool
    {
        return $this->permissionService->hasPermission($user, 'orders.update');
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(Admin $user, $order): bool
    {
        return $this->permissionService->hasPermission($user, 'orders.delete');
    }

    /**
     * Determine whether the user can update order status.
     */
    public function updateStatus(Admin $user, $order): bool
    {
        return $this->permissionService->hasPermission($user, 'orders.update_status');
    }
}
