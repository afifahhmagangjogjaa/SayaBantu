<?php

use Illuminate\Support\Facades\Route;

// Landing/Welcome page (for guest)
Route::view('/welcome', 'welcome')->name('welcome');

// Rejected registration page (public)
Route::get('/rejected/{registration}', [\App\Http\Controllers\Auth\RejectedController::class, 'show'])->name('auth.rejected');

// Public routes - Home page with helps listing
Route::view('/', 'home')->name('home');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Main Dashboard route - redirects based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'mitra') {
            return redirect()->route('mitra.dashboard');
        } elseif ($user->role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Default: redirect to customer dashboard
        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    // Chat shortcut routes (open a specific conversation)
    Route::get('/chat/start', [\App\Http\Controllers\ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat/{help}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');

    // Lightweight AJAX endpoints for authenticated users
    Route::get('/ajax/cities', [\App\Http\Controllers\Api\CityController::class, 'search'])->name('ajax.cities');
    Route::get('/check-account-status', function () {
        $user = auth()->user();
        return response()->json([
            'status' => $user->status,
            'is_blocked' => $user->status === 'blocked',
        ]);
    })->name('account.status.check');

    // ========================================
    // CUSTOMER ROUTES (Customer/Penerima Bantuan)
    // ========================================
    Route::prefix('customer')->name('customer.')->middleware('customer')->group(function () {
        // Dashboard
        Route::get('/dashboard', \App\Livewire\Customer\Dashboard::class)->name('dashboard');

        // Helps Management (specific routes BEFORE general routes)
        Route::get('/helps/create', \App\Livewire\Customer\Helps\Create::class)->name('helps.create');
        Route::get('/helps/history', \App\Livewire\Customer\Helps\History::class)->name('helps.history');
        Route::get('/helps/{id}/detail', \App\Livewire\Customer\Helps\Detail::class)->name('helps.detail');
        Route::get('/helps', \App\Livewire\Customer\Helps\Index::class)->name('helps.index');

        // Notifications
        Route::get('/notifications', \App\Livewire\Customer\Notifications\Index::class)->name('notifications.index');

        // Balance & Transactions
        // Route untuk balance management bisa ditambahkan di sini
        Route::get('/transactions', \App\Livewire\Customer\Transactions\Index::class)->name('transactions.index');

        // Top Up Saldo Biasa (Upload Bukti Transfer & Konfirmasi Admin)
        Route::get('/topup', \App\Livewire\Customer\TopupRequest::class)->name('topup');
        Route::get('/top-up', \App\Livewire\Customer\TopupRequest::class);
        Route::get('/topup/request', \App\Livewire\Customer\TopupRequest::class)->name('topup.request');
        Route::get('/topup/history', \App\Livewire\Customer\TopupHistory::class)->name('topup.history');

        // Top Up Saldo Instan / Urgent (Otomatis via Midtrans - Dipakai saat Buat Bantuan)
        Route::get('/topup/instant', \App\Livewire\Customer\Topup::class)->name('topup.instant');
        Route::get('/topup/midtrans', \App\Livewire\Customer\Topup::class)->name('topup.midtrans');

        // Chat (optional help id for opening detail directly)
        Route::get('/chat/{help?}', \App\Livewire\Customer\Chat::class)->name('chat');

        // Ratings (customer receives ratings from mitra)
        Route::get('/ratings', \App\Http\Livewire\Customer\Ratings\Index::class)->name('ratings');

        // Help & Support
        Route::view('/help-support', 'customer.help-support')->name('help-support');

        // Lightweight endpoint to fetch current tracking coordinates (used by client-side polling)
        Route::get('/helps/{id}/tracking', function ($id) {
            $help = \App\Models\Help::find($id);
            if (! $help) {
                return response()->json(['error' => 'Not found'], 404);
            }

            return response()->json([
                'partnerLat' => $help->partner_current_lat,
                'partnerLng' => $help->partner_current_lng,
                'customerLat' => $help->latitude,
                'customerLng' => $help->longitude,
                'partnerName' => $help->mitra?->name ?? null,
                'updated_at' => $help->updated_at?->toDateTimeString(),
            ]);
        })->name('customer.helps.tracking');

        // Lightweight JSON endpoint to fetch help details (used by JS fallback in preview modals)
        Route::get('/helps/{id}/json', function ($id) {
            $help = \App\Models\Help::with(['city','mitra','user'])->find($id);
            if (! $help) return response()->json(['error' => 'Not found'], 404);

            return response()->json([
                'id' => $help->id,
                'scheduled_at' => $help->scheduled_at?->toDateTimeString(),
                'title' => $help->title,
                'amount' => $help->amount,
            ]);
        })->name('helps.json');

        // Reports
        Route::get('/reports/create', \App\Livewire\Customer\Reports\Create::class)->name('reports.create');
        Route::get('/reports/create/user/{user_id}', \App\Livewire\Customer\Reports\Create::class)->name('reports.create.user');
        Route::get('/reports/create/help/{help_id}', \App\Livewire\Customer\Reports\Create::class)->name('reports.create.help');
        Route::get('/reports/{report}/status-check', function (\App\Models\PartnerReport $report) {
            if ($report->reporter_id !== auth()->id() && $report->reported_user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return response()->json([
                'id' => $report->id,
                'status' => $report->status,
                'admin_notes' => $report->admin_notes,
                'resolved_at' => $report->resolved_at?->toIso8601String(),
                'updated_at' => $report->updated_at?->toIso8601String(),
            ]);
        })->name('reports.status-check');
        Route::get('/reports/{report}', function (\App\Models\PartnerReport $report) {
            if ($report->reporter_id !== auth()->id() && $report->reported_user_id !== auth()->id()) {
                abort(403);
            }
            return view('livewire.customer.reports.show', ['report' => $report]);
        })->name('reports.show');
    });

    // ========================================
    // MITRA ROUTES (Volunteer/Pemberi Bantuan)
    // ========================================
    Route::prefix('mitra')->name('mitra.')->middleware('mitra')->group(function () {
        // Dashboard
        Route::get('/dashboard', \App\Livewire\Mitra\Dashboard\Index::class)->name('dashboard');

        // Helps Management
        Route::get('/helps', \App\Livewire\Mitra\Helps\AllHelps::class)->name('helps.all');
        Route::get('/helps/completed', \App\Livewire\Mitra\Helps\CompletedHelps::class)->name('helps.completed');
        Route::get('/helps/{id}/detail', \App\Livewire\Mitra\Helps\HelpDetail::class)->name('helps.detail');

        // Profile
        Route::get('/profile', \App\Livewire\Mitra\Profile\Index::class)->name('profile');
        // Edit profile page for mitra (full page, not modal)
        Route::get('/profile/edit', \App\Livewire\Mitra\Profile\EditPage::class)->name('profile.edit');

        // Chat (optional help id for opening detail directly)
        Route::get('/chat/{help?}', \App\Livewire\Mitra\Chat\Index::class)->name('chat');

        // Notifications (Mitra)
        Route::get('/notifications', \App\Livewire\Mitra\Notifications\Index::class)->name('notifications.index');

        // Reports
        Route::get('/reports/create', \App\Livewire\Mitra\Reports\Create::class)->name('reports.create');
        Route::get('/reports/create/user/{user_id}', \App\Livewire\Mitra\Reports\Create::class)->name('reports.create.user');
        Route::get('/reports/create/help/{help_id}', \App\Livewire\Mitra\Reports\Create::class)->name('reports.create.help');
        Route::get('/reports/{report}/status-check', function (\App\Models\PartnerReport $report) {
            if ($report->reporter_id !== auth()->id() && $report->reported_user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return response()->json([
                'id' => $report->id,
                'status' => $report->status,
                'admin_notes' => $report->admin_notes,
                'resolved_at' => $report->resolved_at?->toIso8601String(),
                'updated_at' => $report->updated_at?->toIso8601String(),
            ]);
        })->name('reports.status-check');
        // Show a submitted report (mitra can view their own submitted report status)
        Route::get('/reports/{report}', function (\App\Models\PartnerReport $report) {
            if ($report->reporter_id !== auth()->id() && $report->reported_user_id !== auth()->id()) {
                abort(403);
            }
            return view('livewire.mitra.reports.show', ['report' => $report]);
        })->name('reports.show');

        // Processing helps (in-progress) - page for mitra to manage helps they are currently handling
        Route::get('/helps/processing', \App\Livewire\Mitra\Helps\ProcessingHelps::class)->name('helps.processing');

        // Ratings
        Route::get('/ratings', \App\Livewire\Mitra\Ratings\Index::class)->name('ratings');

        // Settings (Mitra) - similar to customer profile settings
        Route::view('/settings', 'mitra.settings')->name('settings');
        Route::view('/settings/notifications', 'mitra.settings.notifications')->name('settings.notifications');
        Route::view('/settings/password', 'mitra.settings.password')->name('settings.password');

        // Help & Support
        Route::view('/help-support', 'mitra.help-support')->name('help-support');

        // Withdraw (Mitra) - form & history
        Route::get('/withdraw', [\App\Http\Controllers\WithdrawController::class, 'showForm'])->name('withdraw.form');
        Route::post('/withdraw', [\App\Http\Controllers\WithdrawController::class, 'requestWithdraw'])->name('withdraw.request');
        Route::get('/withdraw/history', [\App\Http\Controllers\WithdrawController::class, 'withdrawHistory'])->name('withdraw.history');
        Route::get('/withdraw/success/{withdraw}', [\App\Http\Controllers\WithdrawController::class, 'showSuccess'])->name('withdraw.success');
        Route::get('/withdraw/rejected/{withdraw}', [\App\Http\Controllers\WithdrawController::class, 'showRejected'])->name('withdraw.rejected');
    });

    // ========================================
    // SHARED ROUTES (Accessible by both)
    // ========================================
    // Profile Management (accessible by all authenticated users)
    Route::view('/profile', 'profile')->name('profile');
    Route::view('/profile/edit', 'profile.edit')->name('profile.edit');
    Route::view('/profile/settings', 'profile.settings')->name('profile.settings');
    Route::view('/profile/settings/notifications', 'profile.settings.notifications')->name('profile.settings.notifications');
    Route::view('/profile/settings/password', 'profile.settings.password')->name('profile.settings.password');
    Route::view('/profile/settings/verification', 'profile.settings.verification')->name('profile.settings.verification');

    Route::put('/profile/password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai / salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.different' => 'Kata sandi baru tidak boleh sama dengan kata sandi saat ini.',
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui! Silakan gunakan kata sandi baru saat login.'
            ]);
        }

        return back()->with('status', 'Kata sandi berhasil diperbarui! Silakan gunakan kata sandi baru saat login.');
    })->name('profile.password.update');

    Route::delete('/profile', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();

        return redirect('/')->with('status', 'Account deleted successfully!');
    })->name('profile.delete');
});

