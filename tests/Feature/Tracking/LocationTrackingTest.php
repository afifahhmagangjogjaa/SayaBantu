<?php

namespace Tests\Feature\Tracking;

use App\Enums\HelpStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use App\Services\LocationTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Perilaku layanan GPS: simpan titik awal, ubah status saat mitra bergerak,
 * dan tolak update untuk order yang sudah tidak aktif.
 */
class LocationTrackingTest extends TestCase
{
    use RefreshDatabase;

    private const CUSTOMER_LAT = -7.7956;

    private const CUSTOMER_LNG = 110.3695;

    private function makeHelp(string $status = HelpStatus::MEMPEROLEH_MITRA->value): Help
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $mitra = User::factory()->create(['role' => 'mitra', 'status' => 'active', 'verified' => true]);
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test GPS',
            'amount' => 50000,
            'status' => $status,
            'mitra_id' => $mitra->id,
            'latitude' => self::CUSTOMER_LAT,
            'longitude' => self::CUSTOMER_LNG,
        ]);
    }

    public function test_update_pertama_menyimpan_lokasi_awal(): void
    {
        $help = $this->makeHelp();
        $service = app(LocationTrackingService::class);

        $result = $service->updatePartnerLocation($help, -7.7900, 110.3600);

        $this->assertTrue($result['success']);
        $help->refresh();
        $this->assertSame(-7.7900, (float) $help->partner_initial_lat);
        $this->assertSame(110.3600, (float) $help->partner_initial_lng);
        $this->assertSame(HelpStatus::MEMPEROLEH_MITRA->value, $help->status);
    }

    public function test_mitra_bergerak_jauh_mengubah_status_menjadi_menuju_lokasi(): void
    {
        $help = $this->makeHelp();
        $service = app(LocationTrackingService::class);

        // titik awal
        $service->updatePartnerLocation($help, -7.7900, 110.3600);

        // geser ~1 km (jauh di atas ambang 20 meter)
        $result = $service->updatePartnerLocation($help, -7.8000, 110.3700);

        $help->refresh();
        $this->assertTrue($result['status_changed']);
        $this->assertSame(HelpStatus::PARTNER_ON_THE_WAY->value, $help->status);
        $this->assertNotNull($help->partner_started_moving_at);
    }

    public function test_update_ditolak_untuk_order_yang_sudah_selesai(): void
    {
        $help = $this->makeHelp(HelpStatus::SELESAI->value);
        $service = app(LocationTrackingService::class);

        $result = $service->updatePartnerLocation($help, -7.7900, 110.3600);

        $this->assertFalse($result['success']);
        $this->assertNull($help->fresh()->partner_current_lat);
    }

    public function test_hitung_jarak_mendekati_nol_untuk_titik_yang_sama(): void
    {
        $service = app(LocationTrackingService::class);

        $distance = $service->calculateDistance(-7.7956, 110.3695, -7.7956, 110.3695);

        $this->assertSame(0.0, round($distance, 3));
    }
}
