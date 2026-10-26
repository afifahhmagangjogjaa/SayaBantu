# Skrip utilitas manual

Folder ini **bukan bagian dari aplikasi**. Tidak ada satu pun file di sini yang
di-autoload, dipanggil route, command, atau scheduler. Isinya skrip sekali pakai
untuk debugging dan pengecekan data saat pengembangan.

Semua skrip PHP memakai `scripts/bootstrap.php` untuk memuat autoloader dan
`bootstrap/app.php`, jadi tidak perlu menulis path relatif sendiri-sendiri:

```php
$app = require_once __DIR__.'/bootstrap.php';        // dari scripts/
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
```

Untuk skrip di `legacy/`, pakai `__DIR__.'/../bootstrap.php'`.

Jalankan manual bila perlu, contoh:

```bash
php scripts/check_balance.php 12
php scripts/check_roles.php
```

## Isi

| Skrip | Kegunaan |
|---|---|
| check_balance.php | Cek saldo satu/beberapa user |
| check_balance_sync.php | Bandingkan users.balance vs user_balances |
| check_partner_activities.php | Riwayat aktivitas mitra |
| check_roles.php | Distribusi role user |
| check_transaction.php | Cek satu transaksi |
| create_help_test.php | Buat data bantuan untuk uji manual |
| debug_roots.php | Debug konfigurasi path/root |
| dump_user_txns.php | Dump transaksi milik user |
| force_process.php | Paksa proses antrean tertentu |
| insert_test_activity.php | Sisipkan PartnerActivity untuk uji |
| process_completed_topups.php | Proses topup yang sudah selesai |
| test-helps-data.php | Cek data bantuan |

## legacy/

Skrip lama yang dulu ada di root repo (check_jogja.php, clean-db.php,
seed_jogja_accounts.php, percepat_jadwal.php, test-balance-feature.sh).
Disimpan hanya sebagai referensi; untuk pekerjaan rutin pakai
`app/Console/Commands` atau seeder.

> Catatan: `clean-db.php` menghapus data. Jangan dijalankan pada database
> yang datanya masih dibutuhkan.
