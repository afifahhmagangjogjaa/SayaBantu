<?php

namespace Tests\Feature\Interaction;

use App\Livewire\Admin\TopupApproval;
use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TopupApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ]);
    }

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

    private function pendingTopup(User $customer, int $amount = 100000): BalanceTransaction
    {
        return BalanceTransaction::create([
            'user_id' => $customer->id,
            'amount' => $amount,
            'type' => 'topup',
            'status' => 'waiting_approval',
            'description' => 'Topup test',
        ]);
    }

    public function test_admin_menyetujui_topup_dan_saldo_bertambah(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();
        $topup = $this->pendingTopup($customer, 100000);

        $this->actingAs($admin);

        Livewire::test(TopupApproval::class)->call('approve', $topup->id);

        $topup->refresh();
        $this->assertSame('completed', $topup->status);
        $this->assertSame($admin->id, $topup->approved_by);

        // Saldo dihitung ulang dari transaksi completed.
        $this->assertSame(100000.0, (float) UserBalance::recalculateForUser($customer->id));
        $this->assertSame(100000, $customer->fresh()->balance);
    }

    public function test_topup_yang_sudah_diproses_tidak_bisa_disetujui_lagi(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();
        $topup = $this->pendingTopup($customer, 50000);

        $this->actingAs($admin);
        Livewire::test(TopupApproval::class)->call('approve', $topup->id);
        Livewire::test(TopupApproval::class)->call('approve', $topup->id);

        $this->assertSame(
            1,
            BalanceTransaction::where('user_id', $customer->id)->where('status', 'completed')->count()
        );
        $this->assertSame(50000, $customer->fresh()->balance);
    }

    public function test_admin_menolak_topup_tanpa_menambah_saldo(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();
        $topup = $this->pendingTopup($customer, 75000);

        $this->actingAs($admin);

        Livewire::test(TopupApproval::class)
            ->call('viewDetail', $topup->id)
            ->set('rejectionReason', 'Bukti transfer tidak sesuai')
            ->call('reject');

        $topup->refresh();
        $this->assertSame('rejected', $topup->status);
        $this->assertSame('Bukti transfer tidak sesuai', $topup->rejection_reason);
        $this->assertSame(0, $customer->fresh()->balance);
    }

    public function test_penolakan_tanpa_alasan_ditolak_validasi(): void
    {
        $admin = $this->admin();
        $customer = $this->customer();
        $topup = $this->pendingTopup($customer, 20000);

        $this->actingAs($admin);

        Livewire::test(TopupApproval::class)
            ->call('viewDetail', $topup->id)
            ->set('rejectionReason', '')
            ->call('reject')
            ->assertHasErrors('rejectionReason');

        $this->assertSame('waiting_approval', $topup->fresh()->status);
    }
}
