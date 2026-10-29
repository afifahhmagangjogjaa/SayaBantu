<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Autocomplete alamat: validasi input, pemetaan hasil, dan cache.
 * Semua panggilan jaringan di-fake supaya test tidak bergantung internet.
 */
class AddressAutocompleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Cache array dipakai agar tidak menyentuh DB/cache store nyata
        config(['cache.default' => 'array']);
    }

    public function test_query_kurang_dari_tiga_karakter_mengembalikan_kosong(): void
    {
        Http::fake();

        $this->getJson('/api/address/autocomplete?q=ma')
            ->assertOk()
            ->assertExactJson([]);

        Http::assertNothingSent();
    }

    public function test_hasil_nominatim_dipetakan_ke_format_aplikasi(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '-7.797068',
                    'lon' => '110.370529',
                    'display_name' => 'Malioboro, Yogyakarta, Daerah Istimewa Yogyakarta, Indonesia',
                    'address' => [
                        'road' => 'Jalan Malioboro',
                        'city_district' => 'Gondomanan',
                        'city' => 'Yogyakarta',
                        'state' => 'Daerah Istimewa Yogyakarta',
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/address/autocomplete?q=malioboro');

        $response->assertOk();
        $data = $response->json();

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('title', $data[0]);
        $this->assertArrayHasKey('full_address', $data[0]);
        $this->assertSame(-7.797068, $data[0]['lat']);
        $this->assertSame(110.370529, $data[0]['lng']);
    }

    public function test_hasil_disimpan_di_cache(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '-7.8',
                    'lon' => '110.3',
                    'display_name' => 'Suatu Tempat, Yogyakarta',
                    'address' => ['road' => 'Suatu Tempat', 'city' => 'Yogyakarta'],
                ],
            ], 200),
        ]);

        $this->getJson('/api/address/autocomplete?q=suatu+tempat')->assertOk();
        $this->getJson('/api/address/autocomplete?q=suatu+tempat')->assertOk();

        // Panggilan kedua harus dilayani cache, bukan HTTP lagi.
        Http::assertSentCount(1);
    }

    public function test_nominatim_gagal_jatuh_ke_photon(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 500),
            'photon.komoot.io/*' => Http::response([
                'features' => [
                    [
                        'geometry' => ['coordinates' => [110.36, -7.80]],
                        'properties' => [
                            'name' => 'Gedung Agung',
                            'street' => 'Jalan Malioboro',
                            'city' => 'Yogyakarta',
                            'state' => 'Daerah Istimewa Yogyakarta',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/address/autocomplete?q=gedung+agung');

        $response->assertOk();
        $this->assertNotEmpty($response->json());
    }
}
