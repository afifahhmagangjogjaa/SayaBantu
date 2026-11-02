<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'city_id',
        'province',
        'city_name',
        'district',
        'village',
        'postal_code',
        'address',
        'benchmark',
        'latitude',
        'longitude',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Nama penerima yang bersih tanpa suffix role seperti '(Customer)'
     */
    public function getCleanRecipientNameAttribute(): string
    {
        return trim(preg_replace('/\s*\((?:Mitra|Customer|Pending[^)]*)\)|\s*(?:Mitra|Customer)\b/i', '', (string) $this->recipient_name));
    }

    /**
     * Nomor telepon berformat rapi (08xx-xxxx-xxxx)
     */
    public function getFormattedPhoneAttribute(): string
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (strlen($clean) >= 10 && strlen($clean) <= 13) {
            return substr($clean, 0, 4).'-'.substr($clean, 4, 4).'-'.substr($clean, 8);
        }

        return $this->phone ?: '-';
    }

    /**
     * Format wilayah (Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos)
     */
    public function getRegionStringAttribute(): string
    {
        $parts = array_filter([
            $this->village ? 'Kel. '.$this->village : null,
            $this->district ? 'Kec. '.$this->district : null,
            $this->city_name ?: ($this->city ? $this->city->name : null),
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Format alamat lengkap terstruktur ala Shopee.
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = trim((string) $this->address);
        $region = $this->region_string;

        if (empty($region)) {
            return $address;
        }

        // Jika alamat sudah memuat nama kota/kecamatan, hindari pengulangan berlebih
        if ($this->city_name && str_contains(strtolower($address), strtolower($this->city_name))) {
            return $address;
        }

        return $address.', '.$region;
    }
}
