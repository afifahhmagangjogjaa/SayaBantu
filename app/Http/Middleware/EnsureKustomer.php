<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('logout*') || $request->routeIs('logout')) {
            return $next($request);
        }

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        $isBannedOrBlocked = $user->isBanned() || in_array($user->status, ['blocked', 'inactive']);

        // For Livewire requests when blocked or inactive: return 403 JSON so frontend catches it and displays modal
        if ($isBannedOrBlocked && $request->is('livewire*')) {
            $user->purgeSessions();
            return response()->json([
                'status' => $user->status,
                'is_blocked' => true,
                'is_inactive' => ($user->status === 'inactive'),
                'message' => $user->status === 'inactive' ? 'Akun Anda telah dinonaktifkan oleh administrator.' : 'Akun Anda telah diblokir. Silakan hubungi admin.'
            ], 403);
        }

        // For regular requests when blocked or inactive: log out immediately and purge sessions
        if ($isBannedOrBlocked) {
            $user->purgeSessions();
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => $user->status === 'inactive' ? 'Akun Anda telah dinonaktifkan oleh administrator.' : 'Akun Anda telah diblokir. Silakan hubungi administrator.'
            ]);
        }

        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized. Customer access only.');
        }

        return $next($request);
    }
}