// Super Admin routes - require super_admin role only
Route::middleware(['auth', 'verified', 'super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
    Route::get('/users', \App\Livewire\SuperAdmin\Users::class)->name('users');
    Route::get('/ratings', \App\Livewire\SuperAdmin\Ratings::class)->name('ratings.index');
    Route::get('/cities', \App\Livewire\SuperAdmin\Cities::class)->name('cities');
    Route::get('/categories', \App\Livewire\SuperAdmin\Categories::class)->name('categories');
    // Notifications
    Route::get('/notifications', \App\Livewire\SuperAdmin\Notifications::class)->name('notifications.index');
    // Activity Logs
    Route::get('/activity-logs', \App\Livewire\SuperAdmin\ActivityLogs::class)->name('activity.logs');
    // Transaction Logs (detailed)
    Route::get('/transactions/logs', \App\Livewire\SuperAdmin\TransactionsLog::class)->name('transactions.log');
    Route::get('/transactions/export-excel', function (\Illuminate\Http\Request $request) {
        $type = $request->get('type', 'all');
        $search = $request->get('search');
        $from = $request->get('from');
        $to = $request->get('to');

        $query = \App\Models\BalanceTransaction::query();

        if ($type && $type !== 'all') {
            if ($type === 'withdraw') {
                $query->where(function ($q) {
                    $q->where('type', 'withdraw')
                      ->orWhere(function ($sub) {
                          $sub->whereIn('type', ['deduction', 'withdraw_deduction'])
                              ->whereHas('user', fn($u) => $u->where('role', 'mitra'));
                      });
                });
            } elseif ($type === 'topup') {
                $query->where('type', 'topup');
            } elseif ($type === 'other') {
                $query->whereNotIn('type', ['topup', 'withdraw']);
            } else {
                $query->where('type', $type);
            }
        }

        if ($search) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('request_code', 'like', "%{$s}%")
                    ->orWhere('order_id', 'like', "%{$s}%")
                    ->orWhere('reference_id', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($qu) use ($s) {
                        $qu->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $transactions = $query->with('user')->orderBy('created_at', 'desc')->get();
        $filename = 'Laporan-Transaksi-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($transactions) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<style>
                body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #111827; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th { background-color: #1e3a8a; color: #ffffff; font-weight: bold; border: 1px solid #0f172a; padding: 8px 10px; text-align: center; vertical-align: middle; }
                td { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
                .text-center { text-align: center; }
                .text-left { text-align: left; }
                .text-right { text-align: right; }
                .font-bold { font-weight: bold; }
                .bg-total { background-color: #f1f5f9; font-weight: bold; }
                .title-main { font-size: 16pt; font-weight: bold; text-align: center; color: #1e3a8a; }
                .title-sub { font-size: 10pt; color: #64748b; text-align: center; }
            </style></head><body>';
            
            echo '<table>';
            echo '<tr><td colspan="8" class="title-main" style="border:none; padding-top:10px; padding-bottom:4px;">LAPORAN DAFTAR TRANSAKSI</td></tr>';
            echo '<tr><td colspan="8" class="title-sub" style="border:none; padding-bottom:15px;">Tanggal Cetak: ' . now()->translatedFormat('d F Y, H:i') . ' WIB | Oleh: ' . htmlspecialchars(auth()->user()->name ?? 'Super Admin') . '</td></tr>';
            echo '</table>';

            echo '<table>';
            echo '<thead><tr>';
            echo '<th style="width: 45px;">No</th>';
            echo '<th style="width: 140px;">Waktu</th>';
            echo '<th style="width: 180px;">Nama User</th>';
            echo '<th style="width: 200px;">Email</th>';
            echo '<th style="width: 130px;">Tipe</th>';
            echo '<th style="width: 130px;">Jumlah (Rp)</th>';
            echo '<th style="width: 120px;">Ref</th>';
            echo '<th style="width: 100px;">Status</th>';
            echo '</tr></thead><tbody>';

            $no = 1;
            $total = 0;

            foreach ($transactions as $t) {
                $userRole = optional($t->user)->role;
                if ($t->type === 'withdraw' || ($userRole === 'mitra' && in_array($t->type, ['deduction', 'withdraw_deduction']))) {
                    $label = 'Withdraw';
                } elseif ($userRole === 'mitra' && ($t->type === 'topup' || $t->type === 'income')) {
                    $label = 'Pendapatan Mitra';
                } elseif ($t->type === 'topup' || $t->type === 'deposit') {
                    $label = 'Top Up';
                } elseif ($t->type === 'deduction') {
                    $label = 'Pembayaran Bantuan';
                } else {
                    $label = ucfirst($t->type);
                }

                $statusLabel = $t->status ?? 'completed';
                $total += (float) $t->amount;
                $rowBg = ($no % 2 === 0) ? 'style="background-color: #f8fafc;"' : '';
                $refCode = $t->request_code ?? $t->order_id ?? $t->reference_id ?? $t->reference ?? '-';

                echo "<tr {$rowBg}>";
                echo '<td class="text-center">' . $no++ . '</td>';
                echo '<td class="text-center">' . ($t->created_at ? $t->created_at->format('d/m/Y H:i') : '-') . '</td>';
                echo '<td class="text-left">' . htmlspecialchars(optional($t->user)->name ?? '-') . '</td>';
                echo '<td class="text-left">' . htmlspecialchars(optional($t->user)->email ?? '-') . '</td>';
                echo '<td class="text-center">' . $label . '</td>';
                echo '<td class="text-right font-bold">' . number_format($t->amount, 0, ',', '.') . '</td>';
                echo '<td class="text-center">' . htmlspecialchars($refCode) . '</td>';
                echo '<td class="text-center">' . $statusLabel . '</td>';
                echo '</tr>';
            }

            echo '<tr>';
            echo '<td colspan="5" class="text-right font-bold bg-total">TOTAL KESELURUHAN</td>';
            echo '<td class="text-right font-bold bg-total">Rp ' . number_format($total, 0, ',', '.') . '</td>';
            echo '<td colspan="2" class="bg-total"></td>';
            echo '</tr>';

            echo '</tbody></table></body></html>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    })->name('transactions.export.excel');
    // Moderasi Bantuan page removed for SuperAdmin
    Route::get('/helps/approved', \App\Livewire\SuperAdmin\HelpsApproved::class)->name('helps.approved');
    // Verifikasi KTP page removed for SuperAdmin
    // Help settings (minimum nominal and admin fee)
    Route::get('/settings/help', \App\Livewire\SuperAdmin\Settings\HelpSettings::class)->name('settings.help');
    // Banners management for dashboards
    Route::get('/settings/banners', \App\Livewire\SuperAdmin\Banners::class)->name('settings.banners');
    // Transactions / Logs (topup, withdraw, mutasi)
    Route::view('/settings/transactions', 'superadmin.transactions')->name('settings.transactions');
    // Top-up approval management
    Route::get('/topup/approvals', \App\Livewire\SuperAdmin\TopupApproval::class)->name('topup.approvals');
    // Superadmin management page for admin-role users
    Route::get('/admin-users', \App\Livewire\SuperAdmin\AdminUsers::class)->name('admin.users');
    // Withdraw management (moved to SuperAdmin for approvals)
    Route::get('/withdraws', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'index'])->name('withdraws.index');
    Route::get('/withdraws/{withdraw}/modal', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'modal'])->name('withdraws.modal');
    Route::get('/withdraws/{withdraw}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'show'])->name('withdraws.show');
    Route::post('/withdraws/{withdraw}/approve', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'approve'])->name('withdraws.approve');
    Route::post('/withdraws/{withdraw}/reject', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'reject'])->name('withdraws.reject');
    
    
});

// Admin routes - require admin role only (for moderasi)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/helps', \App\Livewire\Admin\Helps\Index::class)->name('helps');
    Route::get('/helps/approved', \App\Livewire\Admin\Helps\Approved::class)->name('helps.approved');
    Route::get('/helps/{id}', \App\Livewire\Admin\Helps\Show::class)->name('helps.show');
    // Restore original verifications Livewire component
    Route::get('/verifications', \App\Livewire\Admin\Verifications\Index::class)->name('verifications');

    // Withdraw management (Admin)
    Route::get('/withdraws', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'index'])->name('withdraws.index');
    // Modal endpoint to load withdraw details into a modal (AJAX)
    Route::get('/withdraws/{withdraw}/modal', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'modal'])->name('withdraws.modal');
    Route::get('/withdraws/{withdraw}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'show'])->name('withdraws.show');
    

    // Users management (Admin area)
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('users.show');

    // Rating & Ulasan (Admin area)
    Route::get('/ratings', [\App\Http\Controllers\Admin\AdminRatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/user/{user}', [\App\Http\Controllers\Admin\AdminRatingController::class, 'userRatings'])->name('ratings.user');
    Route::post('/ratings/user/{user}/toggle-shadow-ban', [\App\Http\Controllers\Admin\AdminRatingController::class, 'toggleShadowBan'])->name('ratings.toggle-shadow-ban');
    Route::get('/ratings/{rating}', [\App\Http\Controllers\Admin\AdminRatingController::class, 'show'])->name('ratings.show');

    // Partners activity and exports
    Route::get('/partners/activity', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'index'])->name('partners.activity');
    Route::get('/partners/activity/export/csv', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'exportCsv'])->name('partners.activity.export.csv');
    Route::get('/partners/activity/export/excel', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'exportExcel'])->name('partners.activity.export.excel');
    Route::get('/partners/activity/export/print', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'exportPrint'])->name('partners.activity.export.print');
    Route::post('/partners/activity/{user}/reset-sessions', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'resetSessions'])->name('partners.activity.reset_sessions');
    Route::post('/partners/activity/{user}/reset-password', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'resetPassword'])->name('partners.activity.reset_password');

    // Partner reports and summaries
    Route::get('/partners/report', [\App\Http\Controllers\Admin\PartnerReportController::class, 'index'])->name('partners.report');
    Route::get('/partners/reports', [\App\Http\Controllers\Admin\PartnerReportController::class, 'reportsIndex'])->name('partners.reports');
    Route::get('/partners/reports/{report}', [\App\Http\Controllers\Admin\PartnerReportController::class, 'show'])->name('partners.reports.show');
    Route::post('/partners/reports/{report}/status', [\App\Http\Controllers\Admin\PartnerReportController::class, 'updateStatus'])->name('partners.reports.update');
    Route::post('/partners/reports/{report}/add-note', [\App\Http\Controllers\Admin\PartnerReportController::class, 'addNote'])->name('partners.reports.add-note');
    Route::post('/partners/reports/{report}/resolve', [\App\Http\Controllers\Admin\PartnerReportController::class, 'resolve'])->name('partners.reports.resolve');
    Route::post('/partners/reports/{report}/reopen', [\App\Http\Controllers\Admin\PartnerReportController::class, 'reopen'])->name('partners.reports.reopen');

    // Blocked partners
    Route::get('/partners/blocked', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'index'])->name('partners.blocked');
    Route::post('/partners/blocked/{id}/toggle', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'toggle'])->name('partners.blocked.toggle');
    // Backwards-compatible toggle route used by views: route('admin.partners.toggle', $id)
    Route::post('/partners/toggle/{id}', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'toggle'])->name('partners.toggle');

    // Top-Up Approval Management
    Route::get('/topup/approvals', \App\Livewire\Admin\TopupApproval::class)->name('topup.approvals');
});

