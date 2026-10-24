<?php

namespace Tests\Feature\Interaction;

use App\Models\BalanceTransaction;
use App\Models\City;
use App\Models\User;
use App\Models\WithdrawRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin wilayah boleh menyetujui/menolak penarikan saldo mitra di kota yang
 * dikelolanya, dan tidak boleh menyentuh withdraw di luar wilayahnya.
 * Super Admin tetap bisa memproses semua kota.
 */
class AdminWithdrawApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function makeCity(User $admin, string $name): City
    {
        return City::create([
            'name' => $name,
            'is_active' => true,
            'admin_id' => $admin->id,
        ]);
    }

    private function makeMitraWithBalance(City $city, int $balance = 200000): User
    {
        $mitra = $this->makeUser('mitra', ['city_id' => $city->id]);

        BalanceTransaction::create([
            'user_id' => $mitra->id,
            'amount' => $balance,
            'type' => 'topup',
            'status' => 'completed',
            'description' => 'Saldo awal pengujian',
            'processed_at' => now(),
        ]);

        return $mitra->fresh();
    }

    private function pendingWithdraw(User $mitra, int $amount = 150000): WithdrawRequest
    {
        return WithdrawRequest::create([
            'user_id' => $mitra->id,
            'amount' => $amount,
            'bank_code' => 'BCA',
            'account_number' => '1234567890',
            'status' => WithdrawRequest::STATUS_PENDING,
        ]);
    }

    public function test_admin_menyetujui_withdraw_mitra_di_wilayahnya(): void
    {
        $admin = $this->makeUser('admin');
        $city = $this->makeCity($admin, 'Kota Wilayah Admin');
        $mitra = $this->makeMitraWithBalance($city, 200000);
        $withdraw = $this->pendingWithdraw($mitra, 150000);

        $this->actingAs($admin)
            ->post(route('admin.withdraws.approve', $withdraw), [
                'transfer_reference' => 'TRF-001',
                'note' => 'Sudah ditransfer',
            ])
            ->assertRedirect(route('admin.withdraws.index'));

        $withdraw->refresh();

        $this->assertSame(WithdrawRequest::STATUS_SUCCESS, $withdraw->status);
        $this->assertSame('TRF-001', $withdraw->external_id);
        $this->assertNotNull($withdraw->processed_at);
        $this->assertSame(50000, $mitra->fresh()->balance);
    }

    public function test_admin_tidak_bisa_menyetujui_withdraw_di_luar_wilayahnya(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeCity($admin, 'Kota Wilayah Admin');

        $adminLain = $this->makeUser('admin');
        $kotaLain = $this->makeCity($adminLain, 'Kota Admin Lain');
        $mitra = $this->makeMitraWithBalance($kotaLain, 200000);
        $withdraw = $this->pendingWithdraw($mitra, 150000);

        $this->actingAs($admin)
            ->post(route('admin.withdraws.approve', $withdraw))
            ->assertForbidden();

        $this->assertSame(WithdrawRequest::STATUS_PENDING, $withdraw->fresh()->status);
        $this->assertSame(200000, $mitra->fresh()->balance);
    }

    public function test_admin_menolak_withdraw_mitra_di_wilayahnya(): void
    {
        $admin = $this->makeUser('admin');
        $city = $this->makeCity($admin, 'Kota Wilayah Admin');
        $mitra = $this->makeMitraWithBalance($city, 200000);
        $withdraw = $this->pendingWithdraw($mitra, 150000);

        $this->actingAs($admin)
            ->post(route('admin.withdraws.reject', $withdraw), ['note' => 'Rekening tidak valid'])
            ->assertRedirect(route('admin.withdraws.index'));

        $withdraw->refresh();

        $this->assertSame(WithdrawRequest::STATUS_FAILED, $withdraw->status);
        $this->assertSame('Rekening tidak valid', $withdraw->description);
        $this->assertSame(200000, $mitra->fresh()->balance);
    }

    public function test_admin_tidak_bisa_membuka_detail_withdraw_di_luar_wilayahnya(): void
    {
        $admin = $this->makeUser('admin');
        $this->makeCity($admin, 'Kota Wilayah Admin');

        $adminLain = $this->makeUser('admin');
        $kotaLain = $this->makeCity($adminLain, 'Kota Admin Lain');
        $mitra = $this->makeMitraWithBalance($kotaLain, 200000);
        $withdraw = $this->pendingWithdraw($mitra, 150000);

        $this->actingAs($admin)
            ->get(route('admin.withdraws.show', $withdraw))
            ->assertForbidden();
    }

    public function test_halaman_detail_admin_menampilkan_aksi_setujui_dan_tolak(): void
    {
        $admin = $this->makeUser('admin');
        $city = $this->makeCity($admin, 'Kota Wilayah Admin');
        $mitra = $this->makeMitraWithBalance($city, 200000);
        $withdraw = $this->pendingWithdraw($mitra, 150000);

        $this->actingAs($admin)
            ->get(route('admin.withdraws.show', $withdraw))
            ->assertOk()
            ->assertSee(route('admin.withdraws.approve', $withdraw))
            ->assertSee(route('admin.withdraws.reject', $withdraw))
            ->assertSee('Setujui');
    }

    public function test_super_admin_tetap_bisa_menyetujui_withdraw_kota_mana_pun(): void
    {
        $admin = $this->makeUser('admin');
        $city = $this->makeCity($admin, 'Kota Wilayah Admin');
        $mitra = $this->makeMitraWithBalance($city, 200000);
        $withdraw = $this->pendingWithdraw($mitra, 150000);

        $superAdmin = $this->makeUser('super_admin');

        $this->actingAs($superAdmin)
            ->post(route('superadmin.withdraws.approve', $withdraw))
            ->assertRedirect(route('superadmin.withdraws.index'));

        $this->assertSame(WithdrawRequest::STATUS_SUCCESS, $withdraw->fresh()->status);
        $this->assertSame(50000, $mitra->fresh()->balance);
    }
}
