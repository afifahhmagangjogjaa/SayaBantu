<?php

namespace Tests\Feature\Smoke;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Menyapu seluruh halaman utama per role. Tujuannya mendeteksi error 500
 * (query salah, view rusak, komponen gagal) yang tidak terlihat dari test unit.
 */
class RolePagesTest extends TestCase
{
    use RefreshDatabase;

    private function userFor(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
            'name' => 'Pengguna '.$role,
            'phone' => '081234567890',
            // NIK unik: satu test bisa membuat beberapa user sekaligus.
            'nik' => fake()->unique()->numerify('################'),
            'address' => 'Jl. Test No. 1',
            'gender' => 'Laki-laki',
            'city_id' => City::create(['name' => 'Kota Test '.fake()->unique()->numerify('###'), 'is_active' => true])->id,
        ]);
    }

    public static function customerRoutes(): array
    {
        return [
            'dashboard' => ['customer.dashboard'],
            'daftar bantuan' => ['customer.helps.index'],
            'buat bantuan' => ['customer.helps.create'],
            'riwayat bantuan' => ['customer.helps.history'],
            'notifikasi' => ['customer.notifications.index'],
            'transaksi' => ['customer.transactions.index'],
            'topup' => ['customer.topup'],
            'riwayat topup' => ['customer.topup.history'],
            'chat' => ['customer.chat'],
            'help support' => ['customer.help-support'],
            'profil' => ['profile'],
            'edit profil' => ['profile.edit'],
            'pengaturan profil' => ['profile.settings'],
            'pengaturan notifikasi' => ['profile.settings.notifications'],
            'pengaturan sandi' => ['profile.settings.password'],
            'verifikasi profil' => ['profile.settings.verification'],
        ];
    }

    public static function mitraRoutes(): array
    {
        return [
            'dashboard' => ['mitra.dashboard'],
            'semua order' => ['mitra.helps.all'],
            'order selesai' => ['mitra.helps.completed'],
            'order diproses' => ['mitra.helps.processing'],
            'profil' => ['mitra.profile'],
            'edit profil' => ['mitra.profile.edit'],
            'notifikasi' => ['mitra.notifications.index'],
            'rating' => ['mitra.ratings'],
            'chat' => ['mitra.chat'],
            'pengaturan' => ['mitra.settings'],
            'pengaturan notifikasi' => ['mitra.settings.notifications'],
            'pengaturan sandi' => ['mitra.settings.password'],
            'help support' => ['mitra.help-support'],
            'tarik saldo' => ['mitra.withdraw.form'],
            'riwayat tarik saldo' => ['mitra.withdraw.history'],
        ];
    }

    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['admin.dashboard'],
            'bantuan' => ['admin.helps'],
            'bantuan disetujui' => ['admin.helps.approved'],
            'verifikasi' => ['admin.verifications'],
            'customer' => ['admin.customers'],
            'mitra' => ['admin.mitra'],
            'semua user' => ['admin.users.index'],
            'rating' => ['admin.ratings.index'],
            'aktivitas mitra' => ['admin.partners.activity'],
            'laporan mitra' => ['admin.partners.report'],
            'notifikasi' => ['admin.notifications.index'],
            'approval topup' => ['admin.topup.approvals'],
            'penarikan saldo' => ['admin.withdraws.index'],
        ];
    }

    public static function superAdminRoutes(): array
    {
        return [
            'dashboard' => ['superadmin.dashboard'],
            'semua user' => ['superadmin.users'],
            'customer' => ['superadmin.customers'],
            'mitra' => ['superadmin.mitra'],
            'kota' => ['superadmin.cities'],
            'kategori' => ['superadmin.categories'],
            'rating' => ['superadmin.ratings.index'],
            'notifikasi' => ['superadmin.notifications.index'],
            'activity log' => ['superadmin.activity.logs'],
            'transaksi' => ['superadmin.transactions.log'],
            'bantuan disetujui' => ['superadmin.helps.approved'],
            'pengaturan bantuan' => ['superadmin.settings.help'],
            'banner' => ['superadmin.settings.banners'],
            'approval topup' => ['superadmin.topup.approvals'],
            'admin user' => ['superadmin.admin.users'],
            'penarikan saldo' => ['superadmin.withdraws.index'],
        ];
    }

    #[DataProvider('customerRoutes')]
    public function test_halaman_customer_bisa_dibuka(string $routeName): void
    {
        Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        $this->actingAs($this->userFor('customer'))
            ->get(route($routeName))
            ->assertSuccessful();
    }

    #[DataProvider('mitraRoutes')]
    public function test_halaman_mitra_bisa_dibuka(string $routeName): void
    {
        Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        $this->actingAs($this->userFor('mitra'))
            ->get(route($routeName))
            ->assertSuccessful();
    }

    #[DataProvider('adminRoutes')]
    public function test_halaman_admin_bisa_dibuka(string $routeName): void
    {
        Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        $this->actingAs($this->userFor('admin'))
            ->get(route($routeName))
            ->assertSuccessful();
    }

    #[DataProvider('superAdminRoutes')]
    public function test_halaman_superadmin_bisa_dibuka(string $routeName): void
    {
        Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        $this->actingAs($this->userFor('super_admin'))
            ->get(route($routeName))
            ->assertSuccessful();
    }

    /**
     * Halaman customer/mitra yang memakai partial bersama harus tetap
     * merender isi partial dan memakai route milik perannya masing-masing.
     */
    public function test_halaman_berbagi_partial_merender_konten_dan_route_yang_benar(): void
    {
        Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        $this->actingAs($this->userFor('customer'))
            ->get(route('customer.help-support'))
            ->assertSuccessful()
            ->assertSee('Frequently Asked Questions')
            ->assertSee('Hubungi Kami')
            ->assertSee(route('profile'), false);

        $this->actingAs($this->userFor('customer'))
            ->get(route('profile.settings.notifications'))
            ->assertSuccessful()
            ->assertSee('Kelola preferensi pemberitahuan')
            ->assertSee(route('customer.notifications.index'), false);

        $this->actingAs($this->userFor('customer'))
            ->get(route('profile.settings.password'))
            ->assertSuccessful()
            ->assertSee('Ubah Kata Sandi')
            ->assertSee(route('profile.password.update'), false);

        $this->actingAs($this->userFor('mitra'))
            ->get(route('mitra.help-support'))
            ->assertSuccessful()
            ->assertSee('Frequently Asked Questions')
            ->assertSee(route('mitra.profile'), false);

        $this->actingAs($this->userFor('mitra'))
            ->get(route('mitra.settings.notifications'))
            ->assertSuccessful()
            ->assertSee('Kelola preferensi pemberitahuan')
            ->assertSee(route('mitra.notifications.index'), false);

        $this->actingAs($this->userFor('mitra'))
            ->get(route('mitra.settings.password'))
            ->assertSuccessful()
            ->assertSee('Ubah Kata Sandi')
            ->assertSee(route('mitra.profile'), false);
    }
}
