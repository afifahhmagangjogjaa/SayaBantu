<?php

namespace App\Livewire\Admin;

use App\Models\Help;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Dashboard extends Component
{
    public function render()
    {
        // If current user is an admin, scope metrics to their assigned city
        if (auth()->user() && auth()->user()->role === 'admin') {
            $cityIds = auth()->user()->getAdminCityIds();
            $cityHelpFilter = function($q) use ($cityIds) {
                $q->where(function($sub) use ($cityIds) {
                    $sub->whereIn('city_id', $cityIds)
                        ->orWhereHas('customer', function($c) use ($cityIds) {
                            $c->whereIn('city_id', $cityIds);
                        });
                });
            };

            $totalHelps = !empty($cityIds) ? Help::where($cityHelpFilter)->count() : Help::count();
            $pendingHelps = !empty($cityIds) ? Help::where($cityHelpFilter)->whereIn('status', ['menunggu_mitra', 'pending', 'created'])->count() : Help::whereIn('status', ['menunggu_mitra', 'pending', 'created'])->count();
            $activeHelps = !empty($cityIds) ? Help::where($cityHelpFilter)->whereIn('status', ['partner_on_the_way', 'waiting_customer_confirmation', 'partner_cancel_requested', 'memperoleh_mitra', 'in_progress', 'sedang_diproses', 'partner_arrived', 'taken', 'active'])->count() : Help::whereIn('status', ['partner_on_the_way', 'waiting_customer_confirmation', 'partner_cancel_requested', 'memperoleh_mitra', 'in_progress', 'sedang_diproses', 'partner_arrived', 'taken', 'active'])->count();
            $completedHelps = !empty($cityIds) ? Help::where($cityHelpFilter)->whereIn('status', ['selesai', 'completed'])->count() : Help::whereIn('status', ['selesai', 'completed'])->count();
            $pendingVerifications = !empty($cityIds) ? \App\Models\Registration::whereIn('city_id', $cityIds)->where('status', 'pending_verification')->count() : \App\Models\Registration::where('status', 'pending_verification')->count();
            $verifiedMitras = !empty($cityIds) ? User::where('role', 'mitra')
                                ->whereIn('city_id', $cityIds)
                                ->where('verified', true)
                                ->count() : User::where('role', 'mitra')->where('verified', true)->count();
            
            // Pending topup approvals (filtered by city)
            $pendingTopups = \App\Models\BalanceTransaction::where('type', 'topup')
                ->where('status', 'waiting_approval')
                ->when(!empty($cityIds), function ($q) use ($cityIds) {
                    $q->whereHas('user', function ($sq) use ($cityIds) {
                        $sq->whereIn('city_id', $cityIds);
                    });
                })
                ->count();
        } else {
            $totalHelps = Help::count();
            $pendingHelps = Help::whereIn('status', ['menunggu_mitra', 'pending', 'created'])->count();
            $activeHelps = Help::whereIn('status', ['partner_on_the_way', 'waiting_customer_confirmation', 'partner_cancel_requested', 'memperoleh_mitra', 'in_progress', 'sedang_diproses', 'partner_arrived', 'taken', 'active'])->count();
            $completedHelps = Help::whereIn('status', ['selesai', 'completed'])->count();
            $pendingVerifications = 0;
            $verifiedMitras = User::where('role', 'mitra')->where('verified', true)->count();
            
            // Pending topup approvals (all cities for super admin)
            $pendingTopups = \App\Models\BalanceTransaction::where('type', 'topup')
                ->where('status', 'waiting_approval')
                ->count();
        }

        // Health check
        $health = [
            'database' => [
                'status' => 'unknown',
            ],
            'queue' => [
                'status' => 'inactive',
                'pending' => null,
            ],
            'disk' => [
                'status' => 'unknown',
                'usage' => null,
            ],
        ];

        try {
            DB::select('select 1');
            $health['database']['status'] = 'ok';
        } catch (\Throwable $e) {
            $health['database']['status'] = 'down';
        }

        if (Schema::hasTable('jobs')) {
            try {
                $pending = DB::table('jobs')->count();
                $health['queue']['pending'] = $pending;
                $health['queue']['status'] = $pending > 0 ? 'warning' : 'ok';
            } catch (\Throwable $e) {
                $health['queue']['pending'] = null;
                $health['queue']['status'] = 'inactive';
            }
        } else {
            $health['queue']['pending'] = null;
            $health['queue']['status'] = 'inactive';
        }

        try {
            $path = base_path();
            $total = @disk_total_space($path);
            $free = @disk_free_space($path);
            if ($total > 0 && $free !== false) {
                $used = $total - $free;
                $usagePct = (int) round(($used / $total) * 100);
                $health['disk']['usage'] = $usagePct;
                if ($usagePct >= 90) {
                    $health['disk']['status'] = 'critical';
                } elseif ($usagePct >= 75) {
                    $health['disk']['status'] = 'warning';
                } else {
                    $health['disk']['status'] = 'ok';
                }
            } else {
                $health['disk']['usage'] = null;
                $health['disk']['status'] = 'unknown';
            }
        } catch (\Throwable $e) {
            $health['disk']['usage'] = null;
            $health['disk']['status'] = 'unknown';
        }

        return view('livewire.admin.dashboard', [
            'totalHelps' => $totalHelps,
            'pendingHelps' => $pendingHelps,
            'activeHelps' => $activeHelps,
            'completedHelps' => $completedHelps,
            'pendingVerifications' => $pendingVerifications,
            'verifiedMitras' => $verifiedMitras,
            'pendingTopups' => $pendingTopups,
            'health' => $health,
        ]);
    }
}
