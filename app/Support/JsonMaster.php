<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Akses terpusat untuk master data wilayah (kecamatan/kelurahan) yang disimpan
 * sebagai JSON di database/data.
 *
 * File-nya ratusan KB, jadi jangan dibaca ulang dengan file_get_contents di
 * setiap request: hasil decode disimpan di cache proses (static) dan di
 * Laravel Cache. Jalankan `php artisan cache:clear` setelah file master diubah.
 */
class JsonMaster
{
    /** Cache TTL (detik). Master data wilayah praktis tidak berubah. */
    public const TTL = 86400;

    /** @var array<string, array<string, mixed>> */
    protected static array $loaded = [];

    public static function districts(): array
    {
        return static::load('districts_master');
    }

    public static function villages(): array
    {
        return static::load('villages_master');
    }

    public static function load(string $name): array
    {
        if (array_key_exists($name, static::$loaded)) {
            return static::$loaded[$name];
        }

        $data = Cache::remember("master.json.{$name}", self::TTL, function () use ($name) {
            $path = database_path("data/{$name}.json");

            if (! is_file($path)) {
                return [];
            }

            $decoded = json_decode((string) file_get_contents($path), true);

            // Array asosiatif (map) maupun list sama-sama diterima.
            return is_array($decoded) ? $decoded : [];
        });

        return static::$loaded[$name] = is_array($data) ? $data : [];
    }

    /**
     * Cari nilai dari map master dengan toleransi format key:
     * "31.71" (kode bertitik), "3171" (kode tanpa titik), "JAKARTA" (nama),
     * atau id kota numerik seperti "1".
     */
    public static function find(array $map, string|int|null $key): array
    {
        if ($key === null || $key === '') {
            return [];
        }

        foreach (static::keyVariants((string) $key) as $variant) {
            if (isset($map[$variant]) && is_array($map[$variant]) && $map[$variant] !== []) {
                return $map[$variant];
            }
        }

        return [];
    }

    /**
     * @return array<int, string>
     */
    protected static function keyVariants(string $key): array
    {
        $key = trim($key);
        $variants = [$key, strtoupper($key)];

        if (str_contains($key, '.')) {
            $variants[] = str_replace('.', '', $key);
        } elseif (preg_match('/^\d{4,5}$/', $key)) {
            $variants[] = substr($key, 0, 2).'.'.substr($key, 2);
        }

        return array_values(array_unique($variants));
    }

    /** Bersihkan cache (dipakai seeder/command setelah master diperbarui). */
    public static function flush(): void
    {
        static::$loaded = [];
        Cache::forget('master.json.districts_master');
        Cache::forget('master.json.villages_master');
    }
}
