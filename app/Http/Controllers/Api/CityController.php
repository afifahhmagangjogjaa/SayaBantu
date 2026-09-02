<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Search cities by query string `q`.
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $limit = (int) $request->get('limit', 10);

        if (trim($q) === '') {
            return response()->json([]);
        }

        $results = City::where('is_active', true)
            ->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('province', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'province', 'code')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        // If we don't have enough results, search the imported `regencies`/`provinces`
        // tables and create corresponding City records on-the-fly so the frontend
        // can select them and Help::create will reference a proper `cities` row.
        if ($results->count() < $limit) {
            $remaining = $limit - $results->count();

            $regRows = DB::table('regencies')
                ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
                ->where(function ($builder) use ($q) {
                    $builder->where('regencies.regency', 'like', "%{$q}%")
                            ->orWhere('provinces.province', 'like', "%{$q}%");
                })
                ->select('regencies.id as regency_id', 'regencies.regency', 'regencies.type', 'provinces.province')
                ->orderBy('regencies.regency')
                ->limit($remaining)
                ->get();

            foreach ($regRows as $r) {
                // Ensure a City record exists for this regency code
                $city = City::firstOrCreate(
                    ['code' => $r->regency_id],
                    [
                        'name' => $r->regency,
                        'province' => $r->province,
                        'type' => $r->type ?? null,
                        'is_active' => true,
                    ]
                );

                // Only add if not already present in results
                if (! $results->contains('id', $city->id)) {
                    $results->push($city->only(['id', 'name', 'province', 'code']));
                }
            }
        }

        return response()->json($results);
    }
}
