<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Allow status check, logout, profile, chat, ajax, verification, notifications, and onboarding endpoints to proceed for any role
            if ($request->routeIs([
                'account.status.check',
                'logout',
                'profile.*',
                'profile',
                'chat.*',
                'ajax.*',
                'notifications.*',
                'onboarding.*',
                'verification.*',
                'register.*',
                'register',
                'login',
            ]) || $request->is('check-account-status*', 'logout*', 'profile*', 'chat*', 'ajax*', 'notifications*', 'onboarding*', 'email*')) {
                return $next($request);
            }

            // Jika belum punya password, jangan redirect otomatis ke dashboard
            if (empty($user->password)) {
                return $next($request);
            }

            // For Livewire requests when blocked or inactive: return 403 JSON so frontend catches it and displays modal
            if (in_array($user->status, ['blocked', 'inactive']) && $request->is('livewire*')) {
                return response()->json([
                    'status' => $user->status,
                    'is_blocked' => ($user->status === 'blocked'),
                    'is_inactive' => ($user->status === 'inactive'),
                    'message' => $user->status === 'inactive' ? 'Akun Anda telah dinonaktifkan oleh super admin.' : 'Akun Anda telah diblokir. Silakan hubungi super admin.'
                ], 403);
            }

            // For regular requests when blocked or inactive (mitra/customer): log out immediately
            if (in_array($user->status, ['blocked', 'inactive']) && in_array($user->role, ['mitra', 'customer', 'kustomer'])) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->withErrors([
                    'email' => $user->status === 'inactive' ? 'Akun Anda telah dinonaktifkan oleh administrator.' : 'Akun Anda telah diblokir. Silakan hubungi administrator.'
                ]);
            }

            // Allow Livewire internal endpoints to pass through without role redirects
            if ($request->is('livewire*')) {
                return $next($request);
            }

            // Redirect mitra to mitra dashboard
            if ($user->role === 'mitra' && !$request->routeIs(['mitra.*', 'profile.*', 'profile', 'chat.*', 'ajax.*', 'notifications.*', 'logout', 'account.status.check'])) {
                return redirect()->route('mitra.dashboard');
            }

            // Redirect admin/super_admin based on their role
            if (in_array($user->role, ['admin', 'super_admin']) && !$request->routeIs(['admin.*', 'superadmin.*', 'profile.*', 'profile', 'chat.*', 'ajax.*', 'notifications.*', 'logout', 'account.status.check'])) {
                $dashboardRoute = $user->role === 'super_admin' ? 'superadmin.dashboard' : 'admin.dashboard';
                return redirect()->route($dashboardRoute);
            }

            // Allow customer to access dashboard and other authenticated routes
            if ($user->role === 'customer' || $user->role === 'kustomer') {
                return $next($request);
            }
        }

        return $next($request);
    }
}
