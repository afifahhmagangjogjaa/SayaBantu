<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use App\Services\NotificationUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationRedirectTest extends TestCase
{
    use RefreshDatabase;

    private function createHelp(User $customer, ?User $mitra = null): Help
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test notification',
            'amount' => 50000,
            'status' => 'in_progress',
            'mitra_id' => $mitra?->id,
        ]);
    }

    public function test_customer_can_read_and_redirect_to_help_detail(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $help = $this->createHelp($customer);

        $notificationId = (string) Str::uuid();
        $customer->notifications()->create([
            'id' => $notificationId,
            'type' => 'App\Notifications\HelpStatusNotification',
            'data' => [
                'type' => 'help_status',
                'help_id' => $help->id,
                'new_status' => 'partner_on_the_way',
                'url' => 'http://127.0.0.1:8000/customer/helps/' . $help->id . '/detail',
            ],
            'read_at' => null,
        ]);

        $this->actingAs($customer);

        Livewire::test(\App\Livewire\Customer\Notifications\Index::class)
            ->call('readAndRedirect', $notificationId)
            ->assertRedirect(route('customer.helps.detail', $help->id));

        $this->assertNotNull($customer->notifications()->find($notificationId)->read_at);
    }

    public function test_mitra_can_read_and_redirect_to_help_detail(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $mitra = User::factory()->create(['role' => 'mitra']);
        $help = $this->createHelp($customer, $mitra);

        $notificationId = (string) Str::uuid();
        $mitra->notifications()->create([
            'id' => $notificationId,
            'type' => 'App\Notifications\NewHelpRequestNotification',
            'data' => [
                'type' => 'help_request',
                'help_id' => $help->id,
            ],
            'read_at' => null,
        ]);

        $this->actingAs($mitra);

        Livewire::test(\App\Livewire\Mitra\Notifications\Index::class)
            ->call('readAndRedirect', $notificationId)
            ->assertRedirect(route('mitra.helps.detail', $help->id));

        $this->assertNotNull($mitra->notifications()->find($notificationId)->read_at);
    }

    public function test_admin_can_read_and_redirect_without_domain_leak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $notificationId = (string) Str::uuid();
        $admin->notifications()->create([
            'id' => $notificationId,
            'type' => 'App\Notifications\NewTopupRequest',
            'data' => [
                'type' => 'new_topup_request',
                'url' => 'http://127.0.0.1:8000/admin/topup/approvals',
            ],
            'read_at' => null,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\NotificationsIndex::class)
            ->call('readAndRedirect', $notificationId)
            ->assertRedirect(route('admin.topup.approvals'));

        $this->assertNotNull($admin->notifications()->find($notificationId)->read_at);
    }

    public function test_superadmin_can_open_notification_and_redirect_correctly(): void
    {
        $superadmin = User::factory()->create(['role' => 'super_admin']);

        $notificationId = (string) Str::uuid();
        $superadmin->notifications()->create([
            'id' => $notificationId,
            'type' => 'App\Notifications\NewTopupRequest',
            'data' => [
                'type' => 'new_topup_request',
                'url' => 'http://127.0.0.1:8000/superadmin/topup/approvals',
            ],
            'read_at' => null,
        ]);

        $this->actingAs($superadmin);

        Livewire::test(\App\Livewire\SuperAdmin\Notifications::class)
            ->call('openNotification', $notificationId)
            ->assertRedirect(route('superadmin.topup.approvals'));

        $this->assertNotNull($superadmin->notifications()->find($notificationId)->read_at);

        // Also test SuperAdmin NotificationDropdown
        $notifId2 = (string) Str::uuid();
        $superadmin->notifications()->create([
            'id' => $notifId2,
            'type' => 'App\Notifications\NewWithdrawRequest',
            'data' => [
                'type' => 'new_withdraw_request',
            ],
            'read_at' => null,
        ]);

        Livewire::test(\App\Livewire\SuperAdmin\NotificationDropdown::class)
            ->call('openNotification', $notifId2)
            ->assertRedirect(route('superadmin.withdraws.index'));

        $this->assertNotNull($superadmin->notifications()->find($notifId2)->read_at);
    }

    public function test_url_resolver_normalizes_outdated_hosts(): void
    {
        $normalized = NotificationUrlResolver::normalizeUrl('http://127.0.0.1:8000/customer/dashboard', '/fallback');
        $this->assertEquals(url('/customer/dashboard'), $normalized);

        $normalizedLocal = NotificationUrlResolver::normalizeUrl('http://localhost/admin/verifications', '/fallback');
        $this->assertEquals(url('/admin/verifications'), $normalizedLocal);

        $emptyFallback = NotificationUrlResolver::normalizeUrl('#', '/fallback');
        $this->assertEquals('/fallback', $emptyFallback);
    }
}
