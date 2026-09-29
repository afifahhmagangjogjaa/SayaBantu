<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class BlockedPartnerController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // Base query: users with role mitra or customer
        $baseQuery = User::whereIn('role', ['mitra', 'customer', 'kustomer']);

        // Filter by admin's cities if user is admin (Multi-city logic)
        if (auth()->user() && auth()->user()->role === 'admin') {
            $admin = auth()->user();
            $cityIds = \App\Models\City::where('admin_id', $admin->id)
                ->pluck('id')
                ->merge($admin->managedCities()->pluck('cities.id'))
                ->push($admin->city_id)
                ->filter()
                ->unique();
                
            $baseQuery->whereIn('city_id', $cityIds);
        }

        // Counts for cards (fresh queries to avoid mutation)
        $totalCount = (clone $baseQuery)->count();
        $blockedCount = (clone $baseQuery)->where('status', 'blocked')->count();
        $activeCount = (clone $baseQuery)->where('status', 'active')->count();
        $mitraCount = (clone $baseQuery)->where('role', 'mitra')->count();
        $customerCount = (clone $baseQuery)->whereIn('role', ['customer', 'kustomer'])->count();

        // Apply filters from request
        $query = User::whereIn('role', ['mitra', 'customer', 'kustomer'])->with('city');

        // Filter by admin's cities if user is admin (Multi-city logic)
        if (auth()->user() && auth()->user()->role === 'admin') {
            $admin = auth()->user();
            $cityIds = \App\Models\City::where('admin_id', $admin->id)
                ->pluck('id')
                ->merge($admin->managedCities()->pluck('cities.id'))
                ->push($admin->city_id)
                ->filter()
                ->unique();
                
            $query->whereIn('city_id', $cityIds);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            if ($role === 'mitra') {
                $query->where('role', 'mitra');
            } elseif ($role === 'customer') {
                $query->whereIn('role', ['customer', 'kustomer']);
            }
        }

        if ($status = $request->get('status')) {
            if (in_array($status, ['active', 'inactive', 'blocked'])) {
                $query->where('status', $status);
            }
        }

        $users = $query->orderByDesc('updated_at')->paginate(15)->withQueryString();

        return view('admin.partners.blocked', [
            'blocked' => $users,
            'counts' => [
                'total' => $totalCount,
                'blocked' => $blockedCount,
                'active' => $activeCount,
                'mitra' => $mitraCount,
                'customer' => $customerCount,
            ],
        ]);
    }

    public function toggle($id)
    {
        $user = User::findOrFail($id);
        // Toggle between 'blocked' and 'active' status
        $user->status = $user->status === 'blocked' ? 'active' : 'blocked';
        $user->save();

        $label = $user->status === 'blocked' ? 'User diblokir.' : 'User dibuka blokirnya.';
        return back()->with('success', $label);
    }

    public function toggleShadowBan($id)
    {
        $user = User::findOrFail($id);
        $user->is_shadow_banned = !((bool) $user->is_shadow_banned);
        $user->save();

        // Record partner activity log
        try {
            \App\Models\PartnerActivity::create([
                'user_id' => $user->id,
                'activity_type' => $user->is_shadow_banned ? 'shadow_ban_enabled' : 'shadow_ban_disabled',
                'description' => $user->is_shadow_banned 
                    ? 'Admin mengaktifkan Shadow Ban untuk akun ini' 
                    : 'Admin menonaktifkan Shadow Ban untuk akun ini',
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->header('User-Agent'),
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        $label = $user->is_shadow_banned 
            ? '👻 Shadow Ban berhasil diaktifkan. Pengguna ini tidak akan melihat/menerima pesanan baru.' 
            : '✅ Shadow Ban berhasil dinonaktifkan. Pengguna kembali dapat menerima pesanan secara normal.';
        return back()->with('success', $label);
    }
}
