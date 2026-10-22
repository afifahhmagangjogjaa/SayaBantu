<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    /**
     * Search address suggestions (autocomplete) using OpenStreetMap / Nominatim / Photon.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        $cityBias = trim($request->get('city', ''));

        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }

        $cacheKey = 'address_suggest_' . md5(strtolower($q . '|' . $cityBias));

        $results = Cache::remember($cacheKey, 86400, function () use ($q, $cityBias) {
            return $this->fetchFromNominatim($q, $cityBias) ?: $this->fetchFromPhoton($q, $cityBias);
        });

        return response()->json($results ?: []);
    }

    /**
     * Fetch suggestions from Nominatim OpenStreetMap
     */
    protected function fetchFromNominatim(string $q, string $cityBias = ''): array
    {
        try {
            $searchQuery = $cityBias ? "{$q}, {$cityBias}" : $q;
            $encodedQuery = urlencode($searchQuery);

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'SayaBantu-App/1.0 (contact@sayabantu.id)',
                    'Accept-Language' => 'id,en;q=0.9',
                ])
                ->timeout(4)
                ->get("https://nominatim.openstreetmap.org/search?q={$encodedQuery}&format=json&addressdetails=1&countrycodes=id&limit=6");

            if (!$response->successful() || empty($response->json())) {
                return [];
            }

            $results = [];
            foreach ($response->json() as $item) {
                $addr = $item['address'] ?? [];
                $title = $item['name'] ?? ($addr['road'] ?? ($addr['village'] ?? ($addr['suburb'] ?? $item['display_name'])));
                
                $city = $addr['city'] ?? $addr['town'] ?? $addr['municipality'] ?? $addr['county'] ?? $addr['regency'] ?? '';
                $province = $addr['state'] ?? '';
                $district = $addr['suburb'] ?? $addr['district'] ?? $addr['neighbourhood'] ?? $addr['village'] ?? '';

                $subtitleParts = array_filter([$district, $city, $province], fn($val) => !empty($val) && $val !== $title);
                $subtitle = implode(', ', array_unique($subtitleParts));

                $results[] = [
                    'title'        => $title,
                    'subtitle'     => $subtitle ?: $item['display_name'],
                    'full_address' => $item['display_name'],
                    'lat'          => (float) $item['lat'],
                    'lng'          => (float) $item['lon'],
                    'city'         => $city,
                    'province'     => $province,
                ];
            }

            return $results;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Fallback to Photon (Komoot OSM Geocoder)
     */
    protected function fetchFromPhoton(string $q, string $cityBias = ''): array
    {
        try {
            $searchQuery = $cityBias ? "{$q} {$cityBias}" : $q;
            $encodedQuery = urlencode($searchQuery);

            $response = Http::withoutVerifying()
                ->timeout(4)
                ->get("https://photon.komoot.io/api/?q={$encodedQuery}&limit=6&lang=default");

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();
            $features = $data['features'] ?? [];
            if (empty($features)) {
                return [];
            }

            $results = [];
            foreach ($features as $f) {
                $prop = $f['properties'] ?? [];
                $coords = $f['geometry']['coordinates'] ?? [0, 0];

                $title = $prop['name'] ?? ($prop['street'] ?? 'Lokasi');
                $city = $prop['city'] ?? ($prop['county'] ?? '');
                $province = $prop['state'] ?? '';
                $district = $prop['district'] ?? ($prop['locality'] ?? '');

                $subtitleParts = array_filter([$district, $city, $province], fn($val) => !empty($val) && $val !== $title);
                $subtitle = implode(', ', array_unique($subtitleParts));

                // Build a nice full address string
                $addressParts = array_filter([
                    $prop['name'] ?? null,
                    $prop['street'] ?? null,
                    $prop['district'] ?? ($prop['locality'] ?? null),
                    $prop['city'] ?? ($prop['county'] ?? null),
                    $prop['state'] ?? null,
                    $prop['postcode'] ?? null,
                    'Indonesia'
                ]);

                $results[] = [
                    'title'        => $title,
                    'subtitle'     => $subtitle ?: 'Indonesia',
                    'full_address' => implode(', ', array_unique($addressParts)),
                    'lat'          => (float) ($coords[1] ?? 0),
                    'lng'          => (float) ($coords[0] ?? 0),
                    'city'         => $city,
                    'province'     => $province,
                ];
            }

            return $results;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
