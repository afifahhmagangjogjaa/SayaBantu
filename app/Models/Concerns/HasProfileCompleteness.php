<?php

namespace App\Models\Concerns;

/**
 * Kelengkapan profil/biodata dan URL dokumen KTP.
 * Diekstrak dari App\Models\User agar model tidak terlalu besar.
 */
trait HasProfileCompleteness
{
    public function isProfileComplete(): bool
    {
        return empty($this->getMissingProfileFields());
    }

    public function getMissingProfileFields(): array
    {
        // 1. Ambil semua field biodata teks dasar
        $missing = $this->getMissingBiodataFields();

        // 2. Foto KTP & Selfie HANYA ditagih jika akun BELUM diverifikasi.
        // Jika akun SUDAH berstatus verified == true (sudah diverifikasi oleh Admin/Super Admin),
        // maka kewajiban upload file foto KTP & Selfie otomatis diabaikan.
        if (!$this->verified) {
            if (empty($this->ktp_photo) && empty($this->ktp_path)) {
                $missing['ktp_photo'] = 'Foto KTP';
            }

            if (empty($this->selfie_photo)) {
                $missing['selfie_photo'] = 'Foto Selfie (Wajah & KTP)';
            }
        }

        return $missing;
    }

    public function getMissingBiodataFields(): array
    {
        $missing = [];

        if (empty(trim((string) $this->name))) {
            $missing['name'] = 'Nama Lengkap';
        }

        $cleanNik = preg_replace('/[^0-9]/', '', (string) $this->nik);
        if (empty($cleanNik) || strlen($cleanNik) !== 16) {
            $missing['nik'] = 'NIK (16 Digit Angka)';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (empty($cleanPhone) || strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13) {
            $missing['phone'] = 'No. HP (10-13 Digit Angka)';
        }

        if (empty($this->city_id)) {
            $missing['city_id'] = 'Kota Domisili';
        }

        if (empty(trim((string) $this->address))) {
            $missing['address'] = 'Alamat Lengkap';
        }

        return $missing;
    }

    public function getKtpUrlAttribute(): ?string
    {
        $path = $this->ktp_photo ?? $this->ktp_path;
        if (!$path && $this->relationLoaded('registration') && $this->registration) {
            $path = $this->registration->ktp_photo_path;
        } elseif (!$path) {
            $reg = \App\Models\Registration::where('email', $this->email)->first();
            if ($reg) {
                $path = $reg->ktp_photo_path;
            }
        }
        if ($path) {
            return asset('storage/' . ltrim($path, '/'));
        }
        return null;
    }

    public function getSelfieUrlAttribute(): ?string
    {
        $path = $this->selfie_photo;
        if (!$path && $this->relationLoaded('registration') && $this->registration) {
            $path = $this->registration->selfie_photo_path;
        } elseif (!$path) {
            $reg = \App\Models\Registration::where('email', $this->email)->first();
            if ($reg) {
                $path = $reg->selfie_photo_path;
            }
        }
        if ($path) {
            return asset('storage/' . ltrim($path, '/'));
        }
        return null;
    }
}
