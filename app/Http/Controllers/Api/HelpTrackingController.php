<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Help;
use Illuminate\Http\JsonResponse;

class HelpTrackingController extends Controller
{
    /**
     * Get live GPS tracking coordinates for a help request.
     */
    public function tracking(int $id): JsonResponse
    {
        $help = Help::with('mitra')->find($id);

        if (!$help) {
            return response()->json(['error' => 'Not found'], 404);
        }

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

        return response()->json([
            'id' => $help->id,
            'scheduled_at' => $help->scheduled_at?->toDateTimeString(),
            'title' => $help->title,
            'amount' => $help->amount,
        ]);
    }
}
