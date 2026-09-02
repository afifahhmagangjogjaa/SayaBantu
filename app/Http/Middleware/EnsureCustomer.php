<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // For Livewire requests when blocked: return 403 JSON so frontend catches it and displays modal
        if ($user->status === 'blocked' && $request->is('livewire*')) {
            return response()->json([
                'is_blocked' => true,
                'message' => 'Akun Anda telah diblokir. Silakan hubungi admin.'
            ], 403);
        }

        if (!$user->isCustomer()) {
            abort(403, 'Unauthorized. Customer access only.');
        }

        return $next($request);
    }
}
