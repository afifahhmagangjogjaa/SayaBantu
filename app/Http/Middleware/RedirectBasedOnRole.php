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
            $user = Auth::user()->fresh() ?? Auth::user();

            // CEK UTAMA: Jika pengguna diblokir, dibanned, atau dinonaktifkan
            // Harus dieksekusi SEBELUM pengecekan rute apapun agar tidak bisa mengakses halaman apapun (termasuk profil/chat)
            $isBannedOrBlocked = $user->isBanned() || in_array($user->status, ['blocked', 'inactive']);

            if ($isBannedOrBlocked) {
                $status = $user->status;
                $isAdmin = in_array($user->role, ['admin', 'super_admin']);

                // Allow GET requests on admin routes so the admin layout can render #admin-blocked-account-modal
                if ($user->role === 'admin' && $request->is('admin*') && $request->isMethod('GET')) {
                    return $next($request);
                }

                // 1. Hapus total seluruh sesi aktif pengguna dari database dan file sesi
                $user->purgeSessions();

                // 2. Putuskan autentikasi dan batalkan sesi saat ini
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = ($status === 'inactive')
                    ? 'Akun Anda telah dinonaktifkan oleh administrator.'
                    : 'Akun Anda telah diblokir. Seluruh sesi login Anda telah dihentikan total.';

                // Untuk request Livewire / AJAX: kembalikan JSON 403
                if ($request->is('livewire*') || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'blocked',
                        'is_blocked' => true,
                        'is_inactive' => ($status === 'inactive'),
                        'should_logout' => true,
                        'message' => $errorMessage,
                    ], 403);
                }

                $referer = $request->headers->get('referer', '');
                $isAdmin = in_array($user->role, ['admin', 'super_admin'])
                    || $request->is('admin*')
                    || $request->is('superadmin*')
                    || str_contains($referer, '/admin')
                    || str_contains($referer, '/superadmin');

                if ($isAdmin) {
                    return redirect(route('admin.login', absolute: false))->withErrors(['email' => $errorMessage]);
                }

                return redirect(route('login', absolute: false))->withErrors(['email' => $errorMessage]);
            }

            // Allow status check, logout, profile, chat, ajax, verification, notifications, password reset, and onboarding endpoints to proceed for any role
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
                'password.*',
                'admin.password.*',
            ]) || $request->is('check-account-status*', 'logout*', 'admin/logout*', 'profile*', 'chat*', 'ajax*', 'notifications*', 'onboarding*', 'email*', 'reset-password*', 'forgot-password*', 'admin/forgot-password*')) {
                return $next($request);
            }

            // Jika belum punya password, jangan redirect otomatis ke dashboard
            if (empty($user->password)) {
                return $next($request);
            }

            // Allow Livewire internal endpoints to pass through without role redirects
            if ($request->is('livewire*')) {
                return $next($request);
            }

            // Redirect mitra to mitra dashboard
            if ($user->role === 'mitra' && !$request->routeIs(['mitra.*', 'profile.*', 'profile', 'chat.*', 'ajax.*', 'notifications.*', 'logout', 'account.status.check'])) {
                return redirect(route('mitra.dashboard', absolute: false));
            }

            // Redirect admin/super_admin based on their role
            if (in_array($user->role, ['admin', 'super_admin']) && !$request->routeIs(['admin.*', 'superadmin.*', 'profile.*', 'profile', 'chat.*', 'ajax.*', 'notifications.*', 'logout', 'account.status.check'])) {
                $dashboardRoute = $user->role === 'super_admin' ? 'superadmin.dashboard' : 'admin.dashboard';
                return redirect(route($dashboardRoute, absolute: false));
            }

            // Allow customer to access dashboard and other authenticated routes
            if ($user->role === 'customer' || $user->role === 'kustomer') {
                return $next($request);
            }
        }

        return $next($request);
    }
}
