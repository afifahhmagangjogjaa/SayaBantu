<?php

namespace App\Livewire\Customer;

use App\Models\BalanceTransaction;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

class Topup extends Component
{
    public $amount;
    public $method = 'bank';
    public $snapToken;
    public $orderId;

    protected $rules = [
        'amount' => 'required|numeric|min:10000|max:10000000',
        'method' => 'required|string',
    ];

    protected $messages = [
        'amount.required' => 'Nominal top up wajib diisi',
        'amount.numeric' => 'Nominal top up harus berupa angka',
        'amount.min' => 'Minimal top up adalah Rp 10.000',
        'amount.max' => 'Maksimal top up adalah Rp 10.000.000',
        'method.required' => 'Metode pembayaran wajib dipilih',
    ];

    public function mount()
    {
        $user = auth()->user();

        if ($user && !$user->isProfileComplete()) {
            $missing = implode(', ', $user->getMissingProfileFields());
            session()->flash('error', "Harap lengkapi profil Anda ($missing) terlebih dahulu sebelum melakukan Top Up.");
            return redirect()->route('customer.dashboard');
        }

        $this->amount = null;
    }

    public function submit()
    {
        $this->validate();

        try {
            // Set Midtrans configuration
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized');
            Config::$is3ds = config('services.midtrans.is_3ds');

            // Di Windows lokal, SSL certificate sering bermasalah (tidak ada CA bundle).
            // Matikan verifikasi SSL hanya saat development (bukan production).
            // CATATAN: CURLOPT_HTTPHEADER harus selalu disertakan karena SDK Midtrans
            // langsung mengaksesnya tanpa pengecekan isset() terlebih dahulu.
            if (!config('services.midtrans.is_production')) {
                Config::$curlOptions = [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTPHEADER     => [],
                ];
            }

            // Validate Midtrans configuration
            $serverKey = config('services.midtrans.server_key');
            $clientKey = config('services.midtrans.client_key');

            if (
                empty($serverKey) ||
                $serverKey === 'your-server-key-here' ||
                $serverKey === 'SB-Mid-server-YOUR_SERVER_KEY_HERE'
            ) {
                session()->flash('error', 'Midtrans belum dikonfigurasi. Silakan daftar di https://dashboard.sandbox.midtrans.com/ dan masukkan kredensial Anda ke file .env');
                \Log::error('Midtrans Server Key not configured in .env file');
                return;
            }

            if (
                empty($clientKey) ||
                $clientKey === 'your-client-key-here' ||
                $clientKey === 'SB-Mid-client-YOUR_CLIENT_KEY_HERE'
            ) {
                session()->flash('error', 'Midtrans belum dikonfigurasi. Silakan daftar di https://dashboard.sandbox.midtrans.com/ dan masukkan kredensial Anda ke file .env');
                \Log::error('Midtrans Client Key not configured in .env file');
                return;
            }

            $user = auth()->user();

            // Generate unique order ID
            $this->orderId = 'TOPUP-' . $user->id . '-' . time();

            // Create transaction record
            $transaction = BalanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $this->amount,
                'type' => 'topup',
                'description' => 'Top up saldo via Midtrans',
                'order_id' => $this->orderId,
                'status' => 'pending',
            ]);

            // Prepare transaction params for Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $this->orderId,
                    'gross_amount' => (int) $this->amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '08123456789',
                ],
                'enabled_payments' => $this->getEnabledPayments(),
                'callbacks' => [
                    'finish' => route('topup.finish'),
                    'unfinish' => route('topup.unfinish'),
                    'error' => route('topup.error'),
                ],
            ];

            \Log::info('Midtrans Request', [
                'server_key_prefix' => substr(Config::$serverKey, 0, 15),
                'is_production' => Config::$isProduction,
                'params' => $params,
            ]);

            // Get Snap token
            $this->snapToken = Snap::getSnapToken($params);

            \Log::info('Midtrans Snap Token Generated', [
                'snap_token' => substr($this->snapToken, 0, 20) . '...',
            ]);

            // Save snap token to transaction
            $transaction->snap_token = $this->snapToken;
            $transaction->save();

            // Store last order id in session so finish callback can use it if Midtrans doesn't include order_id
            session()->put('midtrans_last_order_id', $this->orderId);

            // ====================================================
            // DEVELOPMENT ONLY: Langsung update saldo setelah token
            // dibuat. Di production, webhook Midtrans yang update.
            // ====================================================
            if (!config('services.midtrans.is_production')) {
                \Illuminate\Support\Facades\DB::table('balance_transactions')
                    ->where('id', $transaction->id)
                    ->update(['status' => 'completed', 'processed_at' => now()]);

                $sum = \Illuminate\Support\Facades\DB::table('balance_transactions')
                    ->where('user_id', $user->id)
                    ->where('type', 'topup')
                    ->where('status', 'completed')
                    ->sum('amount');

                // Gunakan Eloquent agar created_at/updated_at otomatis terisi
                \App\Models\UserBalance::updateOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => (float) $sum]
                );

                \Log::info('DEV: saldo langsung diupdate setelah token', [
                    'user_id' => $user->id,
                    'amount'  => $this->amount,
                    'balance' => $sum,
                ]);
            }


            // Dispatch event to open Midtrans Snap
            $this->dispatch('openMidtransSnap', snapToken: $this->snapToken);

        } catch (\Exception $e) {
            \Log::error('Midtrans error: ' . $e->getMessage());
            \Log::error('Error details', [
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Jika sedang development dan ada kendala koneksi/DNS ke server Midtrans (Could not resolve host)
            if (!config('services.midtrans.is_production') && (str_contains($e->getMessage(), 'Could not resolve host') || str_contains($e->getMessage(), 'cURL error') || str_contains($e->getMessage(), 'Connection timed out'))) {
                $user = auth()->user();
                $orderId = $this->orderId ?? ('TOPUP-' . $user->id . '-' . time());

                \App\Models\BalanceTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $this->amount,
                    'type' => 'topup',
                    'description' => 'Top up saldo (Simulasi Sandbox Development)',
                    'order_id' => $orderId,
                    'status' => 'completed',
                    'processed_at' => now(),
                ]);

                $sum = \Illuminate\Support\Facades\DB::table('balance_transactions')
                    ->where('user_id', $user->id)
                    ->where('type', 'topup')
                    ->where('status', 'completed')
                    ->sum('amount');

                \App\Models\UserBalance::updateOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => (float) $sum]
                );

                session()->flash('status', '✅ Top up sebesar Rp ' . number_format($this->amount, 0, ',', '.') . ' berhasil ditambahkan (Simulasi Mode Development / Sandbox)!');
                $this->amount = null;
                return;
            }

            session()->flash('error', 'Terjadi kesalahan koneksi ke Midtrans: ' . $e->getMessage());
        }
    }

    private function getEnabledPayments()
    {
        // Return payment methods based on selected method
        if ($this->method === 'bank') {
            return ['bank_transfer', 'echannel'];
        } elseif ($this->method === 'ewallet') {
            return ['gopay', 'shopeepay', 'qris'];
        }

        // Default: all payment methods
        return ['bank_transfer', 'echannel', 'gopay', 'shopeepay', 'qris'];
    }

    public function render()
    {
        return view('livewire.customer.topup');
    }
}
