<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Support\JsonMaster;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Proxy data wilayah (kecamatan & kelurahan).
 *
 * Master lokal (database/data/*.json) dicek lebih dulu supaya respons instan dan
 * tidak bergantung jaringan; API wilayah.id hanya dipakai sebagai cadangan.
 */
class RegionController extends Controller
{
    private function client(): PendingRequest
    {
        return Http::withOptions([
            'verify' => (bool) config('services.wilayah.verify_tls', true),
        ]);
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.wilayah.base_url'), '/').'/'.$path;
    }

    public function getDistricts(Request $request): JsonResponse
    {
        $cityId = $request->query('city_id');
        $cityName = $request->query('city_name');
        $province = $request->query('province');

        $city = $cityId ? City::find($cityId) : null;

        // 1. Master lokal
        $master = JsonMaster::districts();
        foreach ([$cityId, $city?->code, $cityName ? strtoupper(trim($cityName)) : null] as $lookupKey) {
            $found = JsonMaster::find($master, $lookupKey);
            if ($found !== []) {
                return response()->json(['success' => true, 'data' => $found]);
            }
        }

        // 2. Cadangan: cari kode kota lewat API, lalu simpan ke tabel cities
        $code = $city && ! empty($city->code) ? $city->code : null;

        if (! $code) {
            try {
                $provRes = $this->client()->timeout(5)->get($this->url('provinces.json'));

                if ($provRes->successful()) {
                    $provs = $provRes->json('data') ?? [];
                    $targetProv = $province ?: ($city->province ?? '');
                    $cleanTargetProv = $this->cleanProvinceName($targetProv);

                    $matchedProv = collect($provs)->first(function ($p) use ($targetProv, $cleanTargetProv) {
                        $pName = $p['name'];

                        return strtoupper(trim($targetProv)) === strtoupper(trim($pName))
                            || ($cleanTargetProv !== '' && stripos($this->cleanProvinceName($pName), $cleanTargetProv) !== false)
                            || ($cleanTargetProv !== '' && stripos($pName, $cleanTargetProv) !== false);
                    });

                    if ($matchedProv) {
                        $regRes = $this->client()->timeout(5)->get($this->url("regencies/{$matchedProv['code']}.json"));

                        if ($regRes->successful()) {
                            $regs = $regRes->json('data') ?? [];
                            $targetCity = $cityName ?: ($city->name ?? '');
                            $cleanTargetCity = $this->cleanCityName($targetCity);

                            $matchedReg = collect($regs)->first(function ($r) use ($targetCity, $cleanTargetCity) {
                                $rName = $r['name'];

                                return strtoupper(trim($targetCity)) === strtoupper(trim($rName))
                                    || ($cleanTargetCity !== '' && stripos($this->cleanCityName($rName), $cleanTargetCity) !== false)
                                    || ($cleanTargetCity !== '' && stripos($rName, $cleanTargetCity) !== false);
                            });

                            if ($matchedReg) {
                                $code = $matchedReg['code'];
                                $city?->update(['code' => $code]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // diamkan: endpoint tetap mengembalikan success=false
            }
        }

        if ($code) {
            try {
                $distRes = $this->client()->timeout(6)->get($this->url("districts/{$this->dottedCode($code)}.json"));

                if ($distRes->successful()) {
                    return response()->json(['success' => true, 'data' => $distRes->json('data') ?? []]);
                }
            } catch (\Exception $e) {
            }
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    public function getVillages(Request $request): JsonResponse
    {
        $districtCode = $request->query('district_code');
        $districtName = $request->query('district_name');

        if (! $districtCode && ! $districtName) {
            return response()->json(['success' => false, 'data' => []]);
        }

        // 1. Master lokal (lookup via code atau nama kecamatan)
        $master = JsonMaster::villages();
        foreach (array_filter([$districtCode, $districtName ? strtoupper(trim($districtName)) : null]) as $lookupKey) {
            $found = JsonMaster::find($master, $lookupKey);
            if ($found !== []) {
                return response()->json(['success' => true, 'data' => $found]);
            }
        }

        // 2. Cadangan: API dengan cache 7 hari
        if ($districtCode) {
            try {
                $dotted = strlen($districtCode) === 6 && ! str_contains($districtCode, '.')
                    ? substr($districtCode, 0, 2).'.'.substr($districtCode, 2, 2).'.'.substr($districtCode, 4, 2)
                    : $districtCode;

                $villages = Cache::remember("wilayah_villages_{$dotted}", now()->addDays(7), function () use ($dotted, $districtCode) {
                    $villRes = $this->client()->timeout(6)->get($this->url("villages/{$dotted}.json"));
                    if (! $villRes->successful() && $dotted !== $districtCode) {
                        $villRes = $this->client()->timeout(6)->get($this->url("villages/{$districtCode}.json"));
                    }
                    return $villRes->successful() ? ($villRes->json('data') ?? []) : [];
                });

                if (! empty($villages)) {
                    return response()->json(['success' => true, 'data' => $villages]);
                }
            } catch (\Exception $e) {
            }
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    private function cleanProvinceName(string $name): string
    {
        return trim(preg_replace('/\b(DAERAH|ISTIMEWA|KHUSUS|IBUKOTA|PROVINSI|DI|DKI)\b/i', '', $name));
    }

    private function cleanCityName(string $name): string
    {
        return trim(preg_replace('/\b(KOTA\s+ADM|KOTA|KABUPATEN|KAB)\b/i', '', $name));
    }

    /** Kode wilayah.id memakai format bertitik: 3471 -> 34.71 */
    private function dottedCode(string $code): string
    {
        return strlen($code) === 4 && ! str_contains($code, '.')
            ? substr($code, 0, 2).'.'.substr($code, 2, 2)
            : $code;
    }
}
