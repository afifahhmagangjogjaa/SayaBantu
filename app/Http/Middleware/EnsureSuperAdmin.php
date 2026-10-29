<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect(route('login', absolute: false));
        }

        $user = auth()->user()->fresh() ?? auth()->user();

        if (! $user->isSuperAdmin()) {
            if ($user->role === 'mitra') {
                return redirect()->route('mitra.dashboard');
            } elseif (in_array($user->role, ['customer', 'kustomer'])) {
                return redirect()->route('customer.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            abort(403, 'Unauthorized. Super Admin access only.');
        }

        return $next($request);
    }
}
