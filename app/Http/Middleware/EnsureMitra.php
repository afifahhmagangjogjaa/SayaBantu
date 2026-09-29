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

        // For Livewire requests when blocked or inactive: return 403 JSON so frontend catches it and displays modal
        if (in_array($user->status, ['blocked', 'inactive']) && $request->is('livewire*')) {
            return response()->json([
                'status' => $user->status,
                'is_blocked' => ($user->status === 'blocked'),
                'is_inactive' => ($user->status === 'inactive'),
                'message' => $user->status === 'inactive' ? 'Akun Anda telah dinonaktifkan oleh administrator.' : 'Akun Anda telah diblokir. Silakan hubungi admin.'
            ], 403);
        }

        // For regular requests when blocked or inactive: log out immediately
        if (in_array($user->status, ['blocked', 'inactive'])) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => $user->status === 'inactive' ? 'Akun Anda telah dinonaktifkan oleh administrator.' : 'Akun Anda telah diblokir. Silakan hubungi administrator.'
            ]);
        }

        if (!$user->isMitra()) {
            abort(403, 'Unauthorized. Mitra access only.');
        }

        // Halaman yang tetap bisa diakses meski belum terverifikasi (kuota order dibatasi di controller)
        $allowedRoutes = [
            'mitra.dashboard',
            'mitra.profile',
            'mitra.profile.edit',
            'mitra.settings',
            'mitra.settings.notifications',
            'mitra.settings.password',
            'mitra.help-support',
            'profile.settings.verification',
            'mitra.helps.all',
            'mitra.helps.detail',
            'mitra.helps.processing',
            'mitra.helps.completed',
            'mitra.chat',
            'mitra.notifications.index',
            'mitra.ratings',
            'mitra.reports.create',
            'mitra.reports.show',
            'mitra.reports.status-check',
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
