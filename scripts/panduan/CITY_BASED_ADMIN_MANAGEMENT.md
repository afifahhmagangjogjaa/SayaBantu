# City-Based Admin Management

## Overview
Sistem ini memungkinkan Super Admin untuk menugaskan Admin ke kota tertentu. Admin yang ditugaskan hanya dapat mengelola user (customer dan mitra) dari kota yang ditugaskan.

## Fitur

### 1. Penugasan Admin ke Kota
- Super Admin dapat memilih **semua user dengan role admin** di dropdown saat membuat/edit kota
- Satu admin hanya bisa ditugaskan ke satu kota
- Ketika admin ditugaskan ke kota, `city_id` pada user admin tersebut akan otomatis diupdate

### 2. Filter Berdasarkan Kota
Admin yang ditugaskan ke kota tertentu akan memiliki akses terbatas:

#### Halaman yang Difilter:
- **Dashboard** (`/admin`)
  - Semua statistik (total helps, pending helps, dll) difilter berdasarkan kota admin
  - Grafik aktivitas hanya menampilkan data dari kota admin

- **User Management** (`/admin/users`)
  - Hanya menampilkan user (customer & mitra) dari kota admin
  - Admin hanya bisa create/edit/delete user di kota mereka

- **Verifikasi KTP** (`/admin/verifications`)
  - Hanya menampilkan registrasi dari kota admin
  - Admin hanya bisa approve/reject registrasi dari kota mereka

- **Bantuan/Helps** (`/admin/helps`)
  - Hanya menampilkan bantuan dari customer di kota admin
  - Statistik bantuan difilter berdasarkan kota

- **Aktivitas Mitra** (`/admin/partners/activity`)
  - Hanya menampilkan aktivitas **customer dan mitra** dari kota admin (tidak termasuk aktivitas admin lain)
  - Statistik mitra aktif difilter berdasarkan kota
  - **Catatan:** Admin tidak melihat aktivitas admin lain, hanya user customer dan mitra di kotanya

- **Top-up Approval** (`/admin/topup/approvals`)
  - Hanya menampilkan request top-up dari user di kota admin
  - Admin hanya bisa approve/reject top-up dari user di kota mereka

## Implementasi Teknis

### Database
- Kolom `city_id` sudah ada di tabel `users`
- Kolom `city_id` sudah ada di tabel `registrations`
- Kolom `admin_id` ada di tabel `cities`

### Model Relationships
```php
// User.php
public function city()
{
    return $this->belongsTo(City::class);
}

// City.php
public function admin()
{
    return $this->belongsTo(User::class, 'admin_id');
}

public function users()
{
    return $this->hasMany(User::class);
}
```

### Helper Trait
Trait `FiltersByCityForAdmin` tersedia di `app/Traits/FiltersByCityForAdmin.php` untuk mempermudah filtering:

```php
use App\Traits\FiltersByCityForAdmin;

class YourComponent extends Component
{
    use FiltersByCityForAdmin;

    public function render()
    {
        $query = YourModel::query();
        
        // Filter langsung berdasarkan kolom city_id
        $this->applyCityFilter($query);
        
        // Atau filter via relasi
        $this->applyCityFilterViaRelation($query, 'user');
        
        return view('your.view', [
            'data' => $query->paginate(10)
        ]);
    }
}
```

### Contoh Filter Manual
```php
// Filter user dari kota admin
if (auth()->user() && auth()->user()->role === 'admin' && auth()->user()->city_id) {
    $query->where('city_id', auth()->user()->city_id);
}

// Filter via relasi (contoh: Help memiliki relasi customer)
if (auth()->user() && auth()->user()->role === 'admin' && auth()->user()->city_id) {
    $query->whereHas('customer', function ($q) {
        $q->where('city_id', auth()->user()->city_id);
    });
}
```

## Security

### Authorization
- Admin hanya bisa approve/reject/delete data dari kota mereka
- Admin hanya melihat aktivitas customer dan mitra (tidak melihat aktivitas admin lain)
- Validasi ada di setiap method action (approve, reject, delete, dll)
- Super Admin tidak memiliki batasan akses dan melihat semua data dari semua role (admin, customer, mitra)

### Middleware
- `EnsureAdmin` - Memastikan user adalah admin
- `EnsureSuperAdmin` - Memastikan user adalah super admin

## Activity Logs Filtering

### Admin Panel Activity Logs
Admin yang ditugaskan ke kota tertentu hanya akan melihat:
- ✅ Aktivitas **customer** dari kota mereka
- ✅ Aktivitas **mitra** dari kota mereka
- ❌ **TIDAK** melihat aktivitas admin lain

Ini memastikan admin fokus pada pengelolaan user end-user di kota mereka dan tidak melihat aktivitas internal admin lain.

### SuperAdmin Panel Activity Logs
Super Admin melihat **SEMUA** aktivitas dari:
- ✅ Aktivitas **admin** (semua kota)
- ✅ Aktivitas **customer** (semua kota)
- ✅ Aktivitas **mitra** (semua kota)

Super Admin memiliki visibilitas penuh untuk monitoring dan audit.

## Testing

### Manual Testing Steps
1. Login sebagai Super Admin
2. Buat/edit kota dan assign admin
3. Logout dan login sebagai admin yang sudah ditugaskan
4. Verifikasi bahwa:
   - Dashboard hanya menampilkan data dari kota admin
   - User management hanya menampilkan user dari kota admin
   - Verifikasi KTP hanya menampilkan registrasi dari kota admin
   - Helps hanya menampilkan bantuan dari customer kota admin
   - Top-up approval hanya menampilkan request dari user kota admin

## Files Modified

### SuperAdmin Components
- `app/Livewire/SuperAdmin/Cities.php` - Update untuk menampilkan semua admin di dropdown

### Admin Components (Added City Filtering)
- `app/Livewire/Admin/Dashboard.php` - Sudah ada filter
- `app/Livewire/Admin/Users/Index.php` - Sudah ada filter
- `app/Livewire/Admin/Helps/Index.php` - Added filter
- `app/Livewire/Admin/Partners/ActivityLive.php` - Added filter
- `app/Livewire/Admin/Verifications/Index.php` - Added filter
- `app/Livewire/Admin/Verifications/IndexNew.php` - Added filter
- `app/Livewire/Admin/TopupApproval.php` - Sudah ada filter

### New Files
- `app/Traits/FiltersByCityForAdmin.php` - Helper trait untuk filtering

## Future Improvements
- [ ] Add city filter dropdown di setiap halaman untuk Super Admin
- [ ] Tambahkan audit log untuk perubahan penugasan admin
- [ ] Buat report per-kota untuk Super Admin
- [ ] Tambahkan notification ketika admin ditugaskan/diubah ke kota lain
