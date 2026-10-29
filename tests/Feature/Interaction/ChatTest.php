<?php

namespace Tests\Feature\Interaction;

use App\Enums\HelpStatus;
use App\Livewire\Customer\Chat;
use App\Livewire\Mitra\Chat as MitraChat;
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

    private function makeHelp(User $customer, User $mitra, string $status = HelpStatus::IN_PROGRESS->value, $completedAt = null): Help
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
            'completed_at' => $completedAt,
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

    public function test_pesan_ditolak_untuk_order_selesai_lebih_dari_sehari(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value, now()->subDays(2));

        $this->actingAs($customer);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Masih bisa kirim?')
            ->call('sendMessage')
            ->assertDispatched('error');

        $this->assertSame(0, ChatModel::count());
    }

    public function test_pesan_masih_bisa_dikirim_hingga_sehari_setelah_selesai(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value, now()->subHours(12));

        $this->actingAs($customer);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Terima kasih ya!')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('chats', [
            'help_id' => $help->id,
            'sender_type' => 'customer',
            'message' => 'Terima kasih ya!',
        ]);
    }

    public function test_pesan_ditolak_sebelum_mitra_menerima_pesanan(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra, HelpStatus::MENUNGGU_MITRA->value);

        $this->actingAs($customer);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Halo?')
            ->call('sendMessage')
            ->assertDispatched('error');

        $this->assertSame(0, ChatModel::count());
    }

    public function test_mitra_bisa_mengirim_pesan_hingga_sehari_setelah_selesai(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value, now()->subHours(12));

        $this->actingAs($mitra);

        Livewire::test(MitraChat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Sip, terima kasih!')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('chats', [
            'help_id' => $help->id,
            'sender_type' => 'mitra',
            'message' => 'Sip, terima kasih!',
        ]);
    }

    public function test_mitra_ditolak_mengirim_pesan_setelah_sehari_selesai(): void
    {
        $customer = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($customer, $mitra, HelpStatus::SELESAI->value, now()->subDays(2));

        $this->actingAs($mitra);

        Livewire::test(MitraChat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Masih ada?')
            ->call('sendMessage')
            ->assertDispatched('error');

        $this->assertSame(0, ChatModel::count());
    }

    public function test_customer_tidak_bisa_membuka_atau_mengirim_chat_pesan_orang_lain(): void
    {
        $owner = $this->customer();
        $intruder = $this->customer();
        $mitra = $this->mitra();
        $help = $this->makeHelp($owner, $mitra);

        $this->actingAs($intruder);

        Livewire::test(Chat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Pesan penembus')
            ->call('sendMessage')
            ->assertDispatched('error');

        $this->assertSame(0, ChatModel::count());
    }

    public function test_mitra_lain_tidak_bisa_mengirim_pesan_di_chat_mitra_lain(): void
    {
        $customer = $this->customer();
        $assignedMitra = $this->mitra();
        $otherMitra = $this->mitra();
        $help = $this->makeHelp($customer, $assignedMitra);

        $this->actingAs($otherMitra);

        Livewire::test(MitraChat::class)
            ->set('selected_help_id', $help->id)
            ->set('message', 'Pesan dari mitra asing')
            ->call('sendMessage')
            ->assertDispatched('error');

        $this->assertSame(0, ChatModel::count());
    }
}
