<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        // Add global middleware for role-based redirects
        $middleware->web(append: [
            \App\Http\Middleware\RedirectBasedOnRole::class,
        ]);

        // Exempt logout and sanction acknowledge routes from CSRF verification
        // to prevent 419 error when deactivated/blocked accounts click logout
        $middleware->validateCsrfTokens(except: [
            'logout',
            'logout/*',
            'admin/logout',
            'admin/logout/*',
            'notifications/sanction/*/acknowledge',
        ]);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            $referer = $request->headers->get('referer', '');
            if ($request->is('admin*') || $request->is('superadmin*') || str_contains($referer, '/admin') || str_contains($referer, '/superadmin')) {
                return route('admin.login', absolute: false);
            }
            return route('login', absolute: false);
        });

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->role === 'super_admin') {
                    return route('superadmin.dashboard', absolute: false);
                } elseif ($user->role === 'admin') {
                    return route('admin.dashboard', absolute: false);
                } elseif ($user->role === 'mitra') {
                    return route('mitra.dashboard', absolute: false);
                }
                return route('customer.dashboard', absolute: false);
            }
            return route('login', absolute: false);
        });

        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'admin'       => \App\Http\Middleware\EnsureAdmin::class,
            'kustomer'    => \App\Http\Middleware\EnsureKustomer::class,
            // New alias: use 'customer' everywhere going forward
            'customer'    => \App\Http\Middleware\EnsureCustomer::class,
            'mitra'       => \App\Http\Middleware\EnsureMitra::class,
            
            // Tambahkan alias ini di sini:
            'onboarded'   => \App\Http\Middleware\EnsureOnboardingCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Gracefully handle 419 Page Expired (TokenMismatchException) by logging out and redirecting to login
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $referer = $request->headers->get('referer', '');
            $isAdmin = $request->is('admin*')
                || $request->is('superadmin*')
                || str_contains($referer, '/admin')
                || str_contains($referer, '/superadmin')
                || (auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'super_admin']));

            if (\Illuminate\Support\Facades\Auth::check()) {
                \Illuminate\Support\Facades\Auth::logout();
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($isAdmin) {
                return redirect(route('admin.login', absolute: false))->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            return redirect(route('login', absolute: false))->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        });

        // Gracefully handle 419 HttpException to prevent 419 error page from ever showing
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419) {
                $referer = $request->headers->get('referer', '');
                $isAdmin = $request->is('admin*')
                    || $request->is('superadmin*')
                    || str_contains($referer, '/admin')
                    || str_contains($referer, '/superadmin')
                    || (auth()->check() && in_array(auth()->user()->role ?? '', ['admin', 'super_admin']));

                if (\Illuminate\Support\Facades\Auth::check()) {
                    \Illuminate\Support\Facades\Auth::logout();
                }
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($isAdmin) {
                    return redirect(route('admin.login', absolute: false))->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
                }

                return redirect(route('login', absolute: false))->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }
        });
    })->create();