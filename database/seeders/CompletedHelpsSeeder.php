<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Help;
use App\Models\User;

class CompletedHelpsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get budi@example.com user (customer)
        $customer = User::where('email', 'budi@example.com')->first();
        
        if (!$customer) {
            $this->command->error('User budi@example.com not found. Run UserSeeder first.');
            return;
        }

        // Get a mitra user
        $mitra = User::where('role', 'mitra')->first();
        
        if (!$mitra) {
            $this->command->error('No mitra user found. Run UserSeeder first.');
            return;
        }

        // Update some existing helps to completed status
        $helps = Help::where('user_id', $customer->id)
            ->where('status', '!=', 'selesai')
            ->take(3)
            ->get();

        if ($helps->count() === 0) {
            $this->command->warn('No helps found for customer. Run HelpsSeeder first.');
            return;
        }

        foreach ($helps as $help) {
            $help->update([
                'status' => 'selesai',
                'mitra_id' => $mitra->id,
                'updated_at' => now()->subDays(rand(1, 7)),
            ]);
        }

        $this->command->info("Successfully marked {$helps->count()} helps as completed for customer {$customer->name}");
    }
}
