<?php

namespace Tests\Feature\Api;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Endpoint proxy wilayah harus mengutamakan master lokal, sehingga tetap
 * berjalan tanpa jaringan.
 */
class RegionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_districts_diambil_dari_master_lokal(): void
    {
        // Master lokal memakai beberapa bentuk key: id kota, kode bertitik, dan nama.
        $city = City::create(['name' => 'Jakarta Pusat', 'province' => 'DKI JAKARTA', 'is_active' => true]);

        $response = $this->getJson('/api/wilayah/districts?city_id='.$city->id);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_districts_bisa_dicari_dengan_nama_kota(): void
    {
        // Master lokal menyimpan sebagian data dengan key nama kota (mis. "JAKARTA").
        $city = City::create([
            'name' => 'Jakarta',
            'province' => 'DKI JAKARTA',
            'code' => '31.71',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/wilayah/districts?city_name='.urlencode($city->name));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_villages_diambil_dari_master_lokal(): void
    {
        $response = $this->getJson('/api/wilayah/villages?district_code=31.71.01');

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_villages_tanpa_parameter_mengembalikan_kosong(): void
    {
        $this->getJson('/api/wilayah/villages')
            ->assertOk()
            ->assertJson(['success' => false, 'data' => []]);
    }

    public function test_districts_dengan_kota_tak_dikenal_tidak_error(): void
    {
        $this->getJson('/api/wilayah/districts?city_id=99999')
            ->assertOk()
            ->assertJsonStructure(['success', 'data']);
    }
}
