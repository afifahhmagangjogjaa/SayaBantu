<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AppSetting;
use App\Models\Help;
use App\Models\BalanceTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.superadmin')]
#[Title('Fee & Pendapatan Platform - Super Admin')]
class FeePendapatan extends Component
{
    public $customer_service_fee_percent = 10;
    public $mitra_platform_fee_percent = 10;
    public $admin_fee = 0;

    protected function rules()
    {
        return [
            'customer_service_fee_percent' => 'required|numeric|min:0|max:100',
            'mitra_platform_fee_percent'   => 'required|numeric|min:0|max:100',
            'admin_fee'                    => 'nullable|numeric|min:0',
        ];
    }

    public function mount()
    {
        $this->customer_service_fee_percent = (float) AppSetting::get('customer_service_fee_percent', 10);
        $this->mitra_platform_fee_percent   = (float) AppSetting::get('mitra_platform_fee_percent', 10);
        $this->admin_fee                    = (int) AppSetting::get('admin_fee', 0);
    }

    public function save()
    {
        $this->admin_fee = (int) preg_replace('/\D/', '', (string) ($this->admin_fee ?? 0));
        $this->validate();

        AppSetting::set('customer_service_fee_percent', (string) $this->customer_service_fee_percent);
        AppSetting::set('mitra_platform_fee_percent', (string) $this->mitra_platform_fee_percent);
        AppSetting::set('admin_fee', (string) $this->admin_fee);

        session()->flash('message', 'Pengaturan fee & biaya admin platform berhasil disimpan.');
        $this->dispatch('settingsSaved', message: 'Pengaturan fee & biaya admin platform berhasil disimpan.');
    }

    public function render()
    {
        // Prepare revenue chart data: daily (30 days), monthly (12 months), yearly (5 years)
        $platformFeeChart = [
            'daily' => ['labels' => [], 'data' => []],
            'monthly' => ['labels' => [], 'data' => []],
            'yearly' => ['labels' => [], 'data' => []],
        ];

        $topupAdminChart = [
            'daily' => ['labels' => [], 'data' => []],
            'monthly' => ['labels' => [], 'data' => []],
            'yearly' => ['labels' => [], 'data' => []],
        ];

        $adminFeeChart = [
            'daily' => ['labels' => [], 'data' => []],
            'monthly' => ['labels' => [], 'data' => []],
            'yearly' => ['labels' => [], 'data' => []],
        ];

        $helpFeeExpr = DB::raw('COALESCE(customer_fee_amount, 0) + COALESCE(mitra_fee_amount, 0)');

        // Daily - last 30 days
        $days = 30;
        $startDay = Carbon::today()->subDays($days - 1);
        for ($i = 0; $i < $days; $i++) {
            $d = $startDay->copy()->addDays($i);
            $label = $d->format('d M');
            $helpAdminFee = (float) Help::whereDate('created_at', $d->toDateString())->sum($helpFeeExpr);
            $topupAdminFee = (float) BalanceTransaction::whereDate('created_at', $d->toDateString())
                ->where('status', 'completed')
                ->sum('admin_fee');
            $sum = $helpAdminFee + $topupAdminFee;

            $platformFeeChart['daily']['labels'][] = $label;
            $platformFeeChart['daily']['data'][] = $helpAdminFee;

            $topupAdminChart['daily']['labels'][] = $label;
            $topupAdminChart['daily']['data'][] = $topupAdminFee;

            $adminFeeChart['daily']['labels'][] = $label;
            $adminFeeChart['daily']['data'][] = $sum;
        }

        // Monthly - last 12 months
        $months = 12;
        $startMonth = Carbon::now()->startOfMonth()->subMonths($months - 1);
        for ($i = 0; $i < $months; $i++) {
            $m = $startMonth->copy()->addMonths($i);
            $label = $m->format('M Y');
            $helpAdminFee = (float) Help::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum($helpFeeExpr);
            $topupAdminFee = (float) BalanceTransaction::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->where('status', 'completed')
                ->sum('admin_fee');
            $sum = $helpAdminFee + $topupAdminFee;

            $platformFeeChart['monthly']['labels'][] = $label;
            $platformFeeChart['monthly']['data'][] = $helpAdminFee;

            $topupAdminChart['monthly']['labels'][] = $label;
            $topupAdminChart['monthly']['data'][] = $topupAdminFee;

            $adminFeeChart['monthly']['labels'][] = $label;
            $adminFeeChart['monthly']['data'][] = $sum;
        }

        // Yearly - last 5 years
        $years = 5;
        $startYear = Carbon::now()->startOfYear()->subYears($years - 1);
        for ($i = 0; $i < $years; $i++) {
            $y = $startYear->copy()->addYears($i);
            $label = (string) $y->year;
            $helpAdminFee = (float) Help::whereYear('created_at', $y->year)->sum($helpFeeExpr);
            $topupAdminFee = (float) BalanceTransaction::whereYear('created_at', $y->year)
                ->where('status', 'completed')
                ->sum('admin_fee');
            $sum = $helpAdminFee + $topupAdminFee;

            $platformFeeChart['yearly']['labels'][] = $label;
            $platformFeeChart['yearly']['data'][] = $helpAdminFee;

            $topupAdminChart['yearly']['labels'][] = $label;
            $topupAdminChart['yearly']['data'][] = $topupAdminFee;

            $adminFeeChart['yearly']['labels'][] = $label;
            $adminFeeChart['yearly']['data'][] = $sum;
        }

        // Summary stats - Platform Help Fee (Customer Fee + Mitra Fee)
        $custFeeTotal = (float) Help::sum('customer_fee_amount');
        $mitraFeeTotal = (float) Help::sum('mitra_fee_amount');
        $helpTotalFee = $custFeeTotal + $mitraFeeTotal;

        $helpFeeMonth = (float) Help::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum($helpFeeExpr);

        $helpFee30 = (float) Help::whereDate('created_at', '>=', Carbon::today()->subDays(29))->sum($helpFeeExpr);
        
        $helpsWithFee = (int) Help::whereRaw('(COALESCE(customer_fee_amount, 0) + COALESCE(mitra_fee_amount, 0)) > 0')->count();
        $avgHelpFee = $helpsWithFee ? ($helpTotalFee / $helpsWithFee) : 0;

        $helpBreakdown = [
            'customer' => [
                'total' => $custFeeTotal,
                'percent' => $helpTotalFee > 0 ? round(($custFeeTotal / $helpTotalFee) * 100, 1) : 0,
            ],
            'mitra' => [
                'total' => $mitraFeeTotal,
                'percent' => $helpTotalFee > 0 ? round(($mitraFeeTotal / $helpTotalFee) * 100, 1) : 0,
            ],
            'total' => $helpTotalFee,
            'count' => $helpsWithFee,
            'avg' => $avgHelpFee,
        ];

        return view('superadmin.fee-pendapatan', compact(
            'platformFeeChart',
            'helpTotalFee',
            'helpFeeMonth',
            'helpFee30',
            'helpsWithFee',
            'avgHelpFee',
            'helpBreakdown',
            'custFeeTotal',
            'mitraFeeTotal'
        ));
    }
}