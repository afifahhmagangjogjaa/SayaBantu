<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow logout requests to proceed
        if ($request->is('admin/logout*') || $request->routeIs('admin.logout')) {
            return $next($request);
        }

        // If not authenticated, redirect to admin login
        if (!auth()->check()) {
            return redirect(route('admin.login', absolute: false));
        }

        $user = auth()->user()->fresh() ?? auth()->user();

        // Only for admin role (not super_admin)
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access only.');
        }

        // Check if admin account is inactive or blocked/banned
        $isBannedOrBlocked = $user->isBanned() || in_array($user->status, ['inactive', 'blocked']);
        if ($isBannedOrBlocked) {
            // For Livewire or AJAX requests: return 403 JSON so frontend catches it and displays modal
            if ($request->is('livewire*') || $request->ajax() || $request->wantsJson()) {
                $user->purgeSessions();
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'status' => $user->status,
                    'is_blocked' => true,
                    'is_inactive' => ($user->status === 'inactive'),
                    'should_logout' => true,
                    'message' => $user->status === 'inactive' 
                        ? 'Akun Admin Anda telah dinonaktifkan oleh Super Admin.' 
                        : 'Akun Admin Anda telah diblokir oleh Super Admin.'
                ], 403);
            }

            // For regular GET requests: allow page to render with the modal automatically open (#admin-blocked-account-modal)
            // The modal completely blocks the screen with backdrop-blur and has an 'OK, Keluar' button to logout
            if ($request->isMethod('GET')) {
                return $next($request);
            }

            // For non-GET requests (e.g. form POST): purge session and redirect to login
            $user->purgeSessions();
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $msg = $user->status === 'inactive'
                ? 'Akun Admin Anda telah dinonaktifkan oleh Super Admin. Silakan hubungi Super Admin untuk informasi lebih lanjut.'
                : 'Akun Admin Anda telah diblokir oleh Super Admin. Seluruh sesi login telah dihapus secara total.';

            return redirect(route('admin.login', absolute: false))->withErrors(['email' => $msg]);
        }

        return $next($request);
    }
}
