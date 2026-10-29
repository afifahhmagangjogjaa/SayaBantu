<?php

namespace Tests\Feature\Interaction;

use App\Models\PartnerReport;
use App\Models\User;
use App\Notifications\SanctionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alur Surat Peringatan (SP): pemberian level, penundaan blokir untuk SP3,
 * pencabutan, dan konfirmasi SP3 oleh user.
 */
class SanctionTest extends TestCase
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

    private function makeReport(User $admin, User $reporter, User $reported): PartnerReport
    {
        return PartnerReport::create([
            'user_id' => $reported->id,
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported->id,
            'title' => 'Laporan uji',
            'message' => 'Isi laporan uji yang cukup panjang.',
            'report_type' => 'penipuan',
            'category' => 'dari_customer',
            'status' => 'pending',
        ]);
    }

    public function test_admin_memberi_sp1_tanpa_memblokir_akun(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $report = $this->makeReport($admin, $customer, $mitra);

        $this->actingAs($admin)
            ->post(route('admin.partners.reports.sanction', $report), [
                'user_id' => $mitra->id,
                'warning_level' => 1,
                'sanction_reason' => 'Terlambat tanpa kabar',
            ])
            ->assertRedirect();

        $mitra->refresh();
        $this->assertSame(1, (int) $mitra->warning_level);
        $this->assertSame('Terlambat tanpa kabar', $mitra->warning_reason);
        $this->assertNotNull($mitra->warning_applied_at);
        $this->assertFalse((bool) $mitra->is_banned);
        $this->assertSame('active', $mitra->status);

        // Notifikasi resmi terkirim ke user
        $this->assertSame(1, $mitra->notifications()->count());
        $this->assertSame('sanction_warning', $mitra->notifications()->first()->data['type']);
    }

    public function test_sp3_tidak_langsung_memblokir(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $report = $this->makeReport($admin, $customer, $mitra);

        $this->actingAs($admin)
            ->post(route('admin.partners.reports.sanction', $report), [
                'user_id' => $mitra->id,
                'warning_level' => 3,
                'sanction_reason' => 'Pelanggaran berulang',
            ]);

        $mitra->refresh();
        $this->assertSame(3, (int) $mitra->warning_level);
        // Blokir baru terjadi saat user menutup modal SP3.
        $this->assertFalse((bool) $mitra->is_banned);
        $this->assertSame('active', $mitra->status);
    }

    public function test_pencabutan_sp_mengembalikan_akun_normal(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $mitra = $this->user('mitra', ['warning_level' => 2, 'is_banned' => false]);
        $report = $this->makeReport($admin, $customer, $mitra);

        $this->actingAs($admin)
            ->post(route('admin.partners.reports.sanction', $report), [
                'user_id' => $mitra->id,
                'warning_level' => 0,
            ]);

        $mitra->refresh();
        $this->assertSame(0, (int) $mitra->warning_level);
        $this->assertNull($mitra->warning_applied_at);
        $this->assertFalse((bool) $mitra->is_banned);
    }

    public function test_user_konfirmasi_sp3_lalu_akun_diblokir(): void
    {
        $mitra = $this->user('mitra');
        $mitra->notify(new SanctionNotification(3, 'Pelanggaran berat'));

        $notification = $mitra->notifications()->first();

        $this->actingAs($mitra)
            ->post(route('notifications.sanction.acknowledge', $notification->id))
            ->assertRedirect(route('login'));

        $mitra->refresh();
        $this->assertTrue((bool) $mitra->is_banned);
        $this->assertSame('blocked', $mitra->status);
        $this->assertGuest();
    }

    public function test_user_lain_tidak_boleh_membuka_surat_sanksi(): void
    {
        $mitra = $this->user('mitra');
        $mitra->notify(new SanctionNotification(2, 'Peringatan'));

        $notification = $mitra->notifications()->first();
        $lain = $this->user('mitra', ['email' => 'lain@example.test']);

        $this->actingAs($lain)
            ->get(route('notifications.sanction', $notification->id))
            ->assertForbidden();
    }
}
