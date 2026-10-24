<?php

use Illuminate\Support\Facades\Route;

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/helps', \App\Livewire\Admin\Helps\Index::class)->name('helps');
    Route::get('/helps/approved', \App\Livewire\Admin\Helps\Approved::class)->name('helps.approved');
    Route::get('/helps/{id}', \App\Livewire\Admin\Helps\Show::class)->name('helps.show');
    Route::get('/verifications', \App\Livewire\Admin\Verifications\Index::class)->name('verifications');

    Route::get('/withdraws', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'index'])->name('withdraws.index');
    Route::get('/withdraws/{withdraw}/modal', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'modal'])->name('withdraws.modal');
    Route::post('/withdraws/{withdraw}/approve', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'approve'])->name('withdraws.approve');
    Route::post('/withdraws/{withdraw}/reject', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'reject'])->name('withdraws.reject');
    Route::get('/withdraws/{withdraw}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'show'])->name('withdraws.show');
    Route::delete('/withdraws/clear-all', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'destroyAll'])->name('withdraws.destroy_all');
    Route::delete('/withdraws/delete-period', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'destroyPeriod'])->name('withdraws.destroy_period');
    Route::delete('/withdraws/{withdraw}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'destroy'])->name('withdraws.destroy');

    Route::get('/customers', [\App\Http\Controllers\Admin\AdminUserController::class, 'customers'])->name('customers');
    Route::get('/customers/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('customers.show');
    Route::get('/mitra', [\App\Http\Controllers\Admin\AdminUserController::class, 'mitra'])->name('mitra');
    Route::get('/mitra/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('mitra.show');
    Route::get('/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/verify-ktp', [\App\Http\Controllers\Admin\AdminUserController::class, 'verifyKtp'])->name('users.verify-ktp');
    Route::post('/users/{user}/reject-ktp', [\App\Http\Controllers\Admin\AdminUserController::class, 'rejectKtp'])->name('users.reject-ktp');
    Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('/ratings', [\App\Http\Controllers\Admin\AdminRatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/user/{user}', [\App\Http\Controllers\Admin\AdminRatingController::class, 'userRatings'])->name('ratings.user');
    Route::post('/ratings/user/{user}/toggle-shadow-ban', [\App\Http\Controllers\Admin\AdminRatingController::class, 'toggleShadowBan'])->name('ratings.toggle-shadow-ban');
    Route::get('/ratings/{rating}', [\App\Http\Controllers\Admin\AdminRatingController::class, 'show'])->name('ratings.show');

    Route::get('/partners/activity', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'index'])->name('partners.activity');
    Route::get('/partners/activity/export/csv', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'exportCsv'])->name('partners.activity.export.csv');
    Route::get('/partners/activity/export/excel', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'exportExcel'])->name('partners.activity.export.excel');
    Route::get('/partners/activity/export/print', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'exportPrint'])->name('partners.activity.export.print');
    Route::post('/partners/activity/{user}/reset-sessions', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'resetSessions'])->name('partners.activity.reset_sessions');
    Route::post('/partners/activity/{user}/reset-password', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'resetPassword'])->name('partners.activity.reset_password');
    Route::delete('/partners/activity/clear-all', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'destroyAll'])->name('partners.activity.destroy_all');
    Route::delete('/partners/activity/{activity}', [\App\Http\Controllers\Admin\PartnerActivityController::class, 'destroy'])->name('partners.activity.destroy');

    Route::get('/partners/report', [\App\Http\Controllers\Admin\PartnerReportController::class, 'index'])->name('partners.report');
    Route::get('/partners/reports', [\App\Http\Controllers\Admin\PartnerReportController::class, 'reportsIndex'])->name('partners.reports');
    Route::get('/partners/reports/{report}', [\App\Http\Controllers\Admin\PartnerReportController::class, 'show'])->name('partners.reports.show');
    Route::post('/partners/reports/{report}/status', [\App\Http\Controllers\Admin\PartnerReportController::class, 'updateStatus'])->name('partners.reports.update');
    Route::post('/partners/reports/{report}/add-note', [\App\Http\Controllers\Admin\PartnerReportController::class, 'addNote'])->name('partners.reports.add-note');
    Route::post('/partners/reports/{report}/resolve', [\App\Http\Controllers\Admin\PartnerReportController::class, 'resolve'])->name('partners.reports.resolve');
    Route::post('/partners/reports/{report}/reopen', [\App\Http\Controllers\Admin\PartnerReportController::class, 'reopen'])->name('partners.reports.reopen');
    Route::post('/partners/reports/{report}/sanction', [\App\Http\Controllers\Admin\PartnerReportController::class, 'storeSanction'])->name('partners.reports.sanction');

    Route::get('/partners/blocked', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'index'])->name('partners.blocked');
    Route::post('/partners/blocked/{id}/toggle', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'toggle'])->name('partners.blocked.toggle');
    Route::post('/partners/toggle/{id}', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'toggle'])->name('partners.toggle');
    Route::post('/partners/{id}/toggle-shadow-ban', [\App\Http\Controllers\Admin\BlockedPartnerController::class, 'toggleShadowBan'])->name('partners.toggle-shadow-ban');

    Route::get('/topup/approvals', \App\Livewire\Admin\TopupApproval::class)->name('topup.approvals');
    Route::get('/notifications', \App\Livewire\Admin\NotificationsIndex::class)->name('notifications.index');
    Route::get('/password', \App\Livewire\Admin\ChangePassword::class)->name('password');
});
