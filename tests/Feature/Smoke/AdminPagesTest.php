<?php

namespace Tests\Feature\Smoke;

use App\Enums\HelpStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_admin_bantuan_bisa_dibuka(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ]);

        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Bersih-bersih', 'is_active' => true]);

        $help = Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih rumah',
            'description' => 'Deskripsi singkat untuk test.',
            'amount' => 50000,
            'status' => HelpStatus::SELESAI->value,
        ]);

        $this->actingAs($admin)->get(route('admin.helps'))->assertSuccessful();
        $this->actingAs($admin)->get(route('admin.helps.show', $help->id))->assertSuccessful();
    }
}
