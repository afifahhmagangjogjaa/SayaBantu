<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    // =========================================================================
    // 0. BUAT PASSWORD
    // =========================================================================
    public function showPasswordForm()
    {
        $user = Auth::user();
        if (!empty($user->password)) {
            if (!$user->is_completed) {
                if (empty($user->nik)) {
                    return redirect()->route('onboarding.step1');
                }
                if (empty($user->ktp_photo) && empty($user->ktp_path)) {
                    return redirect()->route('onboarding.step2');
                }
                if (empty($user->selfie_photo)) {
                    return redirect()->route('onboarding.step3');
                }
            }
            return $user->role === 'mitra' ? redirect()->route('mitra.dashboard') : redirect()->route('dashboard');
        }

        return view('onboarding.create-password');
    }

    public function storePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->password),
            'is_completed' => false,
        ]);

        return redirect()->route('onboarding.step1')->with('message', 'Kata sandi berhasil dibuat! Silakan lengkapi data diri Anda.');
    }

    // =========================================================================
    // 1. STEP 1: DATA DIRI KTP
    // =========================================================================
    public function showStep1()
    {
        $user = Auth::user();
        if (empty($user->password)) {
            return redirect()->route('onboarding.password');
        }

        if ($user->is_completed) {
            return redirect()->route('dashboard');
        }

        $cities = City::orderBy('name', 'asc')->get();
        return view('onboarding.step1', compact('user', 'cities'));
    }

    public function storeStep1(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule::unique('users', 'nik')->ignore($user->id),
            ],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(17)->format('Y-m-d')],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'phone' => ['required', 'string', 'min:10', 'max:13', 'regex:/^[0-9]+$/'],
            'city_id' => ['required', 'exists:cities,id'],
            'address' => ['required', 'string', 'max:500'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'kelurahan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'kecamatan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'province' => ['required', 'string', 'max:100'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berupa angka.',
            'nik.unique' => 'NIK ini sudah terdaftar pada akun lain. Mohon gunakan NIK Anda sendiri.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama lengkap hanya boleh berupa huruf.',
            'place_of_birth.required' => 'Tempat lahir wajib diisi.',
            'date_of_birth.before_or_equal' => 'Usia minimal adalah 17 tahun.',
            'phone.min' => 'Nomor HP minimal 10 digit.',
            'phone.max' => 'Nomor HP maksimal 13 digit.',
            'city_id.required' => 'Kota domisili wajib dipilih.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'kelurahan.required' => 'Kelurahan / Desa wajib diisi.',
            'kelurahan.regex' => 'Kelurahan / Desa hanya boleh berisi huruf.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'kecamatan.regex' => 'Kecamatan hanya boleh berisi huruf.',
            'province.required' => 'Provinsi wajib diisi.',
        ]);

        // Cek apakah NIK sudah digunakan oleh akun lain di tabel users
        $existsInUser = \App\Models\User::where('nik', $request->nik)
            ->where('id', '!=', $user->id)
            ->exists();

        // Cek juga apakah NIK sudah terdaftar di tabel registrations
        $existsInReg = \App\Models\Registration::where('nik', $request->nik)
            ->where('email', '!=', $user->email)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($existsInUser || $existsInReg) {
            return back()->withErrors(['nik' => 'NIK ini sudah terdaftar pada akun lain. Mohon gunakan NIK Anda sendiri.'])->withInput();
        }

        $city = City::find($request->city_id);

        $user = $request->user();
        $user->update([
            'nik' => $request->nik,
            'name' => $request->name,
            'place_of_birth' => $request->place_of_birth,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'city_id' => $request->city_id,
            'city' => $city ? $city->name : null,
            'address' => $request->address,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kelurahan' => $request->kelurahan,
            'kecamatan' => $request->kecamatan,
            'province' => $request->province,
        ]);

        // Sync data ke Registration model untuk Admin Verifikasi
        $reg = \App\Models\Registration::where('email', $user->email)->first();
        \App\Models\Registration::updateOrCreate(
            ['email' => $user->email],
            [
                'uuid' => $reg?->uuid ?? (string) \Illuminate\Support\Str::uuid(),
                'role' => $user->role,
                'nik' => $request->nik,
                'full_name' => $request->name,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'city' => $city ? $city->name : null,
                'city_id' => $request->city_id,
                'province' => $request->province,
                'status' => 'in_progress',
            ]
        );

        return redirect()->route('onboarding.step2');
    }

    // =========================================================================
    // 2. STEP 2: UPLOAD FOTO KTP
    // =========================================================================
    public function showStep2()
    {
        $user = Auth::user();
        if (empty($user->password)) {
            return redirect()->route('onboarding.password');
        }
        if (empty($user->nik)) {
            return redirect()->route('onboarding.step1');
        }

        return view('onboarding.step2', compact('user'));
    }

    public function storeStep2(Request $request)
    {
        $user = $request->user();
        $isExisting = !empty($user->ktp_photo) || !empty($user->ktp_path);

        $rules = [
            'ktp_photo' => [$isExisting ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        $request->validate($rules, [
            'ktp_photo.required' => 'Foto fisik KTP wajib diunggah.',
            'ktp_photo.max' => 'Ukuran foto KTP maksimal 2MB.',
            'ktp_photo.image' => 'File harus berupa gambar.',
        ]);

        if ($request->hasFile('ktp_photo')) {
            $ktpPath = $request->file('ktp_photo')->store('ktp-photos', 'public');
            $user->update([
                'ktp_photo' => $ktpPath,
                'ktp_path' => $ktpPath,
            ]);

            \App\Models\Registration::where('email', $user->email)->update([
                'ktp_photo_path' => $ktpPath,
            ]);
        }

        return redirect()->route('onboarding.step3');
    }

    // =========================================================================
    // 3. STEP 3: UPLOAD FOTO SELFIE DENGAN KTP
    // =========================================================================
    public function showStep3()
    {
        $user = Auth::user();
        if (empty($user->password)) {
            return redirect()->route('onboarding.password');
        }
        if (empty($user->nik)) {
            return redirect()->route('onboarding.step1');
        }
        if (empty($user->ktp_photo) && empty($user->ktp_path)) {
            return redirect()->route('onboarding.step2');
        }

        return view('onboarding.step3', compact('user'));
    }

    public function storeStep3(Request $request)
    {
        $user = $request->user();
        $isExisting = !empty($user->selfie_photo);

        $rules = [
            'selfie_photo' => [$isExisting ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];

        $request->validate($rules, [
            'selfie_photo.required' => 'Foto selfie dengan KTP wajib diunggah.',
            'selfie_photo.max' => 'Ukuran foto selfie maksimal 2MB.',
            'selfie_photo.image' => 'File harus berupa gambar.',
        ]);

        if ($request->hasFile('selfie_photo')) {
            $selfiePath = $request->file('selfie_photo')->store('selfie-photos', 'public');
            $user->update([
                'selfie_photo' => $selfiePath,
            ]);

            \App\Models\Registration::where('email', $user->email)->update([
                'selfie_photo_path' => $selfiePath,
                'status' => 'pending_verification',
            ]);
        } else {
            \App\Models\Registration::where('email', $user->email)->update([
                'status' => 'pending_verification',
            ]);
        }

        // Tandai pendaftaran selesai & akun aktif
        $user->update([
            'is_completed' => true,
            'status' => 'active',
        ]);

        // Logout dan arahkan ke login agar user masuk dengan email & password yang baru dibuat
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Silakan login menggunakan email dan kata sandi yang telah Anda buat.');
    }

    // =========================================================================
    // 4. STEP 4: REVIEW & KONFIRMASI DATA
    // =========================================================================
    public function showStep4()
    {
        $user = Auth::user();
        if (empty($user->password)) {
            return redirect()->route('onboarding.password');
        }
        if (empty($user->nik)) {
            return redirect()->route('onboarding.step1');
        }
        if (empty($user->ktp_photo) && empty($user->ktp_path)) {
            return redirect()->route('onboarding.step2');
        }
        if (empty($user->selfie_photo)) {
            return redirect()->route('onboarding.step3');
        }

        return view('onboarding.step4', compact('user'));
    }

    public function storeStep4(Request $request)
    {
        $user = $request->user();

        $user->update([
            'is_completed' => true,
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')->with('success', 'Selamat! Pendaftaran akun Anda telah berhasil diselesaikan.');
    }

    // =========================================================================
    // 5. HELPER API WILAYAH (DISTRICTS & VILLAGES PROXY)
    // =========================================================================
    public function getDistricts(Request $request)
    {
        $cityId = $request->query('city_id');
        $cityName = $request->query('city_name');
        $province = $request->query('province');

        $city = null;
        if ($cityId) {
            $city = City::find($cityId);
        }

        if ($city && !empty($city->code)) {
            $code = $city->code;
        } else {
            $code = null;
            try {
                $provRes = \Illuminate\Support\Facades\Http::withoutVerifying()->get('https://wilayah.id/api/provinces.json');
                if ($provRes->successful()) {
                    $provs = $provRes->json('data') ?? [];
                    $targetProv = $province ?: ($city->province ?? '');
                    $cleanTargetProv = trim(preg_replace('/\b(DAERAH|ISTIMEWA|KHUSUS|IBUKOTA|PROVINSI|DI|DKI)\b/i', '', $targetProv));

                    $matchedProv = collect($provs)->first(function ($p) use ($targetProv, $cleanTargetProv) {
                        $pName = $p['name'];
                        $cleanP = trim(preg_replace('/\b(DAERAH|ISTIMEWA|KHUSUS|IBUKOTA|PROVINSI|DI|DKI)\b/i', '', $pName));
                        return strtoupper(trim($targetProv)) === strtoupper(trim($pName)) ||
                            (!empty($cleanTargetProv) && stripos($cleanP, $cleanTargetProv) !== false) ||
                            (!empty($cleanTargetProv) && stripos($pName, $cleanTargetProv) !== false);
                    });

                    if ($matchedProv) {
                        $regRes = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://wilayah.id/api/regencies/{$matchedProv['code']}.json");
                        if ($regRes->successful()) {
                            $regs = $regRes->json('data') ?? [];
                            $targetCity = $cityName ?: ($city->name ?? '');
                            $cleanTargetCity = trim(preg_replace('/\b(KOTA\s+ADM|KOTA|KABUPATEN|KAB)\b/i', '', $targetCity));

                            $matchedReg = collect($regs)->first(function ($r) use ($targetCity, $cleanTargetCity) {
                                $rName = $r['name'];
                                $cleanR = trim(preg_replace('/\b(KOTA\s+ADM|KOTA|KABUPATEN|KAB)\b/i', '', $rName));
                                return strtoupper(trim($targetCity)) === strtoupper(trim($rName)) ||
                                    (!empty($cleanTargetCity) && stripos($cleanR, $cleanTargetCity) !== false) ||
                                    (!empty($cleanTargetCity) && stripos($rName, $cleanTargetCity) !== false);
                            });

                            if ($matchedReg) {
                                $code = $matchedReg['code'];
                                if ($city) {
                                    $city->update(['code' => $code]);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {}
        }

        if ($code) {
            try {
                $distRes = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://wilayah.id/api/districts/{$code}.json");
                if ($distRes->successful()) {
                    return response()->json(['success' => true, 'data' => $distRes->json('data') ?? []]);
                }
            } catch (\Exception $e) {}
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    public function getVillages(Request $request)
    {
        $districtCode = $request->query('district_code');
        if (!$districtCode) {
            return response()->json(['success' => false, 'data' => []]);
        }

        try {
            $villRes = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://wilayah.id/api/villages/{$districtCode}.json");
            if ($villRes->successful()) {
                return response()->json(['success' => true, 'data' => $villRes->json('data') ?? []]);
            }
        } catch (\Exception $e) {}

        return response()->json(['success' => false, 'data' => []]);
    }
}