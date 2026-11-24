<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tamu (belum login) yang membuka halaman ber-middleware 'auth' → ke form login admin.
        // (Di Laravel 10 diatur di app/Http/Middleware/Authenticate.php.)
        $middleware->redirectGuestsTo(fn () => route('backend.login'));

        // Admin yang SUDAH login lalu membuka halaman ber-middleware 'guest' (form login) → ke dashboard.
        // (Di Laravel 10 diatur di app/Http/Middleware/RedirectIfAuthenticated.php.)
        $middleware->redirectUsersTo(fn () => route('backend.beranda'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
