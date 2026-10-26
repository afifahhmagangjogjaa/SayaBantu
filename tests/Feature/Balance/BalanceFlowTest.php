<?php

namespace Tests\Feature\Balance;

use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\WithdrawRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menjaga invarian uang: saldo diturunkan dari transaksi berstatus completed,
 * tidak pernah negatif, dan penarikan hanya memotong saldo saat disetujui admin.
 */
class BalanceFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'mitra',
            'status' => 'active',
            'verified' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function makeTransaction(User $user, int $amount, string $type, string $status = 'completed'): BalanceTransaction
    {
        return BalanceTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type,
            'status' => $status,
            'description' => 'test',
            'processed_at' => $status === 'completed' ? now() : null,
        ]);
    }

    public function test_saldo_dihitung_dari_transaksi_completed(): void
    {
        $user = $this->makeUser();

        $this->makeTransaction($user, 100000, 'topup');
        $this->makeTransaction($user, 30000, 'deduction');

        $balance = UserBalance::recalculateForUser($user->id);

        $this->assertSame(70000.0, $balance);
        $this->assertSame(70000.0, (float) UserBalance::where('user_id', $user->id)->value('balance'));
        $this->assertSame(70000.0, (float) $user->fresh()->balance);
    }

    public function test_transaksi_belum_completed_tidak_dihitung(): void
    {
        $user = $this->makeUser();

        $this->makeTransaction($user, 50000, 'topup', 'pending');
        $this->makeTransaction($user, 20000, 'topup', 'rejected');

        $this->assertSame(0.0, UserBalance::recalculateForUser($user->id));
    }

    public function test_saldo_tidak_pernah_negatif(): void
    {
        $user = $this->makeUser();

        $this->makeTransaction($user, 50000, 'deduction');

        $this->assertSame(0.0, UserBalance::recalculateForUser($user->id));
    }

    public function test_adjust_balance_mencatat_transaksi_dan_memperbarui_saldo(): void
    {
        $user = $this->makeUser();

        $user->adjustBalance(20000);
        $this->assertSame(20000, $user->fresh()->balance);

        $user->adjustBalance(-5000);
        $this->assertSame(15000, $user->fresh()->balance);

        $this->assertSame(
            ['topup', 'deduction'],
            BalanceTransaction::where('user_id', $user->id)->orderBy('id')->pluck('type')->all()
        );

        $this->assertSame(
            ['completed', 'completed'],
            BalanceTransaction::where('user_id', $user->id)->orderBy('id')->pluck('status')->all()
        );
    }

    public function test_pengajuan_withdraw_belum_memotong_saldo(): void
    {
        $user = $this->makeUser();

        $this->makeTransaction($user, 100000, 'topup');
        UserBalance::recalculateForUser($user->id);

        WithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => 40000,
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'status' => WithdrawRequest::STATUS_PENDING,
        ]);

        // Saldo baru berkurang saat admin menyetujui, bukan saat pengajuan dibuat.
        $this->assertSame(100000, $user->fresh()->balance);
    }

    public function test_approve_withdraw_memotong_saldo_dan_menandai_success(): void
    {
        $mitra = $this->makeUser();

        $this->makeTransaction($mitra, 100000, 'topup');
        UserBalance::recalculateForUser($mitra->id);

        $withdraw = WithdrawRequest::create([
            'user_id' => $mitra->id,
            'amount' => 40000,
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'status' => WithdrawRequest::STATUS_PENDING,
        ]);

        $superAdmin = $this->makeUser([
            'role' => 'super_admin',
            'email' => 'super@example.test',
        ]);

        $response = $this->actingAs($superAdmin)->post(route('superadmin.withdraws.approve', $withdraw), [
            'transfer_reference' => 'TRX-001',
        ]);

        $response->assertRedirect(route('superadmin.withdraws.index'));

        $this->assertSame(60000, $mitra->fresh()->balance);
        $this->assertSame(WithdrawRequest::STATUS_SUCCESS, $withdraw->fresh()->status);
        $this->assertSame('TRX-001', $withdraw->fresh()->external_id);

        $this->assertSame(
            40000.0,
            (float) BalanceTransaction::where('user_id', $mitra->id)
                ->where('type', 'deduction')
                ->where('status', 'completed')
                ->sum('amount')
        );
    }

    public function test_approve_withdraw_ditolak_bila_saldo_tidak_cukup(): void
    {
        $mitra = $this->makeUser();

        $this->makeTransaction($mitra, 10000, 'topup');
        UserBalance::recalculateForUser($mitra->id);

        $withdraw = WithdrawRequest::create([
            'user_id' => $mitra->id,
            'amount' => 50000,
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'status' => WithdrawRequest::STATUS_PENDING,
        ]);

        $superAdmin = $this->makeUser([
            'role' => 'super_admin',
            'email' => 'super2@example.test',
        ]);

        $this->actingAs($superAdmin)->post(route('superadmin.withdraws.approve', $withdraw))
            ->assertSessionHasErrors('general');

        $this->assertSame(10000, $mitra->fresh()->balance);
        $this->assertSame(WithdrawRequest::STATUS_PENDING, $withdraw->fresh()->status);
    }
}
