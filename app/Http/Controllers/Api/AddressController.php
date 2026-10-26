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
        $provinceBias = trim($request->get('province', ''));
        $districtBias = trim($request->get('district', ''));

        // Filter out placeholder labels like "-- Pilih ..."
        $cleanFilter = fn($val) => preg_match('/(--|pilih|memuat)/i', $val) ? '' : trim($val);
        $cityBias = $cleanFilter($cityBias);
        $provinceBias = $cleanFilter($provinceBias);
        $districtBias = $cleanFilter($districtBias);

        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }

        $cacheKey = 'address_suggest_' . md5(strtolower("{$q}|{$cityBias}|{$provinceBias}|{$districtBias}"));

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (!empty($cached) && is_array($cached)) {
                return response()->json($cached);
            }
        }

        $results = $this->fetchFromNominatim($q, $cityBias, $provinceBias, $districtBias);
        if (empty($results)) {
            $results = $this->fetchFromPhoton($q, $cityBias, $provinceBias);
        }

        // Sort results to prioritize matching city/province
        if (!empty($results) && !empty($cityBias)) {
            $cleanCity = strtolower(preg_replace('/\b(kota|kabupaten|kab|adm|administrasi)\b/i', '', $cityBias));
            usort($results, function ($a, $b) use ($cleanCity) {
                $aMatch = (stripos(strtolower($a['city'] ?? ''), $cleanCity) !== false) || (stripos(strtolower($a['full_address'] ?? ''), $cleanCity) !== false);
                $bMatch = (stripos(strtolower($b['city'] ?? ''), $cleanCity) !== false) || (stripos(strtolower($b['full_address'] ?? ''), $cleanCity) !== false);
                return ($bMatch ? 1 : 0) - ($aMatch ? 1 : 0);
            });
        }

        if (!empty($results)) {
            Cache::put($cacheKey, $results, 86400);
        }

        return response()->json($results ?: []);
    }

    /**
     * Fetch suggestions from Nominatim OpenStreetMap
     */
    protected function fetchFromNominatim(string $q, string $cityBias = '', string $provinceBias = '', string $districtBias = ''): array
    {
        try {
            $biasParts = array_filter([$districtBias, $cityBias, $provinceBias]);
            $searchQuery = !empty($biasParts) ? "{$q}, " . implode(', ', $biasParts) : $q;
            $encodedQuery = urlencode($searchQuery);

            $response = \App\Support\ExternalHttp::client()
                ->withHeaders([
                    'User-Agent' => 'SayaBantu-App/1.0 (contact@sayabantu.id)',
                    'Accept-Language' => 'id,en;q=0.9',
                ])
                ->timeout(4)
                ->get("https://nominatim.openstreetmap.org/search?q={$encodedQuery}&format=json&addressdetails=1&countrycodes=id&limit=8");

            // If empty and we had multiple bias filters, fallback to just city bias
            if ((!$response->successful() || empty($response->json())) && !empty($biasParts)) {
                $searchQuery = $cityBias ? "{$q}, {$cityBias}" : $q;
                $encodedQuery = urlencode($searchQuery);
                $response = \App\Support\ExternalHttp::client()
                    ->withHeaders([
                        'User-Agent' => 'SayaBantu-App/1.0 (contact@sayabantu.id)',
                        'Accept-Language' => 'id,en;q=0.9',
                    ])
                    ->timeout(4)
                    ->get("https://nominatim.openstreetmap.org/search?q={$encodedQuery}&format=json&addressdetails=1&countrycodes=id&limit=8");
            }

            if (!$response->successful() || empty($response->json())) {
                return [];
            }

            $results = [];
            foreach ($response->json() as $item) {
                $addr = $item['address'] ?? [];

                $state = $addr['state'] ?? ($addr['province'] ?? '');
                $rawCity = $addr['city'] ?? ($addr['town'] ?? ($addr['municipality'] ?? ''));
                $county = $addr['county'] ?? ($addr['regency'] ?? '');
                $cityDistrict = $addr['city_district'] ?? '';

                // Indonesian Administrative Division Mapping
                if (stripos($rawCity, 'Jakarta') !== false || stripos($state, 'Jakarta') !== false || stripos($cityDistrict, 'Jakarta') !== false) {
                    $province = 'DKI Jakarta';
                    if (stripos($cityDistrict, 'Jakarta') !== false) {
                        $city = $cityDistrict;
                    } elseif (stripos($county, 'Jakarta') !== false) {
                        $city = $county;
                    } else {
                        $city = $rawCity;
                    }
                    $district = $addr['suburb'] ?? ($addr['district'] ?? '');
                } else {
                    $province = $state;
                    $city = $rawCity ?: $county;
                    $district = $cityDistrict ?: ($addr['suburb'] ?? ($addr['district'] ?? ''));
                }

                $village = $addr['village'] ?? ($addr['neighbourhood'] ?? ($addr['quarter'] ?? ''));
                $road = $addr['road'] ?? '';
                $name = $item['name'] ?? '';
                $houseNumber = $addr['house_number'] ?? '';

                // Build a concise, clean title
                if ($name && $road && stripos($name, $road) === false && stripos($road, $name) === false) {
                    $title = $name . ($houseNumber ? " No. {$houseNumber}" : '') . ", {$road}";
                } elseif ($road) {
                    $title = $road . ($houseNumber ? " No. {$houseNumber}" : '');
                } elseif ($name) {
                    $title = $name . ($houseNumber ? " No. {$houseNumber}" : '');
                } else {
                    $title = $item['display_name'];
                }

                $subtitleParts = array_filter([$village, $district, $city, $province], fn($val) => !empty($val) && stripos($title, $val) === false);
                $subtitle = implode(', ', array_unique($subtitleParts));

                $fullAddressParts = array_filter([$title, $village, $district, $city, $province]);
                $fullAddress = implode(', ', array_unique($fullAddressParts));

                $results[] = [
                    'title'        => $title,
                    'subtitle'     => $subtitle ?: $item['display_name'],
                    'full_address' => $fullAddress ?: $item['display_name'],
                    'lat'          => (float) $item['lat'],
                    'lng'          => (float) $item['lon'],
                    'city'         => $city,
                    'province'     => $province,
                    'district'     => $district,
                    'village'      => $village,
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
    protected function fetchFromPhoton(string $q, string $cityBias = '', string $provinceBias = ''): array
    {
        try {
            $biasParts = array_filter([$cityBias, $provinceBias]);
            $searchQuery = !empty($biasParts) ? "{$q} " . implode(' ', $biasParts) : $q;
            $encodedQuery = urlencode($searchQuery);

            $response = \App\Support\ExternalHttp::client()
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

                // Normalize Jakarta
                if (stripos($city, 'Jakarta') !== false || stripos($province, 'Jakarta') !== false) {
                    $province = 'DKI Jakarta';
                    if (empty($city) || $city === 'DKI Jakarta') {
                        $city = $prop['district'] ?? 'Jakarta';
                    }
                }

                $subtitleParts = array_filter([$district, $city, $province], fn($val) => !empty($val) && $val !== $title);
                $subtitle = implode(', ', array_unique($subtitleParts));

                $addressParts = array_filter([
                    $title,
                    $district,
                    $city,
                    $province,
                ]);

                $results[] = [
                    'title'        => $title,
                    'subtitle'     => $subtitle ?: 'Indonesia',
                    'full_address' => implode(', ', array_unique($addressParts)),
                    'lat'          => (float) ($coords[1] ?? 0),
                    'lng'          => (float) ($coords[0] ?? 0),
                    'city'         => $city,
                    'province'     => $province,
                    'district'     => $district,
                    'village'      => '',
                ];
            }

            return $results;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
