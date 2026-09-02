<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\WithdrawStatusNotification;

class AdminWithdrawController extends Controller
{
    public function index()
    {
        // Accept filters from the request (GET)
        $request = request();
        $query = WithdrawRequest::with('user');

        // Filter per kota yang dikelola admin (via pivot admin_city)
        if (auth()->user() && auth()->user()->role === 'admin') {
            $managedCityIds = auth()->user()->managedCities()->pluck('cities.id')->toArray();
            if (!empty($managedCityIds)) {
                $query->whereHas('user', function ($q) use ($managedCityIds) {
                    $q->whereIn('city_id', $managedCityIds);
                });
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by bank code (case-insensitive)
        if ($request->filled('bank_code')) {
            $bankFilter = trim($request->input('bank_code'));
            $query->whereRaw('LOWER(bank_code) = ?', [strtolower($bankFilter)]);
        }

        // Filter by user / search term (id, user_id, name, email, phone, account number, bank, amount, external_id, description)
        $rawTerm = trim((string) ($request->input('search') ?? $request->input('user') ?? ''));
        if ($rawTerm !== '') {
            $query->where(function ($q) use ($rawTerm) {
                // 1. Pencarian eksplisit WD ID: misal "WD: #1", "WD #1", "WD 1", "#1", "WD1"
                if (preg_match('/^(?:wd\s*:?\s*#?|#)(\d+)$/i', $rawTerm, $matches)) {
                    $withdrawId = (int) $matches[1];
                    $q->where('id', $withdrawId);
                    return;
                }

                // 2. Pencarian eksplisit User ID: misal "User ID: 4", "User 4", "UID: 4", "ID: 4", "ID 4"
                if (preg_match('/^(?:user\s*(?:id)?\s*:?|uid\s*:?|id\s*:?)\s*(\d+)$/i', $rawTerm, $matches)) {
                    $targetUserId = (int) $matches[1];
                    $q->where('user_id', $targetUserId);
                    return;
                }

                // 3. Input murni angka (misal "1", "4", "50000", "0978980")
                if (ctype_digit($rawTerm)) {
                    $numVal = (int) $rawTerm;
                    $q->where('id', $numVal)
                      ->orWhere('user_id', $numVal)
                      ->orWhere('amount', $numVal)
                      ->orWhere('account_number', 'like', "%{$rawTerm}%");

                    // Cari no HP jika panjang angka >= 4 digit (mencegah '1' / '0' mencocokkan semua no HP 08...)
                    if (strlen($rawTerm) >= 4) {
                        $q->orWhereHas('user', function ($uq) use ($rawTerm) {
                            $uq->where('phone', 'like', "%{$rawTerm}%");
                        });
                    }
                    return;
                }

                // 4. Input format mata uang: misal "50.000", "Rp 50.000", "Rp50000"
                $cleanCurrency = preg_replace('/[^0-9]/', '', $rawTerm);
                if ($cleanCurrency !== '' && (float)$cleanCurrency >= 1000 && preg_match('/^(?:rp|rp\.)?\s*[\d\.\,]+$/i', $rawTerm)) {
                    $q->where('amount', (float) $cleanCurrency);
                    return;
                }

                // 5. Pencarian teks umum (Nama Mitra, Email, Bank, External ID, Deskripsi)
                $q->where('account_number', 'like', "%{$rawTerm}%")
                  ->orWhere('bank_code', 'like', "%{$rawTerm}%")
                  ->orWhere('external_id', 'like', "%{$rawTerm}%")
                  ->orWhere('description', 'like', "%{$rawTerm}%")
                  ->orWhereHas('user', function ($uq) use ($rawTerm) {
                      $uq->where('name', 'like', "%{$rawTerm}%")
                         ->orWhere('email', 'like', "%{$rawTerm}%")
                         ->orWhere('phone', 'like', "%{$rawTerm}%");
                  });
            });
        }

        // Filter by date range (created_at)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Order and paginate (keep query string)
        $items = $query->orderByDesc('created_at')->paginate(25)->appends($request->except('page'));

        // Complete list of Indonesian Banks & E-Wallets for filter
        $defaultBanks = [
            'BCA',
            'BRI',
            'BNI',
            'Mandiri',
            'BSI',
            'CIMB Niaga',
            'Permata',
            'Danamon',
            'BTN',
            'Panin',
            'OCBC NISP',
            'BTPN / Jenius',
            'Bank Mega',
            'Bank Jago',
            'SeaBank',
            'Allo Bank',
            'DANA',
            'OVO',
            'GoPay',
            'ShopeePay',
            'LinkAja',
        ];

        $dbBanks = WithdrawRequest::select('bank_code')->whereNotNull('bank_code')->distinct()->pluck('bank_code')->toArray();
        $banks = collect(array_merge($defaultBanks, $dbBanks))
            ->map(fn($b) => trim($b))
            ->filter()
            ->unique(fn($b) => strtolower($b))
            ->values();

        // Summary counts - filter per kota yang dikelola admin
        $countsQuery = WithdrawRequest::query();
        if (auth()->user() && auth()->user()->role === 'admin') {
            $managedCityIds = auth()->user()->managedCities()->pluck('cities.id')->toArray();
            if (!empty($managedCityIds)) {
                $countsQuery->whereHas('user', function ($q) use ($managedCityIds) {
                    $q->whereIn('city_id', $managedCityIds);
                });
            }
        }

        $counts = [
            'all' => (clone $countsQuery)->count(),
            'pending' => (clone $countsQuery)->where('status', WithdrawRequest::STATUS_PENDING)->count(),
            'processing' => (clone $countsQuery)->where('status', WithdrawRequest::STATUS_PROCESSING)->count(),
            'success' => (clone $countsQuery)->where('status', WithdrawRequest::STATUS_SUCCESS)->count(),
            'failed' => (clone $countsQuery)->where('status', WithdrawRequest::STATUS_FAILED)->count(),
        ];

        $routeName = request()->route() ? request()->route()->getName() : null;
        if ($routeName && strpos($routeName, 'superadmin.') === 0) {
            return view('superadmin.withdraws.index', ['items' => $items, 'banks' => $banks, 'counts' => $counts]);
        }

        return view('admin.withdraws.index', ['items' => $items, 'banks' => $banks, 'counts' => $counts]);
    }

    public function show(WithdrawRequest $withdraw)
    {
        $withdraw->load('user');
        $routeName = request()->route() ? request()->route()->getName() : null;
        if ($routeName && strpos($routeName, 'superadmin.') === 0) {
            return view('superadmin.withdraws.show', ['withdraw' => $withdraw]);
        }

        return view('admin.withdraws.show', ['withdraw' => $withdraw]);
    }

    /** Return withdraw details as a partial for modal (AJAX) */
    public function modal(WithdrawRequest $withdraw)
    {
        $withdraw->load('user');
        $routeName = request()->route() ? request()->route()->getName() : null;
        if ($routeName && strpos($routeName, 'superadmin.') === 0) {
            return view('superadmin.withdraws._modal', ['withdraw' => $withdraw]);
        }

        return view('admin.withdraws._modal', ['withdraw' => $withdraw]);
    }

    /** Approve and perform deduction */
    public function approve(Request $request, WithdrawRequest $withdraw)
    {
        $request->validate([
            'transfer_reference' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        if ($withdraw->status !== WithdrawRequest::STATUS_PENDING) {
            return back()->withErrors(['general' => 'Hanya request dengan status pending yang dapat diproses.']);
        }

        $user = $withdraw->user;
        $userBalance = $user->balance()->first();
        $current = $userBalance ? (int) round((float) $userBalance->balance) : 0;

        if ($current < $withdraw->amount) {
            return back()->withErrors(['general' => 'Saldo user tidak mencukupi untuk melakukan transfer.']);
        }

        try {
            // Deduct balance now
            $user->adjustBalance(-((int) $withdraw->amount));

            $withdraw->update([
                'status' => WithdrawRequest::STATUS_SUCCESS,
                'processed_at' => now(),
                'description' => $request->input('note') ? $request->input('note') : $withdraw->description,
                'external_id' => $request->input('transfer_reference') ?? $withdraw->external_id,
            ]);

            // Notify user via database notification with link to success page
            try {
                $withdraw->user?->notify(new WithdrawStatusNotification($withdraw));
            } catch (\Throwable $e) {
                Log::warning('Failed to send withdraw success notification: ' . $e->getMessage());
            }

            return redirect()->route('superadmin.withdraws.index')->with('status', 'Withdraw berhasil diproses dan saldo telah dipotong.');
        } catch (\Throwable $e) {
            Log::error('AdminWithdrawController: approve error', ['error' => $e->getMessage(), 'withdraw_id' => $withdraw->id]);
            return back()->withErrors(['general' => 'Terjadi kesalahan saat memproses withdraw.']);
        }
    }

    public function reject(Request $request, WithdrawRequest $withdraw)
    {
        $request->validate(['note' => ['nullable', 'string']]);

        if ($withdraw->status !== WithdrawRequest::STATUS_PENDING) {
            return back()->withErrors(['general' => 'Hanya request dengan status pending yang dapat dibatalkan.']);
        }

        $withdraw->update([
            'status' => WithdrawRequest::STATUS_FAILED,
            'processed_at' => now(),
            'description' => $request->input('note') ?? $withdraw->description,
        ]);

        // Notify user about rejection/failure
        try {
            $withdraw->user?->notify(new WithdrawStatusNotification($withdraw));
        } catch (\Throwable $e) {
            Log::warning('Failed to send withdraw failed notification: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.withdraws.index')->with('status', 'Withdraw dibatalkan.');
    }
}
