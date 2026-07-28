<?php

namespace Beres\Permission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Beres\Permission\Services\PermissionService;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return redirect()->route('admin.session.create');
        }

        if (!$this->permissionService->hasPermission($user, $permission)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
