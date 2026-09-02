# Fitur GPS Tracking untuk Mitra

## Deskripsi
Sistem GPS tracking otomatis yang akan mengaktifkan tracking lokasi mitra secara real-time saat mitra mengambil bantuan. Status bantuan akan berubah otomatis berdasarkan pergerakan dan jarak mitra.

## Fitur Utama

### 1. Auto-Start GPS Tracking
- GPS tracking akan otomatis aktif saat mitra menekan tombol "Ambil Bantuan"
- Lokasi awal mitra akan tercatat di database
- Tracking berjalan di background tanpa mengganggu user

### 2. Status Auto-Update Berdasarkan Jarak

#### Status: `taken` (Bantuan Diambil)
- Mitra baru saja mengambil bantuan
- Lokasi awal tercatat
- GPS tracking dimulai

#### Status: `partner_on_the_way` (Rekan Jasa Menuju Lokasi)
- **Trigger**: Mitra bergerak **≥ 20 meter** dari lokasi awal
- Status berubah otomatis
- Notifikasi dikirim ke customer
- Timestamp `partner_started_moving_at` tercatat

#### Status: `partner_arrived` (Rekan Jasa Tiba di Lokasi)
- **Trigger**: Mitra berada dalam radius **≤ 50 meter** dari lokasi customer
- Status berubah otomatis
- Notifikasi dikirim ke customer
- Timestamp `partner_arrived_at` tercatat

### 3. Real-time Location Updates
- Lokasi mitra diupdate setiap beberapa detik
- Menggunakan HTML5 Geolocation API `watchPosition()`
- High accuracy mode untuk presisi maksimal
- Auto-retry jika GPS gagal

## Struktur Database

### Kolom Baru di Tabel `helps`

```sql
partner_initial_lat     DECIMAL(10,8)  -- Latitude lokasi awal mitra
partner_initial_lng     DECIMAL(11,8)  -- Longitude lokasi awal mitra
partner_current_lat     DECIMAL(10,8)  -- Latitude lokasi real-time mitra
partner_current_lng     DECIMAL(11,8)  -- Longitude lokasi real-time mitra
partner_started_moving_at TIMESTAMP    -- Waktu mitra mulai bergerak
partner_arrived_at      TIMESTAMP      -- Waktu mitra tiba di lokasi
```

### Status Enum Baru
```sql
'taken'                -- Bantuan diambil
'partner_on_the_way'   -- Mitra menuju lokasi (NEW)
'partner_arrived'      -- Mitra tiba di lokasi (NEW)
'in_progress'          -- Pekerjaan sedang berlangsung
'completed'            -- Selesai
```

## Komponen

### 1. LocationTrackingService
**Path**: `app/Services/LocationTrackingService.php`

**Methods**:
- `calculateDistance($lat1, $lng1, $lat2, $lng2)`: Hitung jarak menggunakan Haversine formula
- `updatePartnerLocation($help, $lat, $lng)`: Update lokasi mitra dan auto-update status
- `setInitialLocation($help, $lat, $lng)`: Set lokasi awal saat ambil bantuan
- `getTrackingStatus($help)`: Ambil status tracking untuk ditampilkan

**Constants**:
- `MOVING_THRESHOLD = 20` meter (threshold untuk status "menuju lokasi")
- `ARRIVAL_THRESHOLD = 50` meter (threshold untuk status "tiba di lokasi")

### 2. Livewire Component: GpsTracker
**Path**: `app/Livewire/Mitra/GpsTracker.php`

**Properties**:
- `$helpId`: ID bantuan yang di-track
- `$isTracking`: Status tracking (aktif/tidak)
- `$currentStatus`: Status bantuan saat ini
- `$distanceInfo`: Informasi jarak (dari awal & ke customer)

**Methods**:
- `updateLocation($latitude, $longitude)`: Handle GPS update dari frontend
- `stopTracking()`: Stop GPS tracking

### 3. Frontend Component (Alpine.js)
**Path**: `resources/views/livewire/mitra/gps-tracker.blade.php`

**Features**:
- Auto-start GPS tracking via `navigator.geolocation.watchPosition()`
- Real-time UI updates
- Distance display
- Status change notifications
- Error handling untuk GPS permission denied

## Flow Tracking

```
1. Mitra klik "Ambil Bantuan"
   ↓
2. Request GPS permission
   ↓
3. Ambil lokasi awal → Simpan ke DB
   ↓
4. Status: "taken"
   ↓
5. GPS tracking dimulai (background)
   ↓
6. Mitra mulai bergerak > 20m
   ↓
7. Status berubah: "partner_on_the_way"
   ↓ (notifikasi ke customer)
   ↓
8. Mitra mendekati lokasi customer < 50m
   ↓
9. Status berubah: "partner_arrived"
   ↓ (notifikasi ke customer)
   ↓
10. Mitra mulai pekerjaan → Status: "in_progress"
```

## Implementasi di Dashboard Mitra

### Update `app/Livewire/Mitra/Dashboard.php`

