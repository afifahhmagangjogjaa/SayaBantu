# 🧪 Panduan Testing GPS Tracking (Tanpa Bergerak Fisik)

## 📱 GPS Simulator - Testing Tool

Untuk memudahkan testing fitur GPS tracking tanpa harus berjalan kaki atau berkendara, kami telah menyediakan **GPS Simulator** yang dapat mensimulasikan pergerakan mitra secara otomatis.

---

## 🎯 Fitur GPS Simulator

### 1. **Auto-Simulate Movement**
- Simulasi pergerakan otomatis menuju lokasi customer
- Update GPS coordinates setiap beberapa detik
- Auto-stop saat sudah sampai di lokasi

### 2. **Adjustable Settings**
- **Kecepatan Simulasi**: 1-10 detik per update
- **Langkah Gerak**: 10-100 meter per step
- Sesuaikan dengan kebutuhan testing

### 3. **Quick Actions**
- **Gerak 20m**: Langsung bergerak 20 meter
- **Gerak 50m**: Langsung bergerak 50 meter  
- **Teleport**: Langsung ke lokasi customer

### 4. **Real-time Monitoring**
- Tampilan koordinat GPS saat ini
- Jarak ke target customer
- Status simulasi (Aktif/Stop)

---

## 📋 Cara Testing Step-by-Step

### **Persiapan:**

1. **Login sebagai Mitra**
   ```
   URL: http://localhost:3000/mitra/login
   ```

2. **Ambil Bantuan**
   - Buka Dashboard Mitra
   - Pilih bantuan yang tersedia
   - Klik "Ambil Bantuan"
   - Izinkan akses GPS (atau akan di-generate random)

3. **GPS Simulator Akan Muncul**
   - Simulator hanya tampil di environment non-production
   - Atau jika email Anda terdaftar sebagai admin

---

### **Scenario Testing 1: Auto Movement**

#### Step 1: Setup Kecepatan
```
1. Atur "Kecepatan Simulasi" ke 2 detik
2. Atur "Langkah Gerak" ke 20 meter
```

#### Step 2: Mulai Simulasi
```
1. Klik tombol "Mulai" (hijau)
2. GPS akan mulai update otomatis setiap 2 detik
3. Mitra akan "bergerak" 20 meter per update
```

#### Step 3: Perhatikan Perubahan
```
- Status berubah dari "taken" → "partner_on_the_way" (saat gerak >20m)
- Jarak ke customer berkurang secara real-time
- Status berubah menjadi "partner_arrived" (saat <50m dari customer)
```

#### Step 4: Verifikasi
```
✅ Cek status di GPS Tracker berubah otomatis
✅ Buka halaman detail customer → Status terupdate
✅ Jarak ditampilkan dengan benar
✅ Timestamp tercatat
```

---

### **Scenario Testing 2: Manual Steps**

#### Quick Move 20m
```
1. Klik "Gerak 20m"
2. GPS update sekali saja
3. Cek apakah status berubah jika sudah >20m dari awal
```

#### Quick Move 50m
```
1. Klik "Gerak 50m"  
2. Lompat lebih jauh
3. Berguna untuk skip jarak jauh
```

---

### **Scenario Testing 3: Teleport (Fast Testing)**

#### Langsung ke Tujuan
```
1. Klik tombol "Teleport" (ungu)
2. Konfirmasi popup
3. GPS langsung update ke lokasi customer
4. Status langsung berubah "partner_arrived"
```

**Use Case:**
- Testing cepat untuk status "arrived"
- Skip proses perjalanan
- Testing notifikasi arrival

---

### **Scenario Testing 4: Testing Status Flow Lengkap**

#### Complete Flow Test
```
1. Ambil bantuan baru
   → Status: "taken"
   → Lokasi awal tercatat

2. Klik "Mulai" simulator (Speed: 2s, Step: 20m)
   → Tunggu 2-3 update
   → Status berubah: "partner_on_the_way"
   → Jarak berkurang

3. Biarkan sampai dekat customer
   → Status berubah: "partner_arrived"
   → Simulator auto-stop

4. Verifikasi di halaman customer
   → Buka /customer/helps/{id}/detail
   → Timeline menunjukkan semua step
   → Waktu tercatat dengan benar
```

---

## 🎮 Control Panel Simulator

### **Kecepatan Simulasi**
```
1 detik  = Update sangat cepat (testing cepat)
2 detik  = Recommended (normal)
5 detik  = Lambat (observasi detail)
10 detik = Sangat lambat (demo)
```

### **Langkah Gerak**
```
10 meter  = Gerakan detail, banyak update
20 meter  = Recommended (threshold detection)
50 meter  = Gerakan cepat
100 meter = Skip jarak jauh
```

---

## 📊 Monitoring Testing

### **Apa yang Harus Dicek:**

#### 1. **GPS Tracker Widget**
```
✅ Tampil status real-time
✅ Distance to customer update
✅ Status badge berubah (warna & text)
✅ Last update timestamp
```

#### 2. **Database Updates**
```sql
SELECT 
    id,
    status,
    partner_initial_lat,
    partner_current_lat,
    partner_started_moving_at,
    partner_arrived_at
FROM helps 
WHERE id = {help_id};
```

