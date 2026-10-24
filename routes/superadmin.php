<?php

use Illuminate\Support\Facades\Route;

// Super Admin routes
Route::middleware(['auth', 'verified', 'super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('dashboard');
    Route::get('/customers', \App\Livewire\SuperAdmin\Users::class)->name('customers');
    Route::get('/mitra', \App\Livewire\SuperAdmin\Users::class)->name('mitra');
    Route::get('/users', \App\Livewire\SuperAdmin\Users::class)->name('users');
    Route::get('/ratings', \App\Livewire\SuperAdmin\Ratings::class)->name('ratings.index');
    Route::get('/cities', \App\Livewire\SuperAdmin\Cities::class)->name('cities');
    Route::get('/categories', \App\Livewire\SuperAdmin\Categories::class)->name('categories');
    Route::get('/notifications', \App\Livewire\SuperAdmin\Notifications::class)->name('notifications.index');
    Route::get('/activity-logs', \App\Livewire\SuperAdmin\ActivityLogs::class)->name('activity.logs');
    Route::get('/transactions/logs', \App\Livewire\SuperAdmin\TransactionsLog::class)->name('transactions.log');
    Route::get('/transactions/export-excel', [\App\Http\Controllers\SuperAdmin\TransactionExportController::class, 'export'])->name('transactions.export.excel');

    Route::get('/helps/approved', \App\Livewire\SuperAdmin\HelpsApproved::class)->name('helps.approved');
    Route::get('/settings/help', \App\Livewire\SuperAdmin\Settings\HelpSettings::class)->name('settings.help');
    Route::get('/settings/help/fee-pendapatan', \App\Livewire\SuperAdmin\Settings\FeePendapatan::class)->name('pengaturan.bantuan.fee-pendapatan');
    Route::get('/settings/help/biaya-topup', \App\Livewire\SuperAdmin\Settings\BiayaTopupBank::class)->name('pengaturan.bantuan.biaya-topup');
    Route::get('/settings/help/tarif-radius', \App\Livewire\SuperAdmin\Settings\TarifRadius::class)->name('pengaturan.bantuan.tarif-radius');
    Route::get('/settings/banners', \App\Livewire\SuperAdmin\Banners::class)->name('settings.banners');
    Route::view('/settings/transactions', 'superadmin.transactions')->name('settings.transactions');
    Route::get('/topup/approvals', \App\Livewire\SuperAdmin\TopupApproval::class)->name('topup.approvals');
    Route::get('/admin-users', \App\Livewire\SuperAdmin\AdminUsers::class)->name('admin.users');
    Route::get('/withdraws', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'index'])->name('withdraws.index');
    Route::get('/withdraws/{withdraw}/modal', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'modal'])->name('withdraws.modal');
    Route::get('/withdraws/{withdraw}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'show'])->name('withdraws.show');
    Route::post('/withdraws/{withdraw}/approve', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'approve'])->name('withdraws.approve');
    Route::post('/withdraws/{withdraw}/reject', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'reject'])->name('withdraws.reject');
    Route::delete('/withdraws/clear-all', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'destroyAll'])->name('withdraws.destroy_all');
    Route::delete('/withdraws/delete-period', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'destroyPeriod'])->name('withdraws.destroy_period');
    Route::delete('/withdraws/{withdraw}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'destroy'])->name('withdraws.destroy');
    Route::get('/password', \App\Livewire\SuperAdmin\ChangePassword::class)->name('password');
});
