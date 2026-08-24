<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webkul\Core\Http\Middleware\SecureHeaders;
use Webkul\Installer\Http\Middleware\CanInstall;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => AdminAuth::class,
        ]);
        /**
         * Remove the default Laravel middleware that prevents requests during maintenance mode. There are three
         * middlewares in the shop that need to be loaded before this middleware. Therefore, we need to remove this
         * middleware from the list and add the overridden middleware at the end of the list.
         *
         * As of now, this has been added in the Admin and Shop providers. I will look for a better approach in Laravel 11 for this.
         */
        $middleware->remove(PreventRequestsDuringMaintenance::class);

        /**
         * Remove the default Laravel middleware that converts empty strings to null. First, handle all nullable cases,
         * then remove this line.
         */
        $middleware->remove(ConvertEmptyStringsToNull::class);

        $middleware->append(SecureHeaders::class);
        $middleware->append(CanInstall::class);

        /**
         * Add the overridden middleware at the end of the list.
         */
        $middleware->replaceInGroup('web', BaseEncryptCookies::class, EncryptCookies::class);

        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'deploy/*',
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withSchedule(function (Schedule $schedule) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Graceful error handling: tidak ada lagi halaman debug Laravel.
         *
         * - Validation di admin  -> redirect back + toast error pertama + error field tetap terisi
         * - Write action di admin (POST/PUT/DELETE) -> redirect back + toast error
         * - GET di admin -> halaman error custom (resources/views/errors) dengan detail
         * - Storefront -> halaman error custom (APP_DEBUG=false di production)
         *
         * Semua exception tetap di-log ke laravel.log.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            $isAdmin = $request->is('admin') || $request->is('admin/*');
            $isWrite = ! in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS']);

            if ($e instanceof ValidationException) {
                if ($isAdmin && ! $request->expectsJson()) {
                    $first = collect($e->errors())->flatten()->first();

                    return redirect()->back()
                        ->withInput()
                        ->withErrors($e->errors())
                        ->with('error', $first ?: 'Periksa kembali isian formulir.');
                }

                return null;
            }

            if ($e instanceof AuthenticationException || $e instanceof HttpException) {
                return null;
            }

            report($e);

            $message = mb_substr($e->getMessage(), 0, 300);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan: '.$message], 500);
            }

            if ($isAdmin && $isWrite) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan: '.$message);
            }

            $code = (int) ($e->getCode() ?: 500);
            if ($code < 400 || $code > 599) {
                $code = 500;
            }

            if (view()->exists("errors.$code")) {
                return response()->view("errors.$code", [
                    'detail' => $isAdmin ? $message : null,
                ], $code);
            }

            return response()->view('errors.500', [
                'detail' => $isAdmin ? $message : null,
            ], 500);
        });
    })->create();
