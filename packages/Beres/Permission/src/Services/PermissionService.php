<?php

namespace Beres\Permission\Services;

use Webkul\User\Models\Role;
use Webkul\User\Models\Admin;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    /**
     * Get all defined permissions.
     */
    public function getPermissions(): array
    {
        return config('beres-permission.permissions', []);
    }

    /**
     * Get all defined roles.
     */
    public function getRoles(): array
    {
        return config('beres-permission.roles', []);
    }

    /**
     * Get permissions for a role.
     */
    public function getRolePermissions(string $role): array
    {
        $roles = $this->getRoles();

        return $roles[$role]['permissions'] ?? [];
    }

    /**
     * Check if user has permission.
     */
    public function hasPermission(Admin $user, string $permission): bool
    {
        $role = $user->role;

        if (!$role) {
            return false;
        }

        $rolePermissions = $this->getRolePermissions($role->name);

        // Owner has all permissions
        if (in_array('*', $rolePermissions)) {
            return true;
        }

        // Check exact permission
        if (in_array($permission, $rolePermissions)) {
            return true;
        }

        // Check wildcard permission (e.g., 'products.*')
        $parts = explode('.', $permission);
        $wildcard = $parts[0] . '.*';

        if (in_array($wildcard, $rolePermissions)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(Admin $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all given permissions.
     */
    public function hasAllPermissions(Admin $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($user, $permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get permission groups.
     */
    public function getPermissionGroups(): array
    {
        $permissions = $this->getPermissions();
        $groups = [];

        foreach ($permissions as $group => $items) {
            $groups[$group] = [
                'name'        => ucfirst(str_replace('_', ' ', $group)),
                'permissions' => $items,
            ];
        }

        return $groups;
    }

    /**
     * Seed default roles.
     */
    public function seedRoles(): void
    {
        $roles = $this->getRoles();

        foreach ($roles as $slug => $roleData) {
            Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $roleData['name'],
                    'description' => $roleData['description'],
                ]
            );
        }
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Admin $user, string $roleSlug): bool
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return false;
        }

        return $user->update(['role_id' => $role->id]);
    }

    /**
     * Get user's effective permissions.
     */
    public function getUserPermissions(Admin $user): array
    {
        $role = $user->role;

        if (!$role) {
            return [];
        }

        return $this->getRolePermissions($role->name);
    }
}
