<?php

use Illuminate\Support\Facades\Route;

// Rute mitra (prefix, nama, dan middleware milik role ini).
// Catatan: jangan panggil ->middleware() lagi setelah ini — pemanggilan kedua
// akan menimpa daftar middleware, sehingga 'auth' dan 'onboarded' hilang.
Route::middleware(['auth', 'onboarded', 'mitra'])
    ->prefix('mitra')
    ->name('mitra.')
    ->group(function () {
        Route::get('/dashboard', \App\Livewire\Mitra\Dashboard\Index::class)->name('dashboard');
        Route::get('/helps', \App\Livewire\Mitra\Helps\AllHelps::class)->name('helps.all');
        Route::get('/helps/completed', \App\Livewire\Mitra\Helps\CompletedHelps::class)->name('helps.completed');
        Route::get('/helps/{id}/detail', \App\Livewire\Mitra\Helps\HelpDetail::class)->name('helps.detail');
        Route::get('/profile', \App\Livewire\Mitra\Profile\Index::class)->name('profile');
        Route::get('/profile/edit', \App\Livewire\Mitra\Profile\EditPage::class)->name('profile.edit');
        Route::get('/chat/{help?}', \App\Livewire\Mitra\Chat\Index::class)->name('chat');
        Route::get('/notifications', \App\Livewire\Mitra\Notifications\Index::class)->name('notifications.index');

        Route::get('/reports/create', \App\Livewire\Mitra\Reports\Create::class)->name('reports.create');
        Route::get('/reports/create/user/{user_id}', \App\Livewire\Mitra\Reports\Create::class)->name('reports.create.user');
        Route::get('/reports/create/help/{help_id}', \App\Livewire\Mitra\Reports\Create::class)->name('reports.create.help');
        Route::get('/reports/{report}/status-check', [\App\Http\Controllers\PartnerReportController::class, 'statusCheck'])->name('reports.status-check');
        Route::get('/reports/{report}', [\App\Http\Controllers\PartnerReportController::class, 'show'])->name('reports.show');

        Route::get('/helps/processing', \App\Livewire\Mitra\Helps\ProcessingHelps::class)->name('helps.processing');
        Route::get('/ratings', \App\Livewire\Mitra\Ratings\Index::class)->name('ratings');

        Route::view('/settings', 'mitra.settings')->name('settings');
        Route::view('/settings/notifications', 'mitra.settings.notifications')->name('settings.notifications');
        Route::view('/settings/password', 'mitra.settings.password')->name('settings.password');
        Route::view('/help-support', 'mitra.help-support')->name('help-support');

        Route::get('/withdraw', [\App\Http\Controllers\WithdrawController::class, 'showForm'])->name('withdraw.form');
        Route::post('/withdraw', [\App\Http\Controllers\WithdrawController::class, 'requestWithdraw'])->name('withdraw.request');
        Route::get('/withdraw/history', [\App\Http\Controllers\WithdrawController::class, 'withdrawHistory'])->name('withdraw.history');
        Route::get('/withdraw/success/{withdraw}', [\App\Http\Controllers\WithdrawController::class, 'showSuccess'])->name('withdraw.success');
        Route::get('/withdraw/rejected/{withdraw}', [\App\Http\Controllers\WithdrawController::class, 'showRejected'])->name('withdraw.rejected');
    });
