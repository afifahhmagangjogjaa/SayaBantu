<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Klien HTTP untuk API pihak ketiga (Nominatim, Photon, wilayah.id, dll).
 *
 * Verifikasi TLS mengikuti konfigurasi `services.external.verify_tls`:
 *  - null (default): verifikasi AKTIF, kecuali di environment local. Di lokal
 *    perilakunya sengaja dibiarkan seperti sebelumnya (tanpa verifikasi) karena
 *    CA bundle PHP di mesin developer sering belum disetel.
 *  - true  : selalu verifikasi (pakai ini di production).
 *  - false : jangan verifikasi (hanya untuk mesin yang benar-benar bermasalah).
 */
class ExternalHttp
{
    public static function client(): PendingRequest
    {
        return Http::withOptions(['verify' => self::shouldVerify()]);
    }

    public static function shouldVerify(): bool
    {
        $configured = config('services.external.verify_tls');

        if ($configured === null) {
            return ! app()->isLocal();
        }

        return (bool) $configured;
    }
}
