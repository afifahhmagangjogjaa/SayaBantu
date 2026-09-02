<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get target users (super_admin and admin)
        $superAdmin = User::where('role', 'super_admin')->first();
        $admin = User::where('role', 'admin')->first();

        $targetUsers = collect([$superAdmin, $admin])->filter();

        if ($targetUsers->isEmpty()) {
            $this->command->warn('No admin or super admin user found.');
            return;
        }

        foreach ($targetUsers as $targetUser) {
            $notifications = [
                [
                    'type' => 'App\Notifications\NewTopupRequest',
                    'data' => [
                        'type' => 'new_topup_request',
                        'title' => 'Request Top-Up Saldo Baru',
                        'customer_name' => 'Budi Santoso',
                        'customer_id' => 3,
                        'amount' => 150000,
                        'request_code' => 'TPU-' . date('Ymd') . '-001',
                        'message' => 'Request top-up baru dari Budi Santoso sebesar Rp 150.000 menunggu verifikasi',
                        'url' => route('superadmin.topup.approvals'),
                    ],
                    'read_at' => null,
                    'minutes_ago' => 5,
                ],
                [
                    'type' => 'App\Notifications\NewWithdrawRequest',
                    'data' => [
                        'type' => 'new_withdraw_request',
                        'title' => 'Permintaan Tarik Saldo Baru',
                        'user_name' => 'Ahmad Relawan',
                        'user_id' => 4,
                        'amount' => 200000,
                        'bank_code' => 'BCA',
                        'account_number' => '8830192831',
                        'message' => 'Permintaan tarik saldo baru dari Ahmad Relawan sebesar Rp 200.000 (BCA)',
                        'url' => route('superadmin.withdraws.index'),
                    ],
                    'read_at' => null,
                    'minutes_ago' => 25,
                ],
                [
                    'type' => 'App\Notifications\NewRegistrationNotification',
                    'data' => [
                        'type' => 'new_registration',
                        'title' => 'Pendaftaran Pengguna Baru',
                        'user_name' => 'Siti Nurhaliza',
                        'user_id' => 9,
                        'user_role' => 'mitra',
                        'message' => 'Pengguna baru telah mendaftar: Siti Nurhaliza (Mitra)',
                        'url' => route('superadmin.users'),
                    ],
                    'read_at' => null,
                    'minutes_ago' => 60,
                ],
                [
                    'type' => 'App\Notifications\CustomNotification',
                    'data' => [
                        'type' => 'help_status',
                        'title' => 'Bantuan Selesai',
                        'message' => 'Bantuan "Antar Obat ke RS" telah diselesaikan oleh Mitra Ahmad Relawan',
                        'url' => route('superadmin.helps.approved'),
                    ],
                    'read_at' => now()->subHours(2),
                    'minutes_ago' => 120,
                ],
                [
                    'type' => 'App\Notifications\NewTopupRequest',
                    'data' => [
                        'type' => 'new_topup_request',
                        'title' => 'Request Top-Up Saldo Baru',
                        'customer_name' => 'Dwiki Kurniawan',
                        'customer_id' => 12,
                        'amount' => 50000,
                        'request_code' => 'TPU-' . date('Ymd') . '-002',
                        'message' => 'Request top-up baru dari Dwiki Kurniawan sebesar Rp 50.000',
                        'url' => route('superadmin.topup.approvals'),
                    ],
                    'read_at' => now()->subDay(),
                    'minutes_ago' => 1440,
                ],
            ];

            foreach ($notifications as $notificationData) {
                DB::table('notifications')->insert([
                    'id' => (string) Str::uuid(),
                    'type' => $notificationData['type'],
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $targetUser->id,
                    'data' => json_encode($notificationData['data']),
                    'read_at' => $notificationData['read_at'],
                    'created_at' => now()->subMinutes($notificationData['minutes_ago']),
                    'updated_at' => now()->subMinutes($notificationData['minutes_ago']),
                ]);
            }
        }

        $this->command->info('Admin and SuperAdmin test notifications seeded successfully!');
    }
}
