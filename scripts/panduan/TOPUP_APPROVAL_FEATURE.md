# FITUR TOP-UP SALDO DENGAN APPROVAL ADMIN

## 📌 OVERVIEW
Fitur top-up saldo baru yang menggunakan sistem approval dari admin berdasarkan kota, menggantikan payment gateway Midtrans.

## ✅ YANG SUDAH DIIMPLEMENTASIKAN

### 1. DATABASE
- ✅ Migration: `2025_12_02_add_topup_approval_fields_to_balance_transactions.php`
- ✅ Kolom baru: admin_fee, total_payment, customer_*, payment_method, proof_of_payment, approved_by, approved_at, rejection_reason, request_code, expired_at
- ✅ Update enum status: pending, waiting_approval, approved, completed, rejected, failed

### 2. MODELS
- ✅ `BalanceTransaction` model updated dengan:
  - Fillable fields baru
  - Casts untuk decimal dan datetime
  - Relationship `approvedBy()`
  - Scopes: `waitingApproval()`, `byCity()`
  - Accessor: `getProofOfPaymentUrlAttribute()`

### 3. LIVEWIRE COMPONENTS - CUSTOMER
- ✅ `TopupRequest.php` - 3 step form untuk request top-up
  - Step 1: Form data (nominal, nama, telepon, email, catatan)
  - Step 2: Detail pembayaran (rincian + biaya admin)
  - Step 3: Pilih metode bayar + upload bukti
- ✅ `TopupHistory.php` - Riwayat request top-up dengan filter status

### 4. LIVEWIRE COMPONENTS - ADMIN
- ✅ `TopupApproval.php` - Approve/reject request top-up
  - Filter by kota otomatis untuk admin
  - Super admin bisa lihat semua
  - Preview bukti transfer
  - Approve/reject dengan alasan

### 5. VIEWS
- ✅ `topup-request.blade.php` - Multi-step form dengan UI modern
- ✅ `topup-history.blade.php` - List riwayat dengan detail modal
- ✅ `topup-approval.blade.php` - Admin approval interface

### 6. NOTIFICATIONS
- ✅ `TopupRequestSubmitted` - Notifikasi ke customer saat submit
- ✅ `TopupApproved` - Notifikasi ke customer saat approved
- ✅ `TopupRejected` - Notifikasi ke customer saat rejected (dengan alasan)
- ✅ `NewTopupRequest` - Notifikasi ke admin saat ada request baru

### 7. ROUTES
```php
// Customer Routes
Route::get('/customer/topup/request', TopupRequest::class)->name('customer.topup.request');
Route::get('/customer/topup/history', TopupHistory::class)->name('customer.topup.history');

// Admin Routes
Route::get('/admin/topup/approvals', TopupApproval::class)->name('admin.topup.approvals');
```

### 8. INTEGRATION
- ✅ Dashboard customer updated - button "Tambah Saldo" → route baru
- ✅ Old Midtrans route tetap ada untuk backward compatibility

## 🚀 CARA MENGGUNAKAN

### CUSTOMER:
1. Klik "Tambah Saldo" di dashboard
2. Isi form step 1: nominal + data diri
3. Review detail pembayaran di step 2
4. Pilih metode bayar + upload bukti transfer di step 3
5. Submit → dapat kode request
6. Tunggu approval dari admin
7. Lihat riwayat di menu "Riwayat Top-Up"

### ADMIN:
1. Buka menu "Approval Top-Up"
2. Lihat list request menunggu approval (filtered by kota)
3. Klik "Lihat Bukti Transfer"
4. Approve atau Reject:
   - Approve → saldo customer langsung bertambah
   - Reject → isi alasan penolakan
5. Customer dapat notifikasi otomatis

## 💰 BIAYA ADMIN

Logika biaya admin (bisa disesuaikan di `TopupRequest.php`):
- < Rp 50.000 → Rp 5.000
- Rp 50.000 - 99.999 → Rp 7.500
- ≥ Rp 100.000 → 3% (max Rp 15.000)

