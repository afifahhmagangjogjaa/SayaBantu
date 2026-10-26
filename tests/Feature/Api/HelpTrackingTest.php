<?php

namespace Tests\Feature\Api;

use App\Enums\HelpStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Endpoint tracking hanya boleh dibaca pihak yang berkepentingan atas order.
 */
class HelpTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function makeHelp(User $customer, User $mitra): Help
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test tracking',
            'amount' => 50000,
            'status' => HelpStatus::PARTNER_ON_THE_WAY->value,
            'mitra_id' => $mitra->id,
            'latitude' => -7.7956,
            'longitude' => 110.3695,
            'partner_current_lat' => -7.7900,
            'partner_current_lng' => 110.3600,
        ]);
    }

    public function test_customer_pemilik_bisa_melihat_tracking(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra);

        $this->actingAs($customer)
            ->getJson(route('customer.helps.tracking', $help->id))
            ->assertOk()
            ->assertJson([
                'partnerLat' => -7.79,
                'partnerName' => $mitra->name,
            ]);
    }

    public function test_mitra_tidak_bisa_mengakses_route_tracking_customer(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra);

        // Route ini berada di grup customer; mitra dialihkan/ditolak sebelum
        // sempat membaca data order orang lain.
        $this->actingAs($mitra)
            ->get(route('customer.helps.tracking', $help->id))
            ->assertStatus(302);
    }

    public function test_customer_lain_tidak_boleh_melihat_tracking(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $lain = $this->user('customer', ['email' => 'lain@example.test']);
        $help = $this->makeHelp($customer, $mitra);

        $this->actingAs($lain)
            ->getJson(route('customer.helps.tracking', $help->id))
            ->assertForbidden();
    }

    public function test_customer_lain_tidak_boleh_melihat_detail_json(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $lain = $this->user('customer', ['email' => 'lain2@example.test']);
        $help = $this->makeHelp($customer, $mitra);

        $this->actingAs($lain)
            ->getJson(route('customer.helps.json', $help->id))
            ->assertForbidden();
    }

    public function test_help_tidak_ada_mengembalikan_404(): void
    {
        $customer = $this->user('customer');

        $this->actingAs($customer)
            ->getJson(route('customer.helps.tracking', 9999))
            ->assertNotFound();
    }
}
