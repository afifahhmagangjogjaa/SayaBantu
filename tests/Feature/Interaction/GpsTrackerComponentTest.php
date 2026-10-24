<?php

namespace Tests\Feature\Interaction;

use App\Livewire\Mitra\GpsTracker;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Komponen GPS mitra: hanya mitra yang mengerjakan order (atau admin) yang boleh
 * mengirim posisi, dan status hanya berubah untuk order yang masih berjalan.
 */
class GpsTrackerComponentTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $mitra;

    private User $mitraLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $this->mitra = User::factory()->create([
            'role' => 'mitra', 'status' => 'active', 'verified' => true,
            'is_completed' => true, 'email_verified_at' => now(),
        ]);
        $this->mitraLain = User::factory()->create([
            'role' => 'mitra', 'status' => 'active', 'verified' => true,
            'is_completed' => true, 'email_verified_at' => now(), 'email' => 'lain@example.test',
        ]);
    }

    private function makeHelp(string $status = 'partner_on_the_way', ?User $mitra = null): Help
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $this->customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test komponen GPS',
            'amount' => 50000,
            'status' => $status,
            'mitra_id' => ($mitra ?? $this->mitra)->id,
            'latitude' => -7.7956,
            'longitude' => 110.3695,
        ]);
    }

    public function test_komponen_aktif_untuk_order_yang_sedang_berjalan(): void
    {
        $help = $this->makeHelp('partner_on_the_way');

        $this->actingAs($this->mitra);

        Livewire::test(GpsTracker::class, ['helpId' => $help->id])
            ->assertSet('isTracking', true)
            ->assertSet('currentStatus', 'partner_on_the_way');
    }

    public function test_komponen_tidak_aktif_untuk_order_selesai(): void
    {
        $help = $this->makeHelp('selesai');

        $this->actingAs($this->mitra);

        Livewire::test(GpsTracker::class, ['helpId' => $help->id])
            ->assertSet('isTracking', false);
    }

    public function test_update_lokasi_menyimpan_koordinat_dan_mengubah_status(): void
    {
        $help = $this->makeHelp('memperoleh_mitra');

        $this->actingAs($this->mitra);

        $component = Livewire::test(GpsTracker::class, ['helpId' => $help->id]);

        // titik awal
        $component->call('updateLocation', -7.7900, 110.3600);
        $this->assertNotNull($help->fresh()->partner_initial_lat);

        // bergerak jauh (> 20 m)
        $component->call('updateLocation', -7.8000, 110.3700);

        $help->refresh();
        $this->assertSame(-7.8000, (float) $help->partner_current_lat);
        $this->assertSame('partner_on_the_way', $help->status);
        $this->assertNotNull($help->partner_started_moving_at);
    }

    public function test_update_lokasi_tidak_berpengaruh_untuk_order_selesai(): void
    {
        $help = $this->makeHelp('selesai');

        $this->actingAs($this->mitra);

        Livewire::test(GpsTracker::class, ['helpId' => $help->id])
            ->call('updateLocation', -7.8000, 110.3700);

        $this->assertNull($help->fresh()->partner_current_lat);
    }

    public function test_mitra_lain_tidak_bisa_mengirim_lokasi(): void
    {
        $help = $this->makeHelp('partner_on_the_way');

        $this->actingAs($this->mitraLain);

        $tracking = Livewire::test(GpsTracker::class, ['helpId' => $help->id])
            ->call('updateLocation', -7.8000, 110.3700)
            ->get('isTracking');

        $this->assertFalse($tracking);
        $this->assertNull($help->fresh()->partner_current_lat);
    }

    public function test_tanpa_login_tidak_bisa_mengirim_lokasi(): void
    {
        $help = $this->makeHelp('partner_on_the_way');

        Livewire::test(GpsTracker::class, ['helpId' => $help->id])
            ->call('updateLocation', -7.8000, 110.3700);

        $this->assertNull($help->fresh()->partner_current_lat);
    }
}
