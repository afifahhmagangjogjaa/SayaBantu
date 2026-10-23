<?php

namespace App\Http\Controllers;

use App\Models\PartnerReport;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PartnerReportController extends Controller
{
    /**
     * Check real-time resolution status of a report.
     */
    public function statusCheck(PartnerReport $report): JsonResponse
    {
        $userId = auth()->id();

        if ($report->reporter_id !== $userId && $report->reported_user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $report->id,
            'status' => $report->status,
            'resolved_at' => $report->resolved_at?->toIso8601String(),
            'updated_at' => $report->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Show detail page of a partner report (for customer or mitra).
     */
    public function show(PartnerReport $report): View
    {
        $userId = auth()->id();

        if ($report->reporter_id !== $userId && $report->reported_user_id !== $userId) {
            abort(403);
        }

        $viewName = auth()->user()->isMitra()
            ? 'livewire.mitra.reports.show'
            : 'livewire.customer.reports.show';

        return view($viewName, ['report' => $report]);
    }
}