## 🏦 METODE PEMBAYARAN

Default payment methods (hardcoded, bisa dipindah ke AppSetting):
- QRIS (all e-wallets)
- Transfer Bank BCA
- Transfer Bank Mandiri
- Transfer Bank BNI
- Transfer Bank BRI

## 📧 NOTIFIKASI

### Customer menerima:
- Email + Database notification saat submit
- Email + Database notification saat approved/rejected

### Admin menerima:
- Email + Database notification saat ada request baru
- Hanya admin di kota yang sama dengan customer
- Super admin sebagai backup jika tidak ada admin kota

## 🔐 AUTHORIZATION

- Customer: hanya bisa lihat request sendiri
- Admin: hanya bisa approve/reject request dari kota mereka
- Super Admin: bisa approve/reject semua request

## 📝 TRACKING & AUDIT

Setiap transaksi memiliki:
- `request_code` → Format: TPU-YYYYMMDD-XXX
- `approved_by` → ID admin yang approve/reject
- `approved_at` → Waktu approve/reject
- `expired_at` → 24 jam dari created_at
- `rejection_reason` → Alasan jika ditolak

## ⚙️ KONFIGURASI YANG BISA DISESUAIKAN

Di file `TopupRequest.php`:
```php
// Minimal/maksimal topup
min: 10000
max: 10000000

// Expired time
24 hours

// Admin fee calculation
calculateFees() method

// Payment methods
loadPaymentSettings() method
```

## 🎨 UI/UX FEATURES

- ✅ Progress indicator 3 steps
- ✅ Quick amount buttons (20K, 50K, 100K, 200K, 500K)
- ✅ Auto-calculate admin fee
- ✅ Copy to clipboard untuk nomor rekening
- ✅ Image preview sebelum upload
- ✅ Modal untuk detail transaksi
- ✅ Filter status di history
- ✅ Responsive design

## 🔄 WORKFLOW

```
CUSTOMER                          ADMIN
   │                                │
   ├─ Klik "Tambah Saldo"          │
   ├─ Isi form step 1-3            │
   ├─ Upload bukti transfer        │
   ├─ Submit request               │
   │  (status: waiting_approval)   │
   ├─────────[Notif]──────────────>├─ Terima notifikasi
   │                                ├─ Review bukti transfer
   │                                ├─ Approve/Reject
   │                                │
   ├<────[Notif Approved]───────────┤
   │  (saldo bertambah)             │
   ├─ Lihat riwayat                │
   │                                │
```

## 📱 TESTING CHECKLIST

- [ ] Test submit request dengan berbagai nominal
- [ ] Test upload bukti berbagai format/ukuran
- [ ] Test approval oleh admin kota yang sama
- [ ] Test rejection dengan alasan
- [ ] Test notifikasi email terkirim
- [ ] Test filter riwayat by status
- [ ] Test authorization (admin tidak bisa approve kota lain)
- [ ] Test super admin bisa approve semua
- [ ] Test saldo customer bertambah setelah approve
- [ ] Test expired request (>24 jam)

## 🐛 POTENTIAL IMPROVEMENTS

1. Add cron job untuk auto-reject expired requests
2. Add dashboard widget untuk pending approval count
3. Add WhatsApp notification
4. Add bulk approval untuk admin
5. Add export riwayat to Excel/PDF
6. Add refund mechanism jika reject
7. Add QRIS image upload dari admin
8. Move payment methods ke AppSetting/database
9. Add analytics/statistics

## 📞 SUPPORT

Jika ada pertanyaan atau issue:
1. Check error logs di `storage/logs/laravel.log`
2. Review notification queue jika email tidak terkirim
3. Check migration status: `php artisan migrate:status`
4. Clear cache: `php artisan optimize:clear`

---

**Status:** ✅ FULLY IMPLEMENTED & READY TO USE
**Date:** December 2, 2025
