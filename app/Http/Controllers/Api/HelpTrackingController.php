<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Help;
use Illuminate\Http\JsonResponse;

class HelpTrackingController extends Controller
{
    /**
     * Hanya pihak yang berkepentingan atas order ini yang boleh melihat datanya:
     * customer pemilik order, mitra yang mengerjakan, atau admin/super admin.
     */
    private function authorizeViewer(Help $help): void
    {
        $user = auth()->user();

        abort_if(! $user, 403);

        $allowed = (int) $help->user_id === (int) $user->id
            || (int) $help->mitra_id === (int) $user->id
            || in_array($user->role, ['admin', 'super_admin'], true);

        abort_unless($allowed, 403, 'Anda tidak berhak melihat data bantuan ini.');
    }

    /**
     * Get live GPS tracking coordinates for a help request.
     */
    public function tracking(int $id): JsonResponse
    {
        $help = Help::with('mitra')->find($id);

        if (!$help) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $this->authorizeViewer($help);

        return response()->json([
            'partnerLat' => $help->partner_current_lat,
            'partnerLng' => $help->partner_current_lng,
            'customerLat' => $help->latitude,
            'customerLng' => $help->longitude,
            'partnerName' => $help->mitra?->name,
            'updated_at' => $help->updated_at?->toDateTimeString(),
        ]);
    }

    /**
     * Get basic help request metadata in JSON format.
     */
    public function json(int $id): JsonResponse
    {
        $help = Help::with(['city', 'mitra', 'user'])->find($id);

        if (!$help) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $this->authorizeViewer($help);

        return response()->json([
            'id' => $help->id,
            'scheduled_at' => $help->scheduled_at?->toDateTimeString(),
            'title' => $help->title,
            'amount' => $help->amount,
        ]);
    }
}
