<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;

class AdminUserController extends Controller
{
    public function index(Request $request)
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

        // Filter by role
        if ($request->has('role')) {
            $role = $request->get('role');
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        } else {
            // By default, show mitra and customer/kustomer roles to focus admin listing
            $query->whereIn('role', ['mitra', 'kustomer', 'customer']);
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

        return view('admin.users.index', compact('users'));
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

        // Only restrict access when the admin is linked to one or more cities.
        // If the admin has no city assignments, allow viewing any user.
        if ($cityIds->isNotEmpty() && !$cityIds->contains($user->city_id)) {
            abort(404);
        }

        // If the request is AJAX, return only the partial HTML suitable for a modal.
        if ($request->ajax()) {
            $user->load('city');
            $user->city_name = optional($user->city)->name ?? optional(City::find($user->city_id))->name;
            return view('admin.users.partials.show', compact('user'));
        }

        $user->load('city');

        return view('admin.users.show', compact('user'));
    }
}
