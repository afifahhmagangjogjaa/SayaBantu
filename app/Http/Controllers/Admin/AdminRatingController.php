<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\City;
use App\Models\User;

class AdminRatingController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth()->user();

        // Get all cities managed by this admin
        $cityIds = City::where('admin_id', $admin->id)
            ->pluck('id')
            ->merge($admin->managedCities()->pluck('cities.id'))
            ->push($admin->city_id)
            ->filter()
            ->unique();

        // Query Rekap Pengguna (Mitra & Customer)
        $usersQuery = User::whereIn('role', ['mitra', 'customer', 'kustomer'])
            ->with('city');

        if ($cityIds->isNotEmpty()) {
            $usersQuery->whereIn('city_id', $cityIds);
        }

        if ($search = $request->get('search')) {
            $s = trim($search);
            $usersQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($role = $request->get('role')) {
            if ($role === 'mitra') {
                $usersQuery->where('role', 'mitra');
            } elseif (in_array($role, ['customer', 'kustomer'])) {
                $usersQuery->whereIn('role', ['customer', 'kustomer']);
            }
        }

        $users = $usersQuery->orderBy('name')->paginate(15)->withQueryString();

        // Calculate stats for admin's scope
        $statsQuery = Rating::query();
        if ($cityIds->isNotEmpty()) {
            $statsQuery->where(function ($q) use ($cityIds) {
                $q->whereHas('help', fn($hq) => $hq->whereIn('city_id', $cityIds))
                  ->orWhereHas('rater', fn($rq) => $rq->whereIn('city_id', $cityIds))
                  ->orWhereHas('ratee', fn($rq) => $rq->whereIn('city_id', $cityIds));
            });
        }

        $stats = [
            'total_reviews' => (clone $statsQuery)->count(),
            'mitra_avg' => round((float)(clone $statsQuery)->where(function($q) {
                $q->where('type', 'customer_to_mitra')->orWhereNull('type');
            })->avg('rating'), 1) ?: 0,
            'customer_avg' => round((float)(clone $statsQuery)->where('type', 'mitra_to_customer')->avg('rating'), 1) ?: 0,
            'low_count' => (clone $statsQuery)->whereIn('rating', [1, 2])->count(),
        ];

        return view('admin.ratings.index', compact('users', 'stats'));
    }

    public function userRatings(Request $request, User $user)
    {
        $user->load('city');
        $ratings = $user->isMitra()
            ? $user->mitraRatings()->with(['rater', 'user', 'help.city'])->latest()->get()
            : $user->customerRatings()->with(['rater', 'user', 'help.city'])->latest()->get();

        if ($request->ajax()) {
            return view('admin.ratings.partials.modal-user-ratings', compact('user', 'ratings'));
        }

        return view('admin.ratings.user', compact('user', 'ratings'));
    }

    public function toggleShadowBan(Request $request, User $user)
    {
        $user->is_shadow_banned = !$user->is_shadow_banned;
        $user->shadow_banned_at = $user->is_shadow_banned ? now() : null;
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_shadow_banned' => $user->is_shadow_banned,
                'message' => $user->is_shadow_banned
                    ? "Pengguna {$user->name} berhasil dikenakan Shadow Ban (Senyap)."
                    : "Pengguna {$user->name} berhasil dibebaskan dari Shadow Ban.",
            ]);
        }

        return back()->with('success', $user->is_shadow_banned ? 'User dikenakan Shadow Ban.' : 'User dibebaskan dari Shadow Ban.');
    }
}