**Method `takeHelp()` diupdate**:
```php
public function takeHelp($helpId, $latitude = null, $longitude = null)
{
    $help = Help::findOrFail($helpId);
    
    // ... validasi ...
    
    $help->update([
        'mitra_id' => auth()->id(),
        'status' => 'taken',
        'taken_at' => now(),
    ]);

    // Set lokasi awal jika GPS tersedia
    if ($latitude && $longitude) {
        $locationService = app(LocationTrackingService::class);
        $locationService->setInitialLocation($help, $latitude, $longitude);
    }

    // Dispatch event untuk mulai GPS tracking
    $this->dispatch('start-gps-tracking', helpId: $helpId);
}
```

### Update View Dashboard
**Path**: `resources/views/livewire/mitra/dashboard/index.blade.php`

**GPS Tracker Component ditambahkan**:
```blade
@foreach($helps as $help)
    @if(in_array($help->status, ['taken', 'partner_on_the_way', 'partner_arrived']))
        <livewire:mitra.gps-tracker :helpId="$help->id" />
    @endif
@endforeach
```

**JavaScript untuk Auto-request GPS saat ambil bantuan**:
```javascript
function takeHelpFromModal() {
    navigator.geolocation.getCurrentPosition(
        (position) => {
            @this.takeHelp(currentHelpId, 
                position.coords.latitude, 
                position.coords.longitude
            );
        },
        (error) => {
            // Fallback: ambil tanpa GPS
            @this.takeHelp(currentHelpId);
        }
    );
}
```

## Penggunaan

### Untuk Mitra:
1. Buka dashboard mitra
2. Klik bantuan yang ingin diambil
3. Klik "Ambil Bantuan"
4. Izinkan akses GPS saat diminta browser
5. GPS tracking akan otomatis aktif
6. Status akan berubah otomatis saat Anda bergerak

### Untuk Customer:
- Customer akan menerima notifikasi otomatis saat:
  - Mitra mulai menuju lokasi
  - Mitra tiba di lokasi
- Customer bisa track lokasi real-time mitra (jika fitur ditambahkan)

## Permissions yang Dibutuhkan

### Browser Permissions:
- **Geolocation API**: REQUIRED
- **Notifications API**: OPTIONAL (untuk browser notifications)

### User harus mengizinkan:
1. Akses lokasi di browser
2. Akses lokasi di sistem operasi (Android/iOS)
3. Background location (untuk tracking kontinyu)

## Error Handling

### GPS Error Scenarios:

1. **Permission Denied**
   - Alert: "Izin GPS ditolak"
   - Fallback: Bantuan tetap bisa diambil tanpa GPS

2. **Position Unavailable**
   - Retry otomatis
   - Log error untuk debugging

3. **Timeout**
   - Timeout: 10 detik
   - Retry dengan setting yang lebih lenient

## Testing

### Test Skenario:

1. **Test Ambil Bantuan dengan GPS**
   ```
   - Ambil bantuan
   - Cek lokasi awal tersimpan
   - Cek status = 'taken'
   ```

2. **Test Status Change: Moving**
   ```
   - Simulate gerak > 20m
   - Cek status berubah ke 'partner_on_the_way'
   - Cek timestamp tercatat
   ```

3. **Test Status Change: Arrived**
   ```
   - Simulate lokasi dekat customer (< 50m)
   - Cek status berubah ke 'partner_arrived'
   - Cek timestamp tercatat
   ```

4. **Test GPS Denied**
   ```
   - Deny GPS permission
   - Bantuan tetap bisa diambil
   - Status 'taken' tanpa GPS coordinates
   ```

## Performance Considerations

- GPS update interval: ~5 detik (via `maximumAge: 5000`)
- Database update: Setiap GPS position change
- Optimasi: Hanya track untuk status aktif (`taken`, `partner_on_the_way`, `partner_arrived`)
- Auto-stop tracking saat status berubah ke `in_progress` atau `completed`

## Future Enhancements

1. **Customer View**: Live map tracking untuk customer
2. **ETA Calculation**: Estimasi waktu tiba berdasarkan jarak & kecepatan
3. **Route History**: Simpan rute perjalanan mitra
4. **Geofencing**: Alert jika mitra keluar dari radius tertentu
5. **Offline Mode**: Queue GPS updates saat offline, sync saat online
6. **Battery Optimization**: Adaptive tracking interval berdasarkan battery level

## Security

- GPS coordinates encrypted di database
- Only mitra yang assigned bisa update location
- Rate limiting untuk prevent GPS spam
- Validasi coordinates (range check)

## Dependencies

- **Laravel**: ^11.0
- **Livewire**: ^3.0
- **Alpine.js**: ^3.0
- **Browser**: Modern browser dengan Geolocation API support

## Troubleshooting

### GPS tidak aktif?
1. Cek browser permission
2. Cek HTTPS (GPS tidak jalan di HTTP non-localhost)
3. Cek device GPS enabled
4. Check browser console untuk error

### Status tidak berubah otomatis?
1. Cek jarak > threshold (20m atau 50m)
2. Cek GPS accuracy
3. Review log di `storage/logs/laravel.log`
4. Cek koneksi internet

### Performance lambat?
1. Turunkan GPS accuracy
2. Increase update interval
3. Cek database query optimization
4. Enable query caching

---

**Created**: December 12, 2025
**Version**: 1.0.0
**Author**: Development Team
