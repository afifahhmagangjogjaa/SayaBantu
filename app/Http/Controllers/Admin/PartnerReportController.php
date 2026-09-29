<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerReport;
use App\Models\User;
use Illuminate\Http\Request;

class PartnerReportController extends Controller
{
    public function index()
    {
        // Build base query for statistics
        $statsQuery = PartnerReport::query();
        
        // Filter by admin's city if user is admin
        if (auth()->user() && auth()->user()->role === 'admin') {
            $adminCityIds = auth()->user()->getAdminCityIds();
            if (!empty($adminCityIds)) {
                $statsQuery->where(function ($q) use ($adminCityIds) {
                    $q->whereHas('reporter', function ($sq) use ($adminCityIds) {
                        $sq->whereIn('city_id', $adminCityIds);
                    })->orWhereHas('reportedUser', function ($sq) use ($adminCityIds) {
                        $sq->whereIn('city_id', $adminCityIds);
                    });
                });
            }
        }

        // Statistik ringkasan
        $totalPending = (clone $statsQuery)->pending()->count();
        $totalInProgress = (clone $statsQuery)->inProgress()->count();
        $totalResolved = (clone $statsQuery)->resolved()->count();
        $totalDismissed = (clone $statsQuery)->dismissed()->count();
        $totalFromCustomer = (clone $statsQuery)->fromCustomer()->count();
        $totalFromMitra = (clone $statsQuery)->fromMitra()->count();

        // Filter parameters
        $status = request('status', 'all');
        $category = request('category', 'all');
        $reportType = request('report_type', 'all');
        $search = request('search');
        $startDate = request('start_date');
        $endDate = request('end_date');

        // Build query
        $query = PartnerReport::with(['reporter', 'reportedUser', 'reportedHelp', 'resolvedBy']);

        // Filter by admin's city if user is admin
        if (auth()->user() && auth()->user()->role === 'admin') {
            $adminCityIds = auth()->user()->getAdminCityIds();
            if (!empty($adminCityIds)) {
                $query->where(function ($q) use ($adminCityIds) {
                    $q->whereHas('reporter', function ($sq) use ($adminCityIds) {
                        $sq->whereIn('city_id', $adminCityIds);
                    })->orWhereHas('reportedUser', function ($sq) use ($adminCityIds) {
                        $sq->whereIn('city_id', $adminCityIds);
                    })->orWhereHas('reportedHelp', function ($sq) use ($adminCityIds) {
                        $sq->whereIn('city_id', $adminCityIds);
                    });
                });
            }
        }

        // Apply filters
        if ($status !== 'all') {
            $query->byStatus($status);
        }

        if ($category !== 'all') {
            if ($category === 'dari_customer') {
                $query->fromCustomer();
            } elseif ($category === 'dari_mitra') {
                $query->fromMitra();
            }
        }

        if ($reportType !== 'all') {
            $query->byReportType($reportType);
        }

        if ($search) {
            $search = trim($search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('reported_user_text', 'like', "%{$search}%")
                    ->orWhere('reported_help_text', 'like', "%{$search}%")
                    ->orWhere('admin_notes', 'like', "%{$search}%")
                    ->orWhere('report_type', 'like', "%{$search}%")
                    ->orWhereHas('reporter', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('reportedUser', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('reportedHelp', function ($sq) use ($search) {
                        $sq->where('title', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
                    });

                // Pencarian berdasarkan status dalam bahasa Indonesia
                $lowerSearch = strtolower($search);
                if (str_contains($lowerSearch, 'tunggu') || str_contains($lowerSearch, 'pending')) {
                    $q->orWhere('status', 'pending');
                }
                if (str_contains($lowerSearch, 'tangan') || str_contains($lowerSearch, 'proses') || str_contains($lowerSearch, 'progress')) {
                    $q->orWhere('status', 'in_progress');
                }
                if (str_contains($lowerSearch, 'selesai') || str_contains($lowerSearch, 'resolve')) {
                    $q->orWhere('status', 'resolved');
                }
                if (str_contains($lowerSearch, 'tolak') || str_contains($lowerSearch, 'tutup') || str_contains($lowerSearch, 'dismiss')) {
                    $q->orWhere('status', 'dismissed');
                }
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Get report types for filter dropdown
        $reportTypes = [
            'mitra_berperilaku_buruk' => 'Mitra Berperilaku Buruk',
            'bantuan_fiktif' => 'Bantuan Fiktif',
            'penipuan' => 'Penipuan',
            'pelanggaran_aturan' => 'Pelanggaran Aturan',
            'konten_tidak_pantas' => 'Konten Tidak Pantas',
            'pelayanan_tidak_sesuai' => 'Pelayanan Tidak Sesuai',
            'pengguna_spam' => 'Pengguna Spam',
            'pengguna_kasar' => 'Pengguna Kasar',
            'data_tidak_valid' => 'Data Tidak Valid',
        ];

        $reports = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.partners.report', compact(
            'reports',
            'totalPending',
            'totalInProgress',
            'totalResolved',
            'totalDismissed',
            'totalFromCustomer',
            'totalFromMitra',
            'reportTypes',
            'status',
            'category',
            'reportType',
            'search',
            'startDate',
            'endDate'
        ));
    }

    public function show(PartnerReport $report)
    {
        $report->load([
            'reporter', 
            'reportedUser', 
            'reportedHelp.messages.mitra', 
            'reportedHelp.messages.customer', 
            'reportedHelp.user', 
            'reportedHelp.mitra', 
            'resolvedBy'
        ]);

        // Cari riwayat chat terkait
        $chats = collect();
        if ($report->reportedHelp && $report->reportedHelp->messages) {
            $chats = $report->reportedHelp->messages()->with(['mitra', 'customer'])->orderBy('created_at', 'asc')->get();
        } elseif ($report->reporter_id && $report->reported_user_id) {
            // Jika tidak ada help_id langsung, cari percakapan antara reporter dan reported user
            $chats = \App\Models\Chat::where(function ($q) use ($report) {
                $q->where('mitra_id', $report->reporter_id)->where('customer_id', $report->reported_user_id);
            })->orWhere(function ($q) use ($report) {
                $q->where('mitra_id', $report->reported_user_id)->where('customer_id', $report->reporter_id);
            })->with(['mitra', 'customer'])->orderBy('created_at', 'asc')->get();
        }

        return view('admin.partners.report-detail', compact('report', 'chats'));
    }

    public function reportsIndex()
    {
        // Alias untuk backward compatibility, redirect ke index
        return redirect()->route('admin.partners.report');
    }

    public function updateStatus(PartnerReport $report, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,dismissed',
        ]);

        $data = ['status' => $request->status];

        // Jika status resolved, set resolved_by dan resolved_at
        if ($request->status === 'resolved') {
            $data['resolved_by'] = auth()->id();
            $data['resolved_at'] = now();
        } elseif ($report->status === 'resolved' && $request->status !== 'resolved') {
            // Jika mengubah dari resolved ke status lain, clear resolved info
            $data['resolved_by'] = null;
            $data['resolved_at'] = null;
        }

        $oldStatus = $report->status;
        $report->update($data);

        // Kirim notifikasi pembaruan status ke pelapor jika status berubah
        $reporterUser = $report->reporter ?? $report->user;
        if ($oldStatus !== $request->status && $reporterUser) {
            try {
                $reporterUser->notify(new \App\Notifications\ReportStatusUpdatedNotification($report, $oldStatus, $request->status));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengirim notifikasi status aduan ke pelapor: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function storeSanction(Request $request, PartnerReport $report)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'warning_level' => 'required|integer|in:0,1,2,3',
            'sanction_reason' => 'nullable|string|max:1000',
        ]);

        // Tentukan user (mitra/customer) yang dijatuhi sanksi
        $targetUser = \App\Models\User::findOrFail($validated['user_id']);

        // Set level Surat Peringatan & alasan
        $targetUser->warning_level = (int) $validated['warning_level'];
        $targetUser->warning_reason = $validated['sanction_reason'] ?? null;
        $targetUser->warning_applied_at = $targetUser->warning_level > 0 ? now() : null;

        // SP 3: JANGAN langsung blokir akun di sini.
        // Pemblokiran terjadi nanti saat user menutup modal pop-up SP 3 (dismissSanctionModal).
        // Ini agar user sempat melihat notifikasi SP 3 sebelum akun benar-benar terkunci.
        if ($targetUser->warning_level < 3) {
            $targetUser->is_banned = false;
            // Jika sebelumnya di-block karena SP dan sekarang diturunkan/dicabut
            if ($targetUser->status === 'blocked') {
                $targetUser->status = 'active';
            }
        }
        // Jika warning_level = 0 (cabut SP), pastikan tidak banned
        if ($targetUser->warning_level === 0) {
            $targetUser->is_banned = false;
            if ($targetUser->status === 'blocked') {
                $targetUser->status = 'active';
            }
        }

        $targetUser->save();

        // Jika SP dicabut (level 0), mark semua notifikasi SP lama yang belum di-pop sebagai sudah dibaca
        // Supaya modal peringatan SP lama (mis. SP 3) tidak muncul lagi setelah pencabutan
        if ($targetUser->warning_level === 0) {
            try {
                $targetUser->notifications()
                    ->whereNull('popped_at')
                    ->where(function ($q) {
                        $q->where('type', 'App\Notifications\SanctionNotification')
                          ->orWhere('data->type', 'sanction_warning');
                    })
                    ->update(['popped_at' => now()]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mark SP notifications as popped: ' . $e->getMessage());
            }
        }

        // Kirim Notifikasi Database Resmi agar pop-up / toast muncul di browser target
        try {
            $targetUser->notify(new \App\Notifications\SanctionNotification(
                $targetUser->warning_level,
                $validated['sanction_reason'] ?? null,
                $report->id
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed sending SanctionNotification: ' . $e->getMessage());
        }

        // Bersihkan session flag pop-up yang mungkin pernah tersimpan agar pop-up PASTI muncul
        try {
            $sessions = \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $targetUser->id)->get();
            foreach ($sessions as $s) {
                $payload = base64_decode($s->payload);
                $data = @unserialize($payload);
                if (is_array($data)) {
                    $changed = false;
                    foreach (array_keys($data) as $k) {
                        if (str_contains($k, 'sanction_sp_popped')) {
                            unset($data[$k]);
                            $changed = true;
                        }
                    }
                    if ($changed) {
                        \Illuminate\Support\Facades\DB::table('sessions')->where('id', $s->id)->update([
                            'payload' => base64_encode(serialize($data))
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore session error
        }

        // Tambahkan ke catatan admin pada laporan agar tercatat jejaknya
        $reasonText = !empty($validated['sanction_reason']) ? ' Alasan: ' . $validated['sanction_reason'] : '';
        $actionText = $targetUser->warning_level > 0
            ? ('memberikan sanksi Surat Peringatan ' . $targetUser->warning_level)
            : 'mencabut/mereset Surat Peringatan (Kembali Normal)';

        $logNote = sprintf(
            "\n[%s] %s %s kepada %s (%s).%s",
            now()->format('d/m/Y H:i'),
            auth()->user()?->name ?? 'Admin',
            $actionText,
            $targetUser->name,
            $targetUser->email,
            $reasonText
        );
        $report->update([
            'admin_notes' => trim(($report->admin_notes ?? '') . $logNote),
        ]);

        $message = $targetUser->warning_level > 0
            ? 'Surat Peringatan ' . $targetUser->warning_level . ' berhasil diberikan kepada ' . $targetUser->name . '.' . ($targetUser->is_banned ? ' Akun telah dibanned.' : '')
            : 'Surat Peringatan untuk ' . $targetUser->name . ' telah dicabut/direset.';

        return redirect()->back()->with('success', $message);
    }

    public function addNote(PartnerReport $report, Request $request)
    {
        $request->validate([
            'note' => 'nullable|string|max:2000',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        if ($request->filled('note')) {
            $timestamp = now()->format('d/m/Y H:i');
            $adminName = auth()->user()?->name ?? 'Admin';
            $entry = "[{$timestamp}] {$adminName}: " . trim($request->note);
            $current = trim($report->admin_notes ?? '');
            $report->update([
                'admin_notes' => $current !== '' ? $current . "\n" . $entry : $entry,
            ]);
            return back()->with('success', 'Catatan baru berhasil ditambahkan.');
        }

        if ($request->has('admin_notes')) {
            $report->update([
                'admin_notes' => $request->admin_notes ? trim($request->admin_notes) : null,
            ]);
            return back()->with('success', 'Riwayat catatan berhasil diperbarui.');
        }

        return back();
    }

    public function resolve(PartnerReport $report)
    {
        $oldStatus = $report->status;
        $report->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        $reporterUser = $report->reporter ?? $report->user;
        if ($oldStatus !== 'resolved' && $reporterUser) {
            try {
                $reporterUser->notify(new \App\Notifications\ReportStatusUpdatedNotification($report, $oldStatus, 'resolved'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengirim notifikasi status aduan ke pelapor: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Laporan telah ditandai sebagai resolved.');
    }

    public function reopen(PartnerReport $report)
    {
        $oldStatus = $report->status;
        $report->update([
            'status' => 'in_progress',
            'resolved_by' => null,
            'resolved_at' => null,
        ]);

        $reporterUser = $report->reporter ?? $report->user;
        if ($oldStatus !== 'in_progress' && $reporterUser) {
            try {
                $reporterUser->notify(new \App\Notifications\ReportStatusUpdatedNotification($report, $oldStatus, 'in_progress'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal mengirim notifikasi status aduan ke pelapor: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Laporan telah dibuka kembali.');
    }
}
