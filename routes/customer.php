<?php

use Illuminate\Support\Facades\Route;

// Rute customer (prefix, nama, dan middleware milik role ini).
// Catatan: jangan panggil ->middleware() lagi setelah ini — pemanggilan kedua
// akan menimpa daftar middleware, sehingga 'auth' dan 'onboarded' hilang.
Route::middleware(['auth', 'onboarded', 'customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', \App\Livewire\Customer\Dashboard::class)->name('dashboard');

        // Helps Management
        Route::get('/helps/create', \App\Livewire\Customer\Helps\Create::class)->name('helps.create');
        Route::get('/helps/history', \App\Livewire\Customer\Helps\History::class)->name('helps.history');
        Route::get('/helps/{id}/detail', \App\Livewire\Customer\Helps\Detail::class)->name('helps.detail');
        Route::get('/helps', \App\Livewire\Customer\Helps\Index::class)->name('helps.index');

        // Notifications
        Route::get('/notifications', \App\Livewire\Customer\Notifications\Index::class)->name('notifications.index');

        // Transactions
        Route::get('/transactions', \App\Livewire\Customer\Transactions\Index::class)->name('transactions.index');

        // Top Up Saldo — satu URL kanonik: /customer/topup.
        // URL lain dipertahankan sebagai redirect supaya tautan lama tidak 404.
        Route::get('/topup', \App\Livewire\Customer\TopupRequest::class)->name('topup');
        Route::get('/topup/history', \App\Livewire\Customer\TopupHistory::class)->name('topup.history');
        Route::redirect('/topup/request', '/customer/topup');
        Route::redirect('/top-up', '/customer/topup');
        Route::redirect('/topup/instant', '/customer/topup');
        Route::redirect('/topup/midtrans', '/customer/topup');

        // Chat
        Route::get('/chat/{help?}', \App\Livewire\Customer\Chat::class)->name('chat');

        // Help & Support
        Route::view('/help-support', 'customer.help-support')->name('help-support');

        // Tracking
        Route::get('/helps/{id}/tracking', [\App\Http\Controllers\Api\HelpTrackingController::class, 'tracking'])->name('helps.tracking');

        // Help details JSON
        Route::get('/helps/{id}/json', [\App\Http\Controllers\Api\HelpTrackingController::class, 'json'])->name('helps.json');

        // Reports
        Route::get('/reports/create', \App\Livewire\Customer\Reports\Create::class)->name('reports.create');
        Route::get('/reports/create/user/{user_id}', \App\Livewire\Customer\Reports\Create::class)->name('reports.create.user');
        Route::get('/reports/create/help/{help_id}', \App\Livewire\Customer\Reports\Create::class)->name('reports.create.help');
        Route::get('/reports/{report}/status-check', [\App\Http\Controllers\PartnerReportController::class, 'statusCheck'])->name('reports.status-check');
        Route::get('/reports/{report}', [\App\Http\Controllers\PartnerReportController::class, 'show'])->name('reports.show');
    });
