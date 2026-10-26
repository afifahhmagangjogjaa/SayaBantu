<?php

namespace Tests\Feature\Balance;

use App\Enums\HelpStatus;
use App\Models\Category;
use App\Models\City;
use App\Models\Help;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan migration normalisasi status benar-benar memindahkan nilai legacy
 * ke nilai kanonik enum, dan tidak menyentuh status yang sudah benar.
 */
class HelpStatusNormalizationTest extends TestCase
{
    use RefreshDatabase;

    private function runNormalizationMigration(): void
    {
        $migration = require database_path('migrations/2026_10_24_000001_normalize_helps_status_values.php');
        $migration->up();
    }

    private function makeHelp(string $status): Help
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $city = City::create(['name' => 'Kota Test', 'is_active' => true]);
        $category = Category::create(['name' => 'Kategori Test', 'is_active' => true]);

        return Help::create([
            'user_id' => $customer->id,
            'city_id' => $city->id,
            'category_id' => $category->id,
            'help_type' => 'scheduled',
            'title' => 'Test normalisasi',
            'description' => 'Test',
            'amount' => 10000,
            'status' => $status,
        ]);
    }

    public function test_nilai_legacy_dinormalkan_ke_kanonik(): void
    {
        $cases = [
            'taken' => HelpStatus::MEMPEROLEH_MITRA->value,
            'completed' => HelpStatus::SELESAI->value,
            'cancelled' => HelpStatus::DIBATALKAN->value,
            'approved' => HelpStatus::MENUNGGU_MITRA->value,
            'pending' => HelpStatus::MENUNGGU_MITRA->value,
        ];

        $ids = [];
        foreach ($cases as $legacy => $_) {
            $ids[$legacy] = $this->makeHelp($legacy)->id;
        }

        $this->runNormalizationMigration();

        foreach ($cases as $legacy => $canonical) {
            $this->assertSame(
                $canonical,
                Help::find($ids[$legacy])->status,
                "status '{$legacy}' seharusnya menjadi '{$canonical}'"
            );
        }
    }

    public function test_status_kanonik_tidak_berubah(): void
    {
        $help = $this->makeHelp(HelpStatus::PARTNER_ARRIVED->value);

        $this->runNormalizationMigration();

        $this->assertSame(HelpStatus::PARTNER_ARRIVED->value, $help->fresh()->status);
    }
}
