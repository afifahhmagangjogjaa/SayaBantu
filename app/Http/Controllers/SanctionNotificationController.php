<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class SanctionNotificationController extends Controller
{
    public function show($id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        // Keamanan: hanya pemilik notifikasi yang dapat membuka surat sanksi miliknya
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $type = $notification->data['type'] ?? '';
        if ($type !== 'sanction_warning' && !str_contains($type, 'sanction')) {
            abort(404, 'Bukan notifikasi surat peringatan.');
        }

        // Tandai otomatis sudah dibaca saat dibuka detailnya
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $data = $notification->data ?? [];
        $warningLevel = (int) ($data['warning_level'] ?? 1);
        $reason = $data['reason'] ?? null;
        $reportId = $data['report_id'] ?? null;

        return view('notifications.sanction-detail', [
            'notification' => $notification,
            'warningLevel' => $warningLevel,
            'reason' => $reason,
            'reportId' => $reportId,
            'user' => auth()->user(),
        ]);
    }

    public function acknowledge($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $notification = DatabaseNotification::findOrFail($id);

        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $data = $notification->data ?? [];
        $warningLevel = (int) ($data['warning_level'] ?? 0);

        // Hanya SP 3 yang memicu ban + logout dari sini
        if ($warningLevel >= 3) {
            $user = auth()->user();
            $user->is_banned = true;
            $user->status = 'blocked';
            $user->save();

            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan/diblokir secara permanen dari sistem karena menerima Surat Peringatan 3.');
        }

        // SP 1/2: cukup kembali ke notifikasi
        return redirect()->route(auth()->user()->role === 'mitra' ? 'mitra.notifications.index' : 'customer.notifications.index')
            ->with('success', 'Surat Peringatan telah dikonfirmasi.');
    }
}
