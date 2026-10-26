<?php

namespace Tests\Unit;

use App\Enums\HelpStatus;
use PHPUnit\Framework\TestCase;

class HelpStatusTest extends TestCase
{
    public function test_canonical_menerjemahkan_nilai_legacy(): void
    {
        $this->assertSame('memperoleh_mitra', HelpStatus::canonical('taken'));
        $this->assertSame('selesai', HelpStatus::canonical('completed'));
        $this->assertSame('dibatalkan', HelpStatus::canonical('cancelled'));
        $this->assertSame('menunggu_mitra', HelpStatus::canonical('approved'));
        $this->assertSame('menunggu_mitra', HelpStatus::canonical('active'));
    }

    public function test_canonical_tidak_mengubah_nilai_yang_sudah_kanonik(): void
    {
        foreach (HelpStatus::values() as $value) {
            $this->assertSame($value, HelpStatus::canonical($value));
        }
    }

    public function test_expand_mengembalikan_nilai_kanonik_dan_seluruh_aliasnya(): void
    {
        $expanded = HelpStatus::expand(HelpStatus::MEMPEROLEH_MITRA);

        $this->assertContains('memperoleh_mitra', $expanded);
        $this->assertContains('taken', $expanded);
        $this->assertCount(2, $expanded);
    }

    public function test_available_values_mencakup_status_menunggu_dan_alias_lamanya(): void
    {
        $values = HelpStatus::availableValues();

        $this->assertContains('menunggu_mitra', $values);
        $this->assertContains('approved', $values);
        $this->assertContains('pending', $values);
        $this->assertNotContains('selesai', $values);
    }

    public function test_active_values_tidak_mencakup_status_terminal(): void
    {
        $values = HelpStatus::activeValues();

        $this->assertContains('memperoleh_mitra', $values);
        $this->assertContains('in_progress', $values);
        $this->assertContains('partner_arrived', $values);
        $this->assertNotContains('selesai', $values);
        $this->assertNotContains('dibatalkan', $values);
    }

    public function test_is_final_dan_is_active(): void
    {
        $this->assertTrue(HelpStatus::SELESAI->isFinal());
        $this->assertTrue(HelpStatus::DIBATALKAN->isFinal());
        $this->assertTrue(HelpStatus::REJECTED->isFinal());
        $this->assertFalse(HelpStatus::MENUNGGU_MITRA->isFinal());

        $this->assertTrue(HelpStatus::IN_PROGRESS->isActive());
        $this->assertFalse(HelpStatus::SELESAI->isActive());
    }

    public function test_resolve_menerima_nilai_legacy(): void
    {
        $this->assertSame(HelpStatus::SELESAI, HelpStatus::resolve('completed'));
        $this->assertSame(HelpStatus::MEMPEROLEH_MITRA, HelpStatus::resolve('taken'));
        $this->assertNull(HelpStatus::resolve(null));
        $this->assertNull(HelpStatus::resolve('status_asing'));
    }

    public function test_label_for_aman_untuk_nilai_tidak_dikenal(): void
    {
        $this->assertSame('Selesai', HelpStatus::labelFor('completed'));
        $this->assertSame('status_asing', HelpStatus::labelFor('status_asing'));
        $this->assertStringContainsString('bg-slate', HelpStatus::badgeClassFor('status_asing'));
    }
}
