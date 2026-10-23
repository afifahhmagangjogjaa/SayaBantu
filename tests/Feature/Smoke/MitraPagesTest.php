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
 * Smoke test halaman mitra yang memakai filter status HelpStatus.
 * Tujuannya menangkap error runtime (query/tipe) yang tidak tercakup test unit.
 */
class MitraPagesTest extends TestCase
{
    use RefreshDatabase;

    private function mitra(): User
    {
        return User::factory()->create([
            'role' => 'mitra',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function makeHelp(User $customer, User $mitra, string $status): Help
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
            'mitra_id' => $mitra->id,
        ]);
    }

    public function test_halaman_daftar_mitra_bisa_dibuka(): void
    {
        $mitra = $this->mitra();
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        $this->makeHelp($customer, $mitra, HelpStatus::MENUNGGU_MITRA->value);
        $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value);
        // baris legacy: status lama harus tetap terbaca oleh filter
        $this->makeHelp($customer, $mitra, 'completed');

        $this->actingAs($mitra)->get(route('mitra.helps.all'))->assertSuccessful();
        $this->actingAs($mitra)->get(route('mitra.helps.completed'))->assertSuccessful();
        $this->actingAs($mitra)->get(route('mitra.helps.processing'))->assertSuccessful();
    }

    public function test_halaman_detail_mitra_bisa_dibuka(): void
    {
        $mitra = $this->mitra();
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);

        $help = $this->makeHelp($customer, $mitra, HelpStatus::PARTNER_ARRIVED->value);

        $this->actingAs($mitra)
            ->get(route('mitra.helps.detail', $help->id))
            ->assertSuccessful();
    }
}
