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
        $user->load(['city', 'registration']);
        $user->loadCount(['helps', 'partnerReports']);
        $user->city_name = optional($user->city)->name ?? optional(City::find($user->city_id))->name;

        if ($user->isMitra()) {
            $user->ratings_count = $user->mitra_rating_count;
            $user->average_rating = $user->mitra_average_rating > 0 ? round($user->mitra_average_rating, 1) : null;
            
            // Ambil riwayat pembatalan mitra
            $cancellations = \App\Models\PartnerActivity::where('user_id', $user->id)
                ->where('activity_type', 'help_cancelled')
                ->latest()
                ->get();
            $user->cancellations_count = $cancellations->count();
            $user->recent_cancellations = $cancellations->take(5);
        } else {
            $user->ratings_count = $user->customer_rating_count;
            $user->average_rating = $user->customer_average_rating > 0 ? round($user->customer_average_rating, 1) : null;
            $user->cancellations_count = 0;
            $user->recent_cancellations = collect();
        }

        // Ambil riwayat SP dari log di admin_notes semua laporan yang melibatkan user ini
        $spLogs = collect();
        $reportsInvolvingUser = \App\Models\PartnerReport::where(function ($q) use ($user) {
                $q->where('reported_user_id', $user->id)
                  ->orWhere('reporter_id', $user->id);
            })
            ->whereNotNull('admin_notes')
            ->where('admin_notes', '!=', '')
            ->get(['id', 'admin_notes', 'title', 'updated_at']);

        foreach ($reportsInvolvingUser as $rep) {
            $lines = array_filter(
                explode("\n", $rep->admin_notes),
                fn($l) => trim($l) !== ''
                    && (str_contains($l, 'memberikan sanksi') || str_contains($l, 'mencabut') || str_contains($l, 'Surat Peringatan'))
                    && str_contains(strtolower($l), strtolower($user->name))
            );
            foreach ($lines as $line) {
                $spLogs->push([
                    'log'       => trim($line),
                    'report_id' => $rep->id,
                    'report_title' => $rep->title,
                ]);
            }
        }

        // Juga ambil dari notifikasi database (SanctionNotification) untuk user ini
        $sanctionNotifs = $user->notifications()
            ->where('type', \App\Notifications\SanctionNotification::class)
            ->latest()
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.users.partials.show', compact('user', 'spLogs', 'sanctionNotifs'));
        }

        return view('admin.users.show', compact('user', 'spLogs', 'sanctionNotifs'));
    }

    public function verifyKtp(Request $request, User $user)
    {
        $admin = auth()->user();

        $cityIds = City::where('admin_id', $admin->id)
            ->pluck('id')
            ->merge($admin->managedCities()->pluck('cities.id'))
            ->push($admin->city_id)
            ->filter()
            ->unique();

        if ($cityIds->isNotEmpty() && ! $cityIds->contains($user->city_id)) {
            abort(403, 'Akses tidak diizinkan untuk pengguna kota lain.');
        }

        $user->verified = true;
        if ($user->status !== 'blocked') {
            $user->status = 'active';
        }
        $user->save();

        // Update matching registration if exists
        $reg = \App\Models\Registration::where('email', $user->email)->first();
        if ($reg) {
            $reg->update(['status' => 'approved']);
        }

        try {
            $user->notify(new \App\Notifications\KtpVerificationStatusNotification('approved'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif KTP approved: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "KTP pengguna {$user->name} berhasil disetujui (Terverifikasi)!"
            ]);
        }

        return back()->with('success', "KTP pengguna {$user->name} berhasil disetujui (Terverifikasi)!");
    }

    public function rejectKtp(Request $request, User $user)
    {
        $admin = auth()->user();

        $cityIds = City::where('admin_id', $admin->id)
            ->pluck('id')
            ->merge($admin->managedCities()->pluck('cities.id'))
            ->push($admin->city_id)
            ->filter()
            ->unique();

        if ($cityIds->isNotEmpty() && ! $cityIds->contains($user->city_id)) {
            abort(403, 'Akses tidak diizinkan untuk pengguna kota lain.');
        }

        $reason = $request->input('reason', 'Dokumen KTP tidak sesuai atau tidak jelas.');

        $user->verified = false;
        $user->save();

        // Update matching registration if exists
        $reg = \App\Models\Registration::where('email', $user->email)->first();
        if ($reg) {
            $reg->update(['status' => 'rejected', 'rejection_reason' => $reason]);
        }

        try {
            $user->notify(new \App\Notifications\KtpVerificationStatusNotification('rejected', $reason));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif KTP rejected: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Verifikasi KTP pengguna {$user->name} telah ditolak."
            ]);
        }

        return back()->with('success', "Verifikasi KTP pengguna {$user->name} telah ditolak.");
    }

    public function toggleStatus(Request $request, User $user)
    {
        $admin = auth()->user();

        $cityIds = City::where('admin_id', $admin->id)
            ->pluck('id')
            ->merge($admin->managedCities()->pluck('cities.id'))
            ->push($admin->city_id)
            ->filter()
            ->unique();

        if ($cityIds->isNotEmpty() && ! $cityIds->contains($user->city_id)) {
            abort(403, 'Akses tidak diizinkan untuk pengguna kota lain.');
        }

        // Toggle status: active <-> inactive (or if blocked, unblock to active)
        if ($user->status === 'blocked') {
            $user->status = 'active';
            $label = 'diaktifkan kembali';
        } elseif ($user->status === 'active') {
            $user->status = 'inactive';
            $label = 'dinonaktifkan';
        } else {
            $user->status = 'active';
            $label = 'diaktifkan kembali';
        }

        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $user->status,
                'message' => "Status pengguna {$user->name} berhasil {$label}."
            ]);
        }

        return back()->with('success', "Status pengguna {$user->name} berhasil {$label}.");
    }
}
