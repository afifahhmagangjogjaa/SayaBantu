<?php

namespace App\Enums;

/**
 * Satu-satunya sumber kebenaran untuk status `helps`.
 *
 * Sebelumnya aplikasi memakai dua kosakata sekaligus (Inggris dan Indonesia),
 * mis. 'taken' vs 'memperoleh_mitra' dan 'completed' vs 'selesai'. Nilai lama
 * tetap dikenali lewat aliases() supaya baris data lama tidak "hilang" dari
 * filter, tetapi penulisan baru WAJIB memakai case di enum ini.
 */
enum HelpStatus: string
{
    case MENUNGGU_MITRA = 'menunggu_mitra';
    case MEMPEROLEH_MITRA = 'memperoleh_mitra';
    case PARTNER_ON_THE_WAY = 'partner_on_the_way';
    case PARTNER_ARRIVED = 'partner_arrived';
    case IN_PROGRESS = 'in_progress';
    case WAITING_CUSTOMER_CONFIRMATION = 'waiting_customer_confirmation';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';
    case PARTNER_CANCEL_REQUESTED = 'partner_cancel_requested';
    case REJECTED = 'rejected';

    /**
     * Pemetaan nilai legacy (sudah tidak ditulis lagi) ke nilai kanonik.
     *
     * @return array<string, string>
     */
    public static function aliases(): array
    {
        return [
            'taken' => self::MEMPEROLEH_MITRA->value,
            'completed' => self::SELESAI->value,
            'cancelled' => self::DIBATALKAN->value,
            'approved' => self::MENUNGGU_MITRA->value,
            'active' => self::MENUNGGU_MITRA->value,
            'pending' => self::MENUNGGU_MITRA->value,
            'created' => self::MENUNGGU_MITRA->value,
            'mencari_mitra' => self::MENUNGGU_MITRA->value,
            'menunggu_pembayaran' => self::MENUNGGU_MITRA->value,
        ];
    }

    /**
     * Nilai kanonik dari sebuah status (menerjemahkan nilai legacy bila perlu).
     */
    public static function canonical(string $status): string
    {
        return self::aliases()[$status] ?? $status;
    }

    /**
     * Kembangkan daftar status menjadi nilai kanonik + semua nilai legacy yang
     * setara, sehingga query lama tetap menemukan baris data lama.
     *
     * @param  string|self|array<string|self>  $statuses
     * @return array<int, string>
     */
    public static function expand(string|self|array $statuses): array
    {
        $statuses = is_array($statuses) ? $statuses : [$statuses];

        $canonical = [];
        foreach ($statuses as $status) {
            $value = $status instanceof self ? $status->value : (string) $status;
            $canonical[self::canonical($value)] = true;
        }

        $legacy = [];
        foreach (self::aliases() as $legacyValue => $canonicalValue) {
            if (isset($canonical[$canonicalValue])) {
                $legacy[] = $legacyValue;
            }
        }

        return array_values(array_unique(array_merge(array_keys($canonical), $legacy)));
    }

    /**
     * Status yang berarti bantuan masih tersedia untuk diambil mitra.
     *
     * @return array<int, string>
     */
    public static function availableValues(): array
    {
        return self::expand(self::MENUNGGU_MITRA);
    }

    /**
     * Status aktif: mitra sudah terhubung dan pekerjaan belum tuntas.
     *
     * @return array<int, string>
     */
    public static function activeValues(): array
    {
        return self::expand([
            self::MEMPEROLEH_MITRA,
            self::PARTNER_ON_THE_WAY,
            self::PARTNER_ARRIVED,
            self::IN_PROGRESS,
            self::WAITING_CUSTOMER_CONFIRMATION,
            self::PARTNER_CANCEL_REQUESTED,
        ]);
    }

    /**
     * Status terminal: tidak ada transisi lagi.
     *
     * @return array<int, string>
     */
    public static function finalValues(): array
    {
        return self::expand([self::SELESAI, self::DIBATALKAN, self::REJECTED]);
    }

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_MITRA => 'Menunggu Mitra',
            self::MEMPEROLEH_MITRA => 'Mitra Ditugaskan',
            self::PARTNER_ON_THE_WAY => 'Mitra Menuju Lokasi',
            self::PARTNER_ARRIVED => 'Mitra Tiba di Lokasi',
            self::IN_PROGRESS => 'Sedang Dikerjakan',
            self::WAITING_CUSTOMER_CONFIRMATION => 'Menunggu Konfirmasi',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
            self::PARTNER_CANCEL_REQUESTED => 'Pengajuan Batal Mitra',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU_MITRA => 'bg-amber-100 text-amber-800 border-amber-200',
            self::MEMPEROLEH_MITRA => 'bg-sky-100 text-sky-800 border-sky-200',
            self::PARTNER_ON_THE_WAY => 'bg-blue-100 text-blue-800 border-blue-200',
            self::PARTNER_ARRIVED => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::IN_PROGRESS => 'bg-violet-100 text-violet-800 border-violet-200',
            self::WAITING_CUSTOMER_CONFIRMATION => 'bg-orange-100 text-orange-800 border-orange-200',
            self::SELESAI => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::DIBATALKAN => 'bg-rose-100 text-rose-800 border-rose-200',
            self::PARTNER_CANCEL_REQUESTED => 'bg-pink-100 text-pink-800 border-pink-200',
            self::REJECTED => 'bg-red-100 text-red-800 border-red-200',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::SELESAI, self::DIBATALKAN, self::REJECTED], true);
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::MEMPEROLEH_MITRA,
            self::PARTNER_ON_THE_WAY,
            self::PARTNER_ARRIVED,
            self::IN_PROGRESS,
            self::WAITING_CUSTOMER_CONFIRMATION,
            self::PARTNER_CANCEL_REQUESTED,
        ], true);
    }

    /**
     * Resolve status dari string apa pun (kanonik maupun legacy) menjadi case.
     */
    public static function resolve(?string $status): ?self
    {
        if ($status === null) {
            return null;
        }

        return self::tryFrom(self::canonical($status));
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Label untuk string status apa pun, aman untuk nilai yang tidak dikenal.
     */
    public static function labelFor(?string $status): string
    {
        return self::resolve($status)?->label() ?? (string) $status;
    }

    /**
     * Class badge untuk string status apa pun, aman untuk nilai yang tidak dikenal.
     */
    public static function badgeClassFor(?string $status): string
    {
        return self::resolve($status)?->badgeClass() ?? 'bg-slate-100 text-slate-700 border-slate-200';
    }
}
