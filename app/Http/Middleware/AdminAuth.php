<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->guard('web')->check()) {
            return redirect()->route('admin.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! auth()->guard('web')->user()->is_admin) {
            auth()->guard('web')->logout();

            return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki akses admin.');
        }

        return $next($request);
    }
}
