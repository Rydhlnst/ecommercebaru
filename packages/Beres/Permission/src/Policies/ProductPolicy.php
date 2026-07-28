<?php

namespace Beres\Permission\Policies;

use Beres\Permission\Services\PermissionService;
use Webkul\User\Models\Admin;

class ProductPolicy
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Determine whether the user can view the product.
     */
    public function view(Admin $user): bool
    {
        return $this->permissionService->hasPermission($user, 'products.view');
    }

    /**
     * Determine whether the user can create a product.
     */
    public function create(Admin $user): bool
    {
        return $this->permissionService->hasPermission($user, 'products.create');
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(Admin $user, $product): bool
    {
        return $this->permissionService->hasPermission($user, 'products.update');
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(Admin $user, $product): bool
    {
        return $this->permissionService->hasPermission($user, 'products.delete');
    }
}