#### 3. **Customer View**
```
✅ Status timeline terupdate
✅ Waktu per step tercatat
✅ Jarak ditampilkan (saat on_the_way)
✅ Badge/indicator sesuai status
✅ Auto-refresh setiap 5 detik
```

#### 4. **Logs**
```
Cek: storage/logs/laravel.log

Expected logs:
- "Lokasi awal mitra disimpan"
- "Status berubah: Rekan jasa menuju lokasi"
- "Status berubah: Rekan jasa tiba di lokasi"
```

---

## 🔧 Troubleshooting

### **Simulator Tidak Muncul?**
```php
// Check environment
php artisan tinker
> config('app.env')
// Should be: "local" or "development"

// Atau login dengan admin email
> auth()->user()->email
// Should match with: 'admin@example.com'
```

### **Status Tidak Berubah?**
```
1. Cek threshold distance:
   - Moving: ≥20m dari initial
   - Arrived: ≤50m dari customer

2. Cek koordinat customer ada:
   - $help->latitude != null
   - $help->longitude != null

3. Cek log errors:
   - tail -f storage/logs/laravel.log
```

### **Update Terlalu Lambat?**
```
1. Turunkan "Kecepatan Simulasi" (1-2 detik)
2. Naikkan "Langkah Gerak" (50-100m)
3. Atau gunakan "Teleport" untuk instant
```

---

## 🎯 Testing Checklist

### **Basic Testing**
- [ ] Simulator muncul setelah ambil bantuan
- [ ] Tombol "Mulai" berfungsi
- [ ] GPS coordinates terupdate
- [ ] Jarak ke customer berkurang
- [ ] Tombol "Stop" menghentikan simulasi

### **Status Testing**
- [ ] Status "taken" → ada badge "GPS Tracking Aktif"
- [ ] Status berubah ke "partner_on_the_way" setelah >20m
- [ ] Jarak ditampilkan saat "on_the_way"
- [ ] Status berubah ke "partner_arrived" saat <50m
- [ ] Badge "Rekan jasa sudah sampai" muncul

### **Database Testing**
- [ ] `partner_initial_lat/lng` tersimpan
- [ ] `partner_current_lat/lng` terupdate
- [ ] `partner_started_moving_at` tercatat
- [ ] `partner_arrived_at` tercatat
- [ ] Status enum tersimpan dengan benar

### **UI/UX Testing**
- [ ] GPS Tracker widget responsive
- [ ] Animasi pulse pada status aktif
- [ ] Distance format benar (m vs km)
- [ ] Waktu format HH:mm
- [ ] Auto-refresh di customer detail works

### **Integration Testing**
- [ ] Customer melihat status real-time
- [ ] Notifikasi muncul saat status berubah
- [ ] Timeline di detail customer update
- [ ] Multiple helps tidak conflict

---

## 💡 Tips Testing

### **Fast Testing (Quick)**
```
1. Ambil bantuan
2. Klik "Teleport"
3. Cek status langsung "arrived"
⏱️ Total: ~10 detik
```

### **Normal Testing (Recommended)**
```
1. Ambil bantuan
2. Set Speed: 2s, Step: 50m
3. Klik "Mulai"
4. Tunggu auto-complete
⏱️ Total: ~1-2 menit (tergantung jarak)
```

### **Detailed Testing (Thorough)**
```
1. Ambil bantuan
2. Set Speed: 5s, Step: 20m
3. Klik "Mulai"
4. Observe setiap perubahan
5. Screenshot timeline di customer view
⏱️ Total: ~3-5 menit
```

---

## 📸 Testing Screenshots

### **Before Testing:**
```
Status: taken
GPS Tracker: "GPS Tracking Aktif"
Simulator: Jarak ~800m
```

### **During Testing:**
```
Status: partner_on_the_way
GPS Tracker: "Jarak: 350m"
Simulator: Aktif (dot berkedip)
Customer View: Timeline updated
```

### **After Testing:**
```
Status: partner_arrived
GPS Tracker: "Rekan jasa tiba di lokasi"
Simulator: Stopped
Customer View: Badge hijau "Sudah sampai"
```

---

## 🚀 Production Notes

**⚠️ PENTING:**

Simulator ini **HANYA** untuk testing di environment:
- `local`
- `development`
- `staging`

Atau untuk user dengan email tertentu (admin).

Di **production**, simulator akan **OTOMATIS HIDDEN** dan GPS menggunakan **real device location**.

Untuk disable simulator di production:
```php
// Di: resources/views/livewire/mitra/dashboard/index.blade.php
@if(config('app.env') !== 'production')
    <livewire:mitra.gps-simulator ... />
@endif
```

---

## 📞 Support

Jika ada masalah saat testing:

1. **Check Logs**: `storage/logs/laravel.log`
2. **Check Database**: Query tabel `helps`
3. **Clear Cache**: `php artisan cache:clear`
4. **Restart Server**: Restart Laravel dan browser

---

**Happy Testing! 🎉**

Last Updated: December 12, 2025
