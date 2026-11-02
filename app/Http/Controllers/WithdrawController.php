<?php

namespace App\Http\Controllers;

use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\WithdrawRequest;
use App\Notifications\NewWithdrawRequest;
use App\Services\PaymentGatewayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WithdrawController extends Controller
{
    protected PaymentGatewayService $gateway;

    public function __construct(PaymentGatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    /** Show withdraw form */
    public function showForm(Request $request)
    {
        $user = $request->user();
        $isCustomer = ($user->role === 'customer');
        $prefix = $isCustomer ? 'customer' : 'mitra';
        $dashboardRoute = $prefix . '.dashboard';

        if (! $user->isProfileComplete()) {
            $missing = implode(', ', $user->getMissingProfileFields());

            return redirect()->route($dashboardRoute)->with('error', "Harap lengkapi profil Anda ($missing) terlebih dahulu sebelum melakukan penarikan saldo.");
        }

        // Cek batasan penarikan 1x dalam 24 jam
        $limit24h = $user->getWithdraw24hLimitStatus();

        // If the user's latest withdraw was already processed successfully,
        // redirect them to the success page so they see the confirmation.
        $latest = WithdrawRequest::where('user_id', $user->id)->orderByDesc('created_at')->first();
        // If caller explicitly requests the form (force=1) skip redirect so "Kembali ke Pengajuan" works
        $force = $request->query('force');

        // Also avoid redirecting when the user clicked the link from the dashboard
        $referer = $request->headers->get('referer');
        $fromDashboard = false;
        try {
            $dashboardUrl = route($dashboardRoute);
            if ($referer && str_contains($referer, $dashboardUrl)) {
                $fromDashboard = true;
            }
        } catch (\Throwable $e) {
            // ignore route exceptions
        }

        // Redirect to success only if the latest withdraw is success AND it was processed recently
        // This avoids redirecting to the success page on refresh long after processing occurred.
        $recentWindowMinutes = 15;
        $isRecent = false;
        if ($latest && $latest->processed_at) {
            try {
                $isRecent = Carbon::parse($latest->processed_at)->greaterThanOrEqualTo(Carbon::now()->subMinutes($recentWindowMinutes));
            } catch (\Throwable $e) {
                $isRecent = false;
            }
        }

        if (! $force && ! $fromDashboard && $latest && $isRecent) {
            if ($latest->status === WithdrawRequest::STATUS_SUCCESS) {
                return redirect()->route($prefix . '.withdraw.success', $latest->id);
            }

            if ($latest->status === WithdrawRequest::STATUS_FAILED) {
                return redirect()->route($prefix . '.withdraw.rejected', $latest->id);
            }
        }

        // Otherwise render the withdraw form (the form view will show pending message if needed)
        $bankAccounts = $user->bankAccounts()->get();
        $primaryAccount = $user->primaryBankAccount();

        return view('livewire.mitra.withdraw.form', [
            'user' => $user,
            'bankAccounts' => $bankAccounts,
            'primaryAccount' => $primaryAccount,
            'limit24h' => $limit24h,
            'prefix' => $prefix,
            'isCustomer' => $isCustomer,
        ]);
    }

    /** Handle withdraw request */
    public function requestWithdraw(Request $request)
    {
        $user = $request->user();
        $isCustomer = ($user->role === 'customer');
        $prefix = $isCustomer ? 'customer' : 'mitra';
        $dashboardRoute = $prefix . '.dashboard';

        if (! $user->isProfileComplete()) {
            $missing = implode(', ', $user->getMissingProfileFields());

            return redirect()->route($dashboardRoute)->with('error', "Harap lengkapi profil Anda ($missing) terlebih dahulu sebelum melakukan penarikan saldo.");
        }

        if (! $user->hasVerifiedEmail()) {
            return back()->withErrors(['general' => 'Saldo belum dapat ditarik. Harap verifikasi email Anda terlebih dahulu sebelum mengajukan penarikan dana.'])->withInput();
        }

        if (! $user->hasTransactionPin()) {
            return redirect()->route($prefix . '.bank-accounts')->with('error', 'Harap atur PIN transaksi 6 digit terlebih dahulu untuk keamanan penarikan saldo.');
        }

        if ($user->isPinLocked()) {
            return back()->withErrors(['pin' => 'PIN transaksi Anda terkunci karena salah 3x berturut-turut. Coba lagi dalam ' . $user->getPinLockRemainingMinutes() . ' menit.'])->withInput();
        }

        // Cek batasan penarikan 1x dalam 24 jam
        $limit24h = $user->getWithdraw24hLimitStatus();
        if (! $limit24h['allowed']) {
            return back()->withErrors([
                'general' => "Penarikan saldo dibatasi 1x dalam 24 jam. Anda dapat mengajukan penarikan kembali pada {$limit24h['available_at']->translatedFormat('d M Y, H:i')} WIB (sisa {$limit24h['remaining_text']}).",
            ])->withInput();
        }

        $userBalance = (int) $user->balance;

        if ($request->has('amount')) {
            $rawAmount = preg_replace('/\D/', '', (string) $request->input('amount'));
            $request->merge(['amount' => $rawAmount !== '' ? (int) $rawAmount : null]);
        }

        $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:'.max(10000, $userBalance)],
            'bank_account_id' => ['required', 'exists:user_bank_accounts,id'],
            'pin' => ['required', 'digits:6'],
        ], [
            'amount.required' => 'Jumlah penarikan wajib diisi.',
            'amount.integer' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Jumlah penarikan minimal Rp 10.000.',
            'amount.max' => 'Saldo Anda tidak mencukupi untuk penarikan sebesar Rp '.number_format((int) $request->input('amount', 0), 0, ',', '.').'. Saldo tersedia: Rp '.number_format($userBalance, 0, ',', '.').'.',
            'bank_account_id.required' => 'Silakan pilih rekening bank atau e-wallet tujuan.',
            'bank_account_id.exists' => 'Rekening tujuan tidak valid.',
            'pin.required' => 'PIN transaksi 6 digit wajib diisi.',
            'pin.digits' => 'PIN transaksi harus berupa 6 digit angka.',
        ]);

        // Verifikasi kepemilikan rekening
        $bankAccount = $user->bankAccounts()->where('id', $request->input('bank_account_id'))->first();
        if (! $bankAccount) {
            return back()->withErrors(['bank_account_id' => 'Rekening bank tidak ditemukan atau bukan milik Anda.'])->withInput();
        }

        // Verifikasi PIN Transaksi
        if (! $user->verifyTransactionPin($request->input('pin'))) {
            $attemptsLeft = max(0, 3 - (int) $user->pin_attempts);
            if ($attemptsLeft === 0) {
                return back()->withErrors(['pin' => 'PIN salah! Akun Anda dikunci selama 15 menit karena 3x salah memasukkan PIN.'])->withInput();
            }
            return back()->withErrors(['pin' => 'PIN transaksi salah. Sisa percobaan: ' . $attemptsLeft . ' kali.'])->withInput();
        }

        $amount = (int) $request->input('amount');

        // Business checks: minimum balance
        if ($userBalance < 10000) {
            return back()->withErrors(['amount' => 'Saldo Anda tidak mencukupi untuk melakukan penarikan. Minimal saldo adalah Rp 10.000 (Saldo Anda: Rp '.number_format($userBalance, 0, ',', '.').').'])->withInput();
        }

        // Business checks: amount exceeds available balance
        if ($amount > $userBalance) {
            return back()->withErrors(['amount' => 'Jumlah penarikan (Rp '.number_format($amount, 0, ',', '.').') melebihi saldo tersedia Anda (Rp '.number_format($userBalance, 0, ',', '.').').'])->withInput();
        }

        // Business checks: pending requests
        if ($user->hasPendingOrProcessingWithdraws()) {
            return back()->withErrors(['general' => 'Anda memiliki permintaan tarik saldo yang sedang diproses. Mohon tunggu hingga selesai.'])->withInput();
        }

        try {
            // Deduct / hold balance immediately from user wallet
            $userBalanceModel = $user->balance()->firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
            $userBalanceModel->deductBalance(
                $amount,
                'Pengajuan Penarikan Saldo ke ' . $bankAccount->bank_name . ' (' . $bankAccount->masked_account_number . ')',
                'WD-PENDING'
            );

            // Create withdraw request with account name saved
            $withdraw = WithdrawRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'bank_code' => $bankAccount->bank_code,
                'account_number' => $bankAccount->account_number,
                'account_name' => $bankAccount->account_name,
                'status' => WithdrawRequest::STATUS_PENDING,
            ]);

            // Notify super admins and admin of the mitra's city
            try {
                $superAdmins = User::where('role', 'super_admin')->where('status', 'active')->get();
                $mitraCityId = $user->city_id ?? $withdraw->user?->city_id;
                $cityAdmins = User::getAdminsForCity($mitraCityId);

                $recipients = $superAdmins->merge($cityAdmins)->unique('id');

                foreach ($recipients as $recipient) {
                    $recipient->notify(new NewWithdrawRequest($withdraw));
                }
            } catch (\Throwable $ne) {
                Log::warning('WithdrawController: failed to send withdraw notification', ['error' => $ne->getMessage()]);
            }

            // Redirect back to form — the form view will show a 'pending' message when a request exists
            return redirect()->route($prefix . '.withdraw.form')->with('status', 'Pengajuan Anda berhasil dikirim dan sedang menunggu proses oleh admin. Mohon tunggu 1-5 hari kerja.');
        } catch (\Throwable $e) {
            Log::error('WithdrawController: error creating withdraw', ['error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Terjadi kesalahan saat membuat permintaan tarik saldo.'])->withInput();
        }
    }

    /** Show transaction & withdraw history for user */
    public function withdrawHistory(Request $request)
    {
        $user = $request->user();
        $isCustomer = ($user->role === 'customer');
        $prefix = $isCustomer ? 'customer' : 'mitra';
        $activeTab = $request->query('tab', 'income');

        $incomeHistory = BalanceTransaction::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('type', 'topup')
                    ->orWhere('description', 'like', 'Pendapatan Bantuan%');
            })
            ->where('status', 'completed')
            ->with(['help'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'income_page');

        $withdrawHistory = WithdrawRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'withdraw_page');

        $totalIncome = (float) BalanceTransaction::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('type', 'topup')
                    ->orWhere('description', 'like', 'Pendapatan Bantuan%');
            })
            ->where('status', 'completed')
            ->sum('amount');

        $totalWithdrawn = (float) WithdrawRequest::where('user_id', $user->id)
            ->whereIn('status', ['success', 'approved'])
            ->sum('amount');

        // Build combined transactions feed matching new mobile design
        $recentIncomes = BalanceTransaction::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('type', 'topup')
                    ->orWhere('description', 'like', 'Pendapatan Bantuan%');
            })
            ->where('status', 'completed')
            ->with(['help.category'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get()
            ->map(function ($item) use ($prefix) {
                $help = $item->help;
                $orderCode = $help ? ($help->order_id ?: ('SRB'.str_pad($help->id, 4, '0', STR_PAD_LEFT))) : ($item->order_id ?: ('SRB'.str_pad($item->id, 4, '0', STR_PAD_LEFT)));
                if (str_starts_with($orderCode, 'HELP-')) {
                    $orderCode = 'SRB'.str_pad(substr($orderCode, 5), 4, '0', STR_PAD_LEFT);
                }
                $catName = $help ? (optional($help->category)->name ?? $help->title) : ($item->description ?? 'Pekerjaan Jasa');

                return (object) [
                    'id' => $item->id,
                    'type' => 'income',
                    'title' => 'Order #'.$orderCode,
                    'subtitle' => $catName,
                    'amount' => (float) $item->amount,
                    'created_at' => $item->created_at,
                    'status' => 'completed',
                    'detail_url' => $help ? route($prefix . '.helps.detail', $help->id) : null,
                ];
            });

        $recentWithdraws = WithdrawRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get()
            ->map(function ($item) use ($prefix) {
                $bankCode = strtoupper($item->bank_code ?? 'BCA');
                $accMasked = strlen($item->account_number) >= 4 ? '****'.substr($item->account_number, -4) : '****1234';

                return (object) [
                    'id' => $item->id,
                    'type' => 'withdraw',
                    'title' => 'Penarikan Saldo',
                    'subtitle' => 'Ke '.$bankCode.' '.$accMasked,
                    'amount' => (float) $item->amount,
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'detail_url' => null,
                ];
            });

        $combinedTransactions = $recentIncomes->concat($recentWithdraws)
            ->sortByDesc('created_at')
            ->values();

        // If user has no transactions yet, show exact preview items matching the reference mockup
        if ($combinedTransactions->isEmpty()) {
            $combinedTransactions = collect([
                (object) [
                    'id' => 1,
                    'type' => 'income',
                    'title' => 'Order #SRB0012',
                    'subtitle' => 'Bersih-bersih Rumah',
                    'amount' => 75000,
                    'created_at' => \Carbon\Carbon::create(2025, 5, 12, 10, 30),
                    'status' => 'completed',
                    'detail_url' => null,
                ],
                (object) [
                    'id' => 2,
                    'type' => 'income',
                    'title' => 'Order #PDN0008',
                    'subtitle' => 'Pindahan Barang',
                    'amount' => 120000,
                    'created_at' => \Carbon\Carbon::create(2025, 5, 10, 14, 20),
                    'status' => 'completed',
                    'detail_url' => null,
                ],
                (object) [
                    'id' => 3,
                    'type' => 'withdraw',
                    'title' => 'Penarikan Saldo',
                    'subtitle' => 'Ke BCA ****1234',
                    'amount' => 100000,
                    'created_at' => \Carbon\Carbon::create(2025, 5, 8, 9, 15),
                    'status' => 'completed',
                    'detail_url' => null,
                ],
                (object) [
                    'id' => 4,
                    'type' => 'income',
                    'title' => 'Order #TBG0005',
                    'subtitle' => 'Tukang Bangunan',
                    'amount' => 150000,
                    'created_at' => \Carbon\Carbon::create(2025, 5, 5, 16, 40),
                    'status' => 'completed',
                    'detail_url' => null,
                ],
            ]);
        }

        return view('livewire.mitra.withdraw.history', [
            'incomeHistory' => $incomeHistory,
            'withdrawHistory' => $withdrawHistory,
            'combinedTransactions' => $combinedTransactions,
            'history' => $withdrawHistory, // backward compatibility
            'activeTab' => $activeTab,
            'totalIncome' => $totalIncome,
            'totalWithdrawn' => $totalWithdrawn,
            'user' => $user,
            'prefix' => $user->role === 'customer' ? 'customer' : 'mitra',
            'isCustomer' => ($user->role === 'customer'),
        ]);
    }

    /** Show success page for a completed withdraw */
    public function showSuccess(Request $request, WithdrawRequest $withdraw)
    {
        $user = $request->user();
        if ($withdraw->user_id !== $user->id) {
            abort(403);
        }

        $prefix = $user->role === 'customer' ? 'customer' : 'mitra';

        if ($withdraw->status !== WithdrawRequest::STATUS_SUCCESS) {
            return redirect()->route($prefix . '.withdraw.history')->with('status', 'Penarikan belum selesai atau tidak ditemukan.');
        }

        return view('livewire.mitra.withdraw.success', [
            'withdraw' => $withdraw,
            'user' => $user,
            'prefix' => $prefix,
            'isCustomer' => ($user->role === 'customer'),
        ]);
    }

    /** Show rejected page for a failed withdraw */
    public function showRejected(Request $request, WithdrawRequest $withdraw)
    {
        $user = $request->user();
        if ($withdraw->user_id !== $user->id) {
            abort(403);
        }

        $prefix = $user->role === 'customer' ? 'customer' : 'mitra';

        if ($withdraw->status !== WithdrawRequest::STATUS_FAILED) {
            return redirect()->route($prefix . '.withdraw.history')->with('status', 'Penarikan belum dibatalkan atau tidak ditemukan.');
        }

        return view('livewire.mitra.withdraw.rejected', [
            'withdraw' => $withdraw,
            'user' => $user,
            'prefix' => $prefix,
            'isCustomer' => ($user->role === 'customer'),
        ]);
    }

    /** Public endpoint: gateway callback (for real integration) */
    public function gatewayCallback(Request $request)
    {
        // In production validate signature / secret
        $payload = $request->all();

        if (empty($payload['external_id']) || empty($payload['status'])) {
            return response()->json(['error' => 'invalid_payload'], 422);
        }

        $withdraw = WithdrawRequest::where('external_id', $payload['external_id'])->first();
        if (! $withdraw) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $status = $payload['status'];

        if ($status === WithdrawRequest::STATUS_SUCCESS) {
            $withdraw->update(['status' => WithdrawRequest::STATUS_SUCCESS, 'processed_at' => now()]);
        } else {
            $withdraw->update(['status' => WithdrawRequest::STATUS_FAILED, 'processed_at' => now()]);
            // refund
            $user = $withdraw->user;
            if ($user) {
                $user->adjustBalance($withdraw->amount);
            }
        }

        return response()->json(['ok' => true]);
    }
}
