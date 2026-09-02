<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMitra
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

        if (!$user->isMitra()) {
            abort(403, 'Unauthorized. Mitra access only.');
        }

        // Halaman yang tetap bisa diakses meski belum terverifikasi
        $allowedRoutes = [
            'mitra.dashboard',
            'mitra.profile',
            'mitra.profile.edit',
            'mitra.settings',
            'mitra.settings.notifications',
            'mitra.settings.password',
            'mitra.help-support',
            'profile.settings.verification',
        ];

        // Jika mitra belum terverifikasi dan mencoba akses halaman yang dibatasi,
        // arahkan ke halaman edit profil untuk upload KTP
        if (!$user->verified && !in_array($request->route()?->getName(), $allowedRoutes)) {
            // Biarkan Livewire internal requests lewat agar tidak break UI
            if ($request->is('livewire*')) {
                return $next($request);
            }

            session()->flash('error', 'Akun Anda belum terverifikasi. Silakan upload foto KTP dan Selfie terlebih dahulu, lalu tunggu persetujuan admin.');
            return redirect()->route('mitra.profile.edit');
        }

        return $next($request);
    }
}
