<?php

namespace App\Providers;

use App\Models\Help;
use App\Models\Rating;
use App\Models\User;
use App\Observers\HelpObserver;
use App\Observers\RatingObserver;
use App\Observers\UserObserver;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Redirector $redirect): void
    {
        // Register Help Observer
        Help::observe(HelpObserver::class);

        // Register User & Rating observers for activity logging
        User::observe(UserObserver::class);
        Rating::observe(RatingObserver::class);

        // Register BalanceTransaction observer to update user balances when transactions complete
        \App\Models\BalanceTransaction::observe(\App\Observers\BalanceTransactionObserver::class);

        // Redirect authenticated users based on their role
        $this->configureRedirectsForAuthentication();
    }

    /**
     * Configure redirects after authentication
     */
    private function configureRedirectsForAuthentication(): void
    {
        $this->app['redirect']->macro('intended', function ($default = '/', $status = 302, $headers = [], $secure = null) {
            $intended = session()->pull('url.intended');

            if ($intended && Auth::check()) {
                $user = Auth::user();
                $path = parse_url($intended, PHP_URL_PATH) ?? '';
                $valid = true;

                if (in_array($user->role, ['customer', 'kustomer'])) {
                    if (str_starts_with($path, '/admin') || str_starts_with($path, '/superadmin') || str_starts_with($path, '/mitra')) {
                        $valid = false;
                    }
                } elseif ($user->role === 'mitra') {
                    if (str_starts_with($path, '/admin') || str_starts_with($path, '/superadmin')) {
                        $valid = false;
                    }
                } elseif ($user->role === 'admin') {
                    if (str_starts_with($path, '/superadmin')) {
                        $valid = false;
                    }
                }

                if ($valid) {
                    return redirect()->to($intended, $status, $headers, $secure);
                }
            } elseif ($intended && ! Auth::check()) {
                return redirect()->to($intended, $status, $headers, $secure);
            }

            // Check user role and redirect accordingly
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'mitra') {
                    return redirect()->to(route('mitra.dashboard'), $status, $headers, $secure);
                } elseif (in_array($user->role, ['admin', 'super_admin'])) {
                    return redirect()->to(
                        $user->role === 'super_admin' ? route('superadmin.dashboard') : route('admin.dashboard'),
                        $status,
                        $headers,
                        $secure
                    );
                } elseif (in_array($user->role, ['customer', 'kustomer'])) {
                    return redirect()->to(route('customer.dashboard'), $status, $headers, $secure);
                }
            }

            return redirect()->to($default, $status, $headers, $secure);
        });
    }
}
