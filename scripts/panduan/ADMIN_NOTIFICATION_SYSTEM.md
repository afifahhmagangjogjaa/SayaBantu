# Admin Notification System

## Overview
Sistem notifikasi admin panel telah diimplementasikan dengan fitur lengkap untuk menampilkan dan mengelola notifikasi secara real-time.

## Fitur

### 1. **Notifikasi Real-time**
- Auto-refresh setiap 30 detik menggunakan `wire:poll.30s`
- Event listener untuk update instan saat ada notifikasi baru
- Badge counter menampilkan jumlah notifikasi yang belum dibaca

### 2. **Dropdown Notifikasi**
- Klik icon bell untuk membuka dropdown
- Menampilkan 10 notifikasi terbaru
- Auto-close saat klik di luar dropdown menggunakan Alpine.js `@click.away`
- Animasi smooth dengan transitions

### 3. **Jenis Notifikasi**
Sistem mendukung beberapa tipe notifikasi dengan icon yang berbeda:

#### 💰 Request Top-Up Baru (`new_topup_request`)
- Icon: Coin (hijau)
- Menampilkan nama customer, jumlah top-up, dan kode request
- Link langsung ke halaman approval

#### 🤝 Bantuan Diambil (`help_taken`)
- Icon: Users (ungu)
- Notifikasi saat mitra mengambil bantuan

#### 👤 Pendaftaran Baru (`new_registration`)
- Icon: User Add (kuning)
- Notifikasi saat ada pendaftaran user baru

#### 📢 Notifikasi Umum (default)
- Icon: Info (biru)
- Untuk notifikasi lainnya

### 4. **Aksi Notifikasi**
- **Tandai Dibaca**: Mark individual notification sebagai read
- **Tandai Semua Dibaca**: Mark all notifications sebagai read sekaligus
- **Hapus**: Delete individual notification
- **Auto-redirect**: Klik notifikasi untuk redirect ke halaman terkait

### 5. **Status Visual**
- Notifikasi belum dibaca: Background biru muda (`bg-blue-50`)
- Notifikasi sudah dibaca: Background putih
- Badge merah pada icon bell saat ada unread notifications
- Timestamp relative (e.g., "2 menit yang lalu")

## Komponen

### Livewire Component
**Path**: `app/Livewire/Admin/Notifications.php`

**Methods**:
- `mount()`: Load initial notifications
- `loadNotifications()`: Fetch 10 latest notifications
- `refreshNotifications()`: Refresh notification list (triggered by events)
- `toggleDropdown()`: Show/hide dropdown
- `markAsRead($id)`: Mark single notification as read
- `markAllAsRead()`: Mark all notifications as read
- `deleteNotification($id)`: Delete notification

**Listeners**:
- `topupRequestCreated`: Auto-refresh saat ada request top-up baru
- `notificationRead`: Auto-refresh saat notifikasi dibaca

### View
**Path**: `resources/views/livewire/admin/notifications.blade.php`

Menggunakan:
- Livewire untuk reactivity
- Alpine.js untuk dropdown interactivity
- Tailwind CSS untuk styling

## Integrasi

### 1. Layout Admin
Komponen notifikasi terintegrasi di navbar admin:
```blade
@livewire('admin.notifications')
```

### 2. Event Dispatch
Saat customer membuat top-up request, event di-dispatch:
```php
// app/Livewire/Customer/TopupRequest.php
$this->dispatch('topupRequestCreated');
```

### 3. Notification Class
Menggunakan Laravel Notification system:
```php
use App\Notifications\NewTopupRequest;
$admin->notify(new NewTopupRequest($transaction));
```

## Testing

### Seeder
Jalankan seeder untuk membuat test notifications:
```bash
php artisan db:seed --class=AdminNotificationSeeder
```

Ini akan membuat:
- 2 notifikasi top-up (1 unread, 1 read)
- 1 notifikasi pendaftaran
- 1 notifikasi bantuan diambil

## Customization

### Menambah Tipe Notifikasi Baru

1. **Buat Notification Class**:
```bash
php artisan make:notification YourNotification
```

2. **Update toArray() method** dengan type:
```php
public function toArray($notifiable): array
{
    return [
        'type' => 'your_notification_type',
        'message' => 'Your message',
        // ... data lainnya
    ];
}
```

3. **Update view** untuk handle tipe baru:
```blade
@elseif($data['type'] === 'your_notification_type')
    <!-- Your custom notification UI -->
@endif
```

## Dependencies

- **Laravel Livewire 3.x**: Untuk reactivity
- **Alpine.js 3.14.1**: Untuk dropdown interactivity
- **Tailwind CSS**: Untuk styling

## Browser Compatibility

Tested dan berfungsi di:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)

## Performance

- **Polling**: 30 detik interval (dapat disesuaikan)
- **Database Query**: Only fetch 10 latest notifications
- **Lazy Loading**: Dropdown content only loaded when opened
- **Auto-cleanup**: Old notifications can be deleted by user

## Future Enhancements

Potential improvements:
1. Push notifications menggunakan WebSockets (Pusher/Laravel Echo)
2. Sound notification untuk notifikasi penting
3. Filter notifikasi by type
4. Pagination untuk notifikasi lama
5. Export notification history
6. Notification preferences per admin
