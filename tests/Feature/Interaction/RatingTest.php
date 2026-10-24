<?php

namespace Tests\Feature\Interaction;

use App\Enums\HelpStatus;
use App\Livewire\Customer\Helps\Detail;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function makeHelp(User $customer, User $mitra, string $status): Help
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test rating',
            'amount' => 50000,
            'status' => $status,
            'mitra_id' => $mitra->id,
        ]);
    }

    public function test_customer_bisa_memberi_rating_untuk_order_selesai(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value);

        $this->actingAs($customer);

        Livewire::test(Detail::class, ['id' => $help->id])
            ->set('rating', 5)
            ->set('review', 'Kerja bagus dan cepat')
            ->call('submitRating')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ratings', [
            'help_id' => $help->id,
            'rater_id' => $customer->id,
            'ratee_id' => $mitra->id,
            'type' => 'customer_to_mitra',
            'rating' => 5,
        ]);
    }

    public function test_rating_ditolak_untuk_order_yang_belum_selesai(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra, HelpStatus::IN_PROGRESS->value);

        $this->actingAs($customer);

        Livewire::test(Detail::class, ['id' => $help->id])
            ->set('rating', 4)
            ->call('submitRating');

        $this->assertSame(0, Rating::count());
    }

    public function test_rating_ganda_dari_customer_yang_sama_ditolak(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value);

        Rating::create([
            'help_id' => $help->id,
            'user_id' => $customer->id,
            'mitra_id' => $mitra->id,
            'rater_id' => $customer->id,
            'ratee_id' => $mitra->id,
            'type' => 'customer_to_mitra',
            'rating' => 5,
        ]);

        $this->actingAs($customer);

        Livewire::test(Detail::class, ['id' => $help->id])
            ->set('rating', 3)
            ->call('submitRating');

        $this->assertSame(1, Rating::count());
        $this->assertSame(5, (int) Rating::first()->rating);
    }

    public function test_rating_di_luar_skala_ditolak(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value);

        $this->actingAs($customer);

        Livewire::test(Detail::class, ['id' => $help->id])
            ->set('rating', 9)
            ->call('submitRating')
            ->assertHasErrors('rating');

        $this->assertSame(0, Rating::count());
    }
}
