# SayaBantu

Platform bantuan sosial on-demand: **customer** membuat permintaan bantuan, **mitra**
mengerjakan, **admin wilayah** dan **super admin** mengelola verifikasi, moderasi, dan
pencairan dana.

Aplikasi berjalan sebagai web app mobile-first: Laravel 12 + Livewire 3 (Volt), UI
max-width 420px dengan bottom navigation.

---

## Stack

| Bagian | Teknologi |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Livewire 3, Volt, Alpine.js, Tailwind, Vite |
| Auth | Laravel Breeze + verifikasi email + Google OAuth (Socialite) |
| Database | MySQL (produksi) / SQLite (test) |
| Pembayaran | Midtrans (kolom tersedia di `balance_transactions`) |

---

## Requirement

- PHP 8.2 atau lebih baru (ekstensi: pdo, mbstring, gd, fileinfo)
- Composer 2
- Node.js 18+ dan npm
- MySQL 8 / MariaDB

---

## Setup lokal

```bash
# 1. Dependency
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
#    lalu isi DB_DATABASE, DB_USERNAME, DB_PASSWORD, dan kredensial Google bila dipakai

# 3. Database
php artisan migrate --seed

# 4. Storage & aset
php artisan storage:link
npm run build

# 5. Jalankan
php artisan serve
# atau, sekaligus server + queue + scheduler + log + vite:
composer dev
```

Buka http://127.0.0.1:8000

### Login demo

`DEMO_LOGIN=true` di `.env` mengaktifkan toleransi password demo
(`Sayabantu123!`, `sayabantu123`, `password`) untuk akun yang memang memakai
salah satu password tersebut. **Jangan pernah mengaktifkannya di production** —
default di `.env.example` adalah `false`.

---

## Yang wajib jalan di background

| Proses | Perintah | Kenapa perlu |
|---|---|---|
| Scheduler | `php artisan schedule:run` tiap menit (cron) atau `php artisan schedule:work` saat dev | `helps:auto-confirm` (tiap 10 menit) dan `helps:auto-cancel` (tiap menit) |

Di server, tambahkan cron:

```cron
* * * * * cd /path/to/sayabantu && php artisan schedule:run >> /dev/null 2>&1
```

Queue worker **belum dibutuhkan**: saat ini semua notifikasi dikirim sinkron (tidak ada
job yang di-dispatch). `QUEUE_CONNECTION` tetap diisi karena suatu saat bisa dipakai —
kalau nanti notifikasi dijadikan `ShouldQueue`, jalankan `php artisan queue:work`.

---

## Struktur penting

```
app/
  Enums/HelpStatus.php            Satu-satunya sumber nilai status bantuan (case + alias legacy)
  Models/Help.php                 Model inti transaksi bantuan
  Models/Concerns/                Trait: status, journey timeline, payout, pembatalan mitra
  Livewire/{Customer,Mitra,Admin,SuperAdmin}/
  Livewire/Forms/LoginForm.php    Login email atau nomor HP
  Support/JsonMaster.php          Cache master wilayah (districts/villages)
  Support/Sql.php                 Ekspresi SQL portabel antar driver
  Observers/HelpObserver.php      Efek samping perubahan status (refund, pencairan saldo)
  Console/Commands/               helps:auto-confirm, helps:auto-cancel, dan perintah maintenance

routes/
  web.php                         Rute publik, auth/onboarding, dan rute bersama
  customer.php mitra.php admin.php superadmin.php   Rute per role
  auth.php                        Login, Google OAuth, reset password
  console.php                     Jadwal (scheduler)

database/migrations/              Termasuk normalisasi status help dan perbaikan FK cities
scripts/                          Skrip utilitas manual (tidak dipakai aplikasi) - lihat scripts/README.md
```

---

## Role dan alur singkat

1. **Registrasi** — `/register` memilih role, lalu data akun disimpan di session.
2. **Onboarding 4 langkah** — data diri/KTP, foto KTP, selfie, review. Akun dibuat di langkah akhir.
3. **Verifikasi** — admin memverifikasi data dan KTP; status akun berpengaruh ke kuota order.
4. **Bantuan** — customer membuat permintaan (saldo ditahan), mitra mengambil order,
   tracking perjalanan, chat, upload bukti, customer mengonfirmasi selesai.
5. **Saldo** — dana masuk ke mitra setelah selesai terkonfirmasi, lalu bisa ditarik (withdraw)
   dan disetujui admin/super admin.

Status bantuan yang dipakai sekarang:

```
menunggu_mitra -> memperoleh_mitra -> partner_on_the_way -> partner_arrived
-> in_progress -> waiting_customer_confirmation -> selesai
                                      `-> dibatalkan / rejected
```

Nilai lama (`approved`, `taken`, `completed`, `cancelled`, `pending`) tetap dikenali lewat
`HelpStatus::aliases()` dan sudah dinormalkan oleh migration.

---

## Perintah berguna

```bash
php artisan test                      # seluruh test
php artisan test tests/Feature/Smoke  # smoke test halaman per role
php artisan route:list                # daftar rute per role
php artisan schedule:list             # jadwal yang aktif
php artisan helps:auto-cancel         # jalankan manual pembatalan order kedaluwarsa
php artisan helps:auto-confirm        # jalankan manual konfirmasi otomatis 24 jam
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

---

## Testing

Test memakai SQLite in-memory (`phpunit.xml`), jadi tidak menyentuh database MySQL Anda.

Cakupan saat ini: autentikasi, verifikasi email, registrasi multi-step, profil, alur saldo
dan withdraw, normalisasi status, serta smoke test seluruh halaman utama per role
(customer, mitra, admin, super admin).

---

## Status fitur

Sudah ada:

- Registrasi email + verifikasi, login email/nomor HP, Google OAuth
- Onboarding KTP dan verifikasi manual oleh admin
- Permintaan bantuan (terjadwal dan urgent), auto-cancel, auto-confirm 24 jam
- Tracking perjalanan mitra (GPS) beserta simulasi
- Chat customer-mitra (polling 5 detik), rating dua arah, laporan pelanggaran + sanksi
- Saldo, topup dengan bukti transfer + approval, withdraw dengan approval
- Panel admin wilayah dan super admin, activity log, export transaksi (Excel)

Belum ada:

- Notifikasi real-time (Pusher/Laravel Echo) dan push notification
- Verifikasi KTP otomatis (OCR)
- Export laporan PDF

---

## Catatan

- Log memakai channel `daily` (`LOG_STACK=daily`), retensi 14 hari.
- `APP_DEBUG=false` di `.env.example`; jangan pernah `true` di production.
- Aplikasi ini dibuat untuk tujuan pembelajaran dan sosial.
