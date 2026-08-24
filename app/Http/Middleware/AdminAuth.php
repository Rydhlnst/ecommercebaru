<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = auth()->guard('web');

        if (! $request->hasSession() || ! $guard->check()) {
            return redirect()->route('admin.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! $guard->user()?->is_admin) {
            $guard->logout();

            return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki akses admin.');
        }

        return $next($request);
    }
}
