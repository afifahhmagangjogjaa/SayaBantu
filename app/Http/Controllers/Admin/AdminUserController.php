<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;

class AdminUserController extends Controller
{
    public function customers(Request $request)
    {
        return $this->index($request, 'customer');
    }

    public function mitra(Request $request)
    {
        return $this->index($request, 'mitra');
    }

    public function index(Request $request, $forcedRole = null)
    {
        $admin = auth()->user();

        // Get all cities managed by this admin (via admin_id, pivot table, or primary city_id)
        $cityIds = City::where('admin_id', $admin->id)
            ->pluck('id')
            ->merge($admin->managedCities()->pluck('cities.id'))
            ->push($admin->city_id)
            ->filter()
            ->unique();

        $query = User::with('city')
            ->withCount('helps')
            ->withCount('partnerReports');

        // Apply city scoping only when admin has linked cities
        if ($cityIds->isNotEmpty()) {
            $query->whereIn('city_id', $cityIds);
        }

        // Search by name or email
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Detect route or forced role
        $currentRoute = $request->route()?->getName() ?? '';
        if ($forcedRole === 'customer' || $currentRoute === 'admin.customers') {
            $pageRole = 'customer';
            $query->whereIn('role', ['customer', 'kustomer']);
        } elseif ($forcedRole === 'mitra' || $currentRoute === 'admin.mitra') {
            $pageRole = 'mitra';
            $query->where('role', 'mitra');
        } elseif ($request->has('role')) {
            $role = $request->get('role');
            if ($role !== 'all') {
                $query->where('role', $role);
            }
            $pageRole = $role;
        } else {
            // By default, show mitra and customer/kustomer roles to focus admin listing
            $query->whereIn('role', ['mitra', 'kustomer', 'customer']);
            $pageRole = 'all';
        }

        // Filter by account status
        if ($status = $request->get('account_status')) {
            if ($status === 'blocked') {
                $query->where('status', 'blocked');
            } elseif ($status === 'inactive') {
                $query->where('status', 'inactive');
            } elseif ($status === 'active') {
                $query->where('status', 'active');
            }
        }

        // Filter by KTP status
        if ($ktpStatus = $request->get('ktp_status')) {
            if ($ktpStatus === 'uploaded') {
                $query->where(function($q) {
                    $q->whereNotNull('ktp_path')->orWhereNotNull('ktp_photo');
                });
            } elseif ($ktpStatus === 'missing') {
                $query->whereNull('ktp_path')->whereNull('ktp_photo');
            }
        }

        $users = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Ensure we have a city_name property and accurate rating display per user
        foreach ($users as $user) {
            $user->city_name = optional($user->city)->name ?? optional(City::find($user->city_id))->name;

            if ($user->isMitra()) {
                $user->ratings_count = $user->mitra_rating_count;
                $user->average_rating = $user->mitra_average_rating > 0 ? round($user->mitra_average_rating, 1) : null;
            } else {
                $user->ratings_count = $user->customer_rating_count;
                $user->average_rating = $user->customer_average_rating > 0 ? round($user->customer_average_rating, 1) : null;
            }
        }

        return view('admin.users.index', compact('users', 'pageRole'));
    }

    public function show(\Illuminate\Http\Request $request, User $user)
    {
        $admin = auth()->user();

        $cityIds = City::where('admin_id', $admin->id)
            ->pluck('id')
            ->merge($admin->managedCities()->pluck('cities.id'))
            ->push($admin->city_id)
            ->filter()
            ->unique();

        // If admin has cities, check if target user belongs to allowed cities
        if ($cityIds->isNotEmpty() && ! $cityIds->contains($user->city_id)) {
            abort(403, 'Akses tidak diizinkan untuk pengguna kota lain.');
        }

        // Load relations and counts
        $user->load('city');
        $user->loadCount(['helps', 'partnerReports']);

        return view('admin.users.show', compact('user'));
    }
}
