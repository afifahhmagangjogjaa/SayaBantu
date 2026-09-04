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

            // Allow status check, logout, profile, chat, ajax, verification, and onboarding endpoints to proceed for any role
            if ($request->routeIs([
                'account.status.check',
                'logout',
                'profile.*',
                'profile',
                'chat.*',
                'ajax.*',
                'onboarding.*',
                'verification.*',
                'register.*',
                'register',
                'login',
            ]) || $request->is('check-account-status*', 'logout*', 'profile*', 'chat*', 'ajax*', 'onboarding*', 'email*')) {
                return $next($request);
            }

            // Jika belum verifikasi email atau belum selesai onboarding (password / biodata / dokumen), jangan redirect otomatis ke dashboard
            if (!$user->hasVerifiedEmail() || empty($user->password) || !$user->is_completed) {
                return $next($request);
            }

            // For Livewire requests when blocked: return 403 JSON so frontend catches it and displays modal
            if ($user->status === 'blocked' && $request->is('livewire*')) {
                return response()->json([
                    'is_blocked' => true,
                    'message' => 'Akun Anda telah diblokir. Silakan hubungi super admin.'
                ], 403);
            }

            // Allow Livewire internal endpoints to pass through without role redirects
            if ($request->is('livewire*')) {
                return $next($request);
            }

            // Redirect mitra to mitra dashboard
            if ($user->role === 'mitra' && !$request->routeIs(['mitra.*', 'profile.*', 'profile', 'chat.*', 'ajax.*', 'logout', 'account.status.check'])) {
                return redirect()->route('mitra.dashboard');
            }

            // Redirect admin/super_admin based on their role
            if (in_array($user->role, ['admin', 'super_admin']) && !$request->routeIs(['admin.*', 'superadmin.*', 'profile.*', 'profile', 'chat.*', 'ajax.*', 'logout', 'account.status.check'])) {
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
