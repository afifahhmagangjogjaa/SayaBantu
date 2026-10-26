<?php

namespace Tests\Feature\Smoke;

use App\Enums\HelpStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test halaman customer yang menampilkan status bantuan.
 */
class CustomerPagesTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function makeHelp(User $customer, string $status, ?int $mitraId = null): Help
    {
        $city = City::create(['name' => 'Kota Test', 'province' => 'DI Yogyakarta', 'is_active' => true]);
        $category = Category::create(['name' => 'Bersih-bersih', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih rumah',
            'description' => 'Deskripsi singkat untuk test.',
            'amount' => 50000,
            'status' => $status,
            'mitra_id' => $mitraId,
        ]);
    }

    public function test_halaman_daftar_dan_detail_bantuan_customer_bisa_dibuka(): void
    {
        $customer = $this->customer();
        $mitra = User::factory()->create(['role' => 'mitra', 'status' => 'active', 'verified' => true]);

        $menunggu = $this->makeHelp($customer, HelpStatus::MENUNGGU_MITRA->value);
        $berjalan = $this->makeHelp($customer, HelpStatus::PARTNER_ON_THE_WAY->value, $mitra->id);
        $selesai = $this->makeHelp($customer, HelpStatus::SELESAI->value, $mitra->id);
        // baris legacy 'taken' harus tetap tampil di tab diproses
        $legacy = $this->makeHelp($customer, 'taken', $mitra->id);

        $this->actingAs($customer)->get(route('customer.helps.index'))->assertSuccessful();

        foreach ([$menunggu, $berjalan, $selesai, $legacy] as $help) {
            $this->actingAs($customer)
                ->get(route('customer.helps.detail', $help->id))
                ->assertSuccessful();
        }

        $this->actingAs($customer)->get(route('customer.dashboard'))->assertSuccessful();
    }
}
