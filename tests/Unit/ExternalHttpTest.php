<?php

namespace Tests\Unit;

use App\Support\ExternalHttp;
use Tests\TestCase;

/**
 * Verifikasi TLS untuk API pihak ketiga: default aman di production,
 * tetap seperti perilaku lama (tanpa verifikasi) di local.
 */
class ExternalHttpTest extends TestCase
{
    public function test_local_tanpa_konfigurasi_tidak_memverifikasi(): void
    {
        config(['services.external.verify_tls' => null]);
        $this->app['env'] = 'local';

        $this->assertFalse(ExternalHttp::shouldVerify());
    }

    public function test_production_tanpa_konfigurasi_memverifikasi(): void
    {
        config(['services.external.verify_tls' => null]);
        $this->app['env'] = 'production';

        $this->assertTrue(ExternalHttp::shouldVerify());
    }

    public function test_konfigurasi_eksplisit_menang(): void
    {
        $this->app['env'] = 'local';
        config(['services.external.verify_tls' => true]);
        $this->assertTrue(ExternalHttp::shouldVerify());

        $this->app['env'] = 'production';
        config(['services.external.verify_tls' => false]);
        $this->assertFalse(ExternalHttp::shouldVerify());
    }
}
