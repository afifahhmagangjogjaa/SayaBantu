<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasVerifiedEmail()) {
            // Admin dan Super Admin selalu diizinkan langsung
            if ($user->isSuperAdmin() || $user->isAdmin()) {
                return $next($request);
            }

            // Jika belum selesai onboarding dan bukan rute onboarding atau logout
            if (!$user->is_completed && !$request->routeIs('onboarding.*') && !$request->routeIs('logout')) {
                if (empty($user->password)) {
                    return redirect()->route('onboarding.password');
                }
                if (empty($user->nik)) {
                    return redirect()->route('onboarding.step1');
                }
                if (empty($user->ktp_photo) && empty($user->ktp_path)) {
                    return redirect()->route('onboarding.step2');
                }
                if (empty($user->selfie_photo)) {
                    return redirect()->route('onboarding.step3');
                }
            }
        }

        return $next($request);
    }
}