<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Help;
use App\Models\BalanceTransaction;
use App\Models\User;
use Carbon\Carbon;

class AdminFeeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test users
        $customer = User::where('email', 'budi@example.com')->first();
        $mitra = User::where('role', 'mitra')->first();

        if (!$customer || !$mitra) {
            $this->command->error('Test users not found. Please run UserSeeder first.');
            return;
        }

        // Create some completed helps with admin fees
        $helps = [
            [
                'amount' => 100000,
                'admin_fee' => 10000, // 10% admin fee
                'title' => 'Bantuan Perbaikan Atap',
                'days_ago' => 5,
            ],
            [
                'amount' => 200000,
                'admin_fee' => 20000, // 10% admin fee
                'title' => 'Bantuan Renovasi Dapur',
                'days_ago' => 10,
            ],
            [
                'amount' => 50000,
                'admin_fee' => 5000, // 10% admin fee
                'title' => 'Bantuan Cat Rumah',
                'days_ago' => 15,
            ],
        ];

        foreach ($helps as $helpData) {
            $help = Help::where('user_id', $customer->id)
                ->where('title', $helpData['title'])
                ->first();

            if (!$help) {
                $help = Help::create([
                    'user_id' => $customer->id,
                    'city_id' => $customer->city_id,
                    'title' => $helpData['title'],
                    'description' => 'Test bantuan dengan admin fee untuk dashboard',
                    'amount' => $helpData['amount'],
                    'admin_fee' => $helpData['admin_fee'],
                    'total_amount' => $helpData['amount'] + $helpData['admin_fee'],
                    'status' => 'selesai',
                    'mitra_id' => $mitra->id,
                    'created_at' => Carbon::now()->subDays($helpData['days_ago']),
                    'updated_at' => Carbon::now()->subDays($helpData['days_ago'] - 1),
                ]);
            }
        }

        // Create some completed top-up transactions with admin fees
        $topups = [
            [
                'amount' => 50000,
                'admin_fee' => 7500, // Tier 2 fee
                'days_ago' => 3,
            ],
            [
                'amount' => 100000,
                'admin_fee' => 7500, // Tier 2 fee
                'days_ago' => 7,
            ],
            [
                'amount' => 200000,
                'admin_fee' => 6000, // 3% of 200000
                'days_ago' => 12,
            ],
        ];

        foreach ($topups as $topupData) {
            BalanceTransaction::create([
                'user_id' => $customer->id,
                'amount' => $topupData['amount'],
                'admin_fee' => $topupData['admin_fee'],
                'total_payment' => $topupData['amount'] + $topupData['admin_fee'],
                'type' => 'topup',
                'description' => 'Top-up saldo test dengan admin fee',
                'status' => 'completed',
                'payment_method' => 'bank_bca',
                'created_at' => Carbon::now()->subDays($topupData['days_ago']),
                'updated_at' => Carbon::now()->subDays($topupData['days_ago']),
                'processed_at' => Carbon::now()->subDays($topupData['days_ago']),
            ]);
        }

        $this->command->info('Admin fee test data created successfully!');
        $this->command->info('Total help admin fees: Rp ' . number_format(array_sum(array_column($helps, 'admin_fee')), 0, ',', '.'));
        $this->command->info('Total topup admin fees: Rp ' . number_format(array_sum(array_column($topups, 'admin_fee')), 0, ',', '.'));
        $this->command->info('Grand total admin fees: Rp ' . number_format(
            array_sum(array_column($helps, 'admin_fee')) + array_sum(array_column($topups, 'admin_fee')),
            0,
            ',',
            '.'
        ));
    }
}
