<?php

namespace Tests\Feature\Interaction;

use App\Enums\HelpStatus;
use App\Livewire\Customer\Reports\Create as CreateReport;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\PartnerReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PartnerReportTest extends TestCase
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

    private function makeHelp(User $customer, User $mitra): Help
    {
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Bantu bersih-bersih',
            'description' => 'Test laporan',
            'amount' => 50000,
            'status' => HelpStatus::SELESAI->value,
            'mitra_id' => $mitra->id,
        ]);
    }

    public function test_customer_bisa_mengirim_laporan_terkait_order(): void
    {
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra);

        $this->actingAs($customer);

        Livewire::test(CreateReport::class)
            ->set('title', 'Mitra tidak sesuai kesepakatan')
            ->set('message', 'Mitra datang terlambat dua jam dan tidak membawa peralatan.')
            ->set('report_type', 'pelayanan_tidak_sesuai')
            ->set('help_id', $help->id)
            ->call('submit')
            ->assertHasNoErrors();

        $report = PartnerReport::first();
        $this->assertNotNull($report);
        $this->assertSame('pending', $report->status);
        $this->assertSame('dari_customer', $report->category);
        $this->assertSame($customer->id, $report->reporter_id);
        $this->assertSame($mitra->id, $report->reported_user_id);
        $this->assertSame($help->id, $report->reported_help_id);
    }

    public function test_laporan_tanpa_judul_atau_pesan_singkat_ditolak(): void
    {
        $customer = $this->user('customer');

        $this->actingAs($customer);

        Livewire::test(CreateReport::class)
            ->set('title', '')
            ->set('message', 'pendek')
            ->set('report_type', 'penipuan')
            ->call('submit')
            ->assertHasErrors(['title', 'message']);

        $this->assertSame(0, PartnerReport::count());
    }

    public function test_admin_bisa_menyelesaikan_dan_membuka_kembali_laporan(): void
    {
        $admin = $this->user('admin');
        $customer = $this->user('customer');
        $mitra = $this->user('mitra');
        $help = $this->makeHelp($customer, $mitra);

        $report = PartnerReport::create([
            'user_id' => $mitra->id, // kolom legacy: user yang dilaporkan
            'reporter_id' => $customer->id,
            'reported_user_id' => $mitra->id,
            'reported_help_id' => $help->id,
            'title' => 'Laporan uji',
            'message' => 'Isi laporan uji yang cukup panjang.',
            'report_type' => 'penipuan',
            'category' => 'dari_customer',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.partners.reports.resolve', $report))
            ->assertRedirect();

        $report->refresh();
        $this->assertSame('resolved', $report->status);
        $this->assertNotNull($report->resolved_at);
        $this->assertSame($admin->id, $report->resolved_by);

        $this->actingAs($admin)
            ->post(route('admin.partners.reports.reopen', $report))
            ->assertRedirect();

        $report->refresh();
        $this->assertSame('in_progress', $report->status);
        $this->assertNull($report->resolved_at);
    }
}
