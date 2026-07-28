<?php

namespace Beres\Permission\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Permission\Services\PermissionService;
use Webkul\User\Models\Admin;
use Webkul\User\Models\Role;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Display permissions matrix.
     */
    public function index()
    {
        $roles = $this->permissionService->getRoles();
        $permissions = $this->permissionService->getPermissions();

        return view('beres-permission::permissions.index', [
            'roles'       => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Get user permissions.
     */
    public function userPermissions($userId)
    {
        $user = Admin::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $permissions = $this->permissionService->getUserPermissions($user);

        return response()->json([
            'success' => true,
            'data'    => $permissions,
        ]);
    }

    /**
     * Check if user has permission.
     */
    public function checkPermission(Request $request)
    {
        $request->validate([
            'permission' => 'required|string',
        ]);

        $user = auth()->guard('admin')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $hasPermission = $this->permissionService->hasPermission(
            $user,
            $request->input('permission')
        );

        return response()->json([
            'success' => true,
            'has_permission' => $hasPermission,
        ]);
    }

    /**
     * Get current user permissions.
     */
    public function myPermissions()
    {
        $user = auth()->guard('admin')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $permissions = $this->permissionService->getUserPermissions($user);

        return response()->json([
            'success' => true,
            'data'    => $permissions,
        ]);
    }
}