// ========================================
// MIDTRANS PAYMENT ROUTES (Public - No Auth)
// ========================================
Route::prefix('topup')->name('topup.')->group(function () {
    Route::get('/finish', [\App\Http\Controllers\TopupController::class, 'finish'])->name('finish');
    Route::get('/unfinish', [\App\Http\Controllers\TopupController::class, 'unfinish'])->name('unfinish');
    Route::get('/error', [\App\Http\Controllers\TopupController::class, 'error'])->name('error');
    Route::get('/success', [\App\Http\Controllers\TopupController::class, 'success'])->name('success');
    Route::post('/notification', [\App\Http\Controllers\TopupController::class, 'notification'])->name('notification');
    // Client-side callback (AJAX) used to notify server immediately when Snap reports success
    Route::post('/client-callback', [\App\Http\Controllers\TopupController::class, 'clientCallback'])->name('client-callback');
});

// Public callback endpoint used by payment gateway integrations for withdraw disbursements
Route::post('/gateway/callback', [\App\Http\Controllers\WithdrawController::class, 'gatewayCallback'])->name('gateway.callback');

// Sementara: fix user_balances dari completed transactions
if (config('app.debug')) {
    Route::get('/dev-sync-balance', function () {
        $userId = auth()->id();
        if (!$userId) return "Silakan login dulu.";

        $sum = \Illuminate\Support\Facades\DB::table('balance_transactions')
            ->where('user_id', $userId)
            ->where('type', 'topup')
            ->where('status', 'completed')
            ->sum('amount');

        \App\Models\UserBalance::updateOrCreate(
            ['user_id' => $userId],
            ['balance' => (float) $sum]
        );

        return "✅ Saldo disinkronkan dari " . \Illuminate\Support\Facades\DB::table('balance_transactions')
            ->where('user_id', $userId)->where('type', 'topup')->where('status', 'completed')->count()
            . " transaksi completed.<br>"
            . "Saldo sekarang: <b>Rp " . number_format($sum, 0, ',', '.') . "</b><br><br>"
            . "<a href='/customer/dashboard'>Kembali ke Dashboard</a>";
    });
}

require __DIR__ . '/auth.php';

// Fallback untuk mencegah error MethodNotAllowedHttpException saat browser mengirim GET /livewire/update (misal saat back/refresh)
Route::get('/livewire/update', function () {
    return redirect()->back();
});