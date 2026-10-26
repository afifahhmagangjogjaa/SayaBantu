<?php

namespace Tests\Feature\Commands;

use App\Enums\HelpStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Perilaku auto-cancel sekarang dijalankan oleh command terjadwal
 * (helps:auto-cancel), bukan lagi saat halaman dibuka.
 */
class AutoCancelHelpsTest extends TestCase
{
    use RefreshDatabase;

    private function makeHelp(array $attributes = []): Help
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create(array_merge([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'urgent',
            'title' => 'Butuh bantuan cepat',
            'description' => 'Test auto-cancel',
            'amount' => 20000,
            'status' => HelpStatus::MENUNGGU_MITRA->value,
            'auto_cancel_at' => now()->subMinutes(5),
        ], $attributes));
    }

    public function test_command_membatalkan_bantuan_urgent_yang_kedaluwarsa(): void
    {
        $expired = $this->makeHelp();

        $this->artisan('helps:auto-cancel')->assertSuccessful();

        $expired->refresh();
        $this->assertSame(HelpStatus::DIBATALKAN->value, $expired->status);
        $this->assertNotNull($expired->cancelled_at);
    }

    public function test_bantuan_yang_belum_kedaluwarsa_tidak_dibatalkan(): void
    {
        $aktif = $this->makeHelp(['auto_cancel_at' => now()->addMinutes(30)]);

        $this->artisan('helps:auto-cancel')->assertSuccessful();

        $this->assertSame(HelpStatus::MENUNGGU_MITRA->value, $aktif->fresh()->status);
    }

    public function test_bantuan_terjadwal_tidak_ikut_dibatalkan(): void
    {
        $terjadwal = $this->makeHelp([
            'help_type' => 'scheduled',
            'auto_cancel_at' => now()->subMinutes(5),
        ]);

        $this->artisan('helps:auto-cancel')->assertSuccessful();

        $this->assertSame(HelpStatus::MENUNGGU_MITRA->value, $terjadwal->fresh()->status);
    }

    public function test_bantuan_yang_sudah_diambil_mitra_tidak_dibatalkan(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'status' => 'active',
            'verified' => true,
        ]);

        $diambil = $this->makeHelp([
            'status' => HelpStatus::MEMPEROLEH_MITRA->value,
            'mitra_id' => $mitra->id,
        ]);

        $this->artisan('helps:auto-cancel')->assertSuccessful();

        $this->assertSame(HelpStatus::MEMPEROLEH_MITRA->value, $diambil->fresh()->status);
    }
}
