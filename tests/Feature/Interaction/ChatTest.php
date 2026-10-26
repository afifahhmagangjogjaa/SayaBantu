<?php

namespace Tests\Feature\Interaction;

use App\Enums\HelpStatus;
use App\Livewire\Customer\Chat;
use App\Models\Category;
use App\Models\Chat as ChatModel;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatTest extends TestCase
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

    private function makeHelp(User $customer, User $mitra, string $status = HelpStatus::IN_PROGRESS->value): Help
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test chat',
            'amount' => 50000,
            'status' => $status,
            'mitra_id' => $mitra->id,
        ]);
    }

    public function test_customer_bisa_mengirim_pesan(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra);

        $this->actingAs($customer);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Halo, saya sudah di lokasi')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('chats', [
            'help_id' => $help->id,
            'customer_id' => $customer->id,
            'sender_type' => 'customer',
            'message' => 'Halo, saya sudah di lokasi',
        ]);

        $this->assertSame(1, ChatModel::where('help_id', $help->id)->count());
    }

    public function test_pesan_kosong_ditolak(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra);

        $this->actingAs($customer);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', '')
            ->call('sendMessage')
            ->assertHasErrors('message');

        $this->assertSame(0, ChatModel::count());
    }

    public function test_pesan_ditolak_untuk_order_yang_sudah_selesai(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value);

        $this->actingAs($customer);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Masih bisa kirim?')
            ->call('sendMessage')
            ->assertDispatched('error');

        $this->assertSame(0, ChatModel::count());
    }
}
