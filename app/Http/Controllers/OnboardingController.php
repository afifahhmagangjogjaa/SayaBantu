<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Registration;
use App\Models\User;
use App\Notifications\NewRegistrationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    /**
     * Mendapatkan user aktif atau membuat instance User sementara dari data session registrasi.
     */
    protected function getRegistrationUser()
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $account = session('reg_account');
        if (! $account) {
            return null;
        }

        $step1 = session('reg_step1', []);
        $step2 = session('reg_step2', []);
        $step3 = session('reg_step3', []);

        $userData = array_merge($account, $step1, $step2, $step3);

        return new User($userData);
    }

    // =========================================================================
    // 0. BUAT PASSWORD
    // =========================================================================
    public function showPasswordForm()
    {
        if (! Auth::check()) {
            return redirect()->route('register.choose-role');
        }

        $user = Auth::user();
        if (! empty($user->password)) {
            if (! $user->is_completed) {
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
        if (! Auth::check()) {
            return redirect()->route('register.choose-role');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, angka, dan karakter khusus / simbol.',
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
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        if ($user->exists && $user->is_completed) {
            return $user->role === 'mitra' ? redirect()->route('mitra.dashboard') : redirect()->route('dashboard');
        }

        if (Auth::check() && empty($user->password)) {
            return redirect()->route('onboarding.password');
        }

        $cities = City::where('is_active', true)->orderBy('name', 'asc')->get();
        $allDistricts = \App\Support\JsonMaster::districts();
        $allVillages = \App\Support\JsonMaster::villages();

        return view('onboarding.step1', compact('user', 'cities', 'allDistricts', 'allVillages'));
    }

    public function storeStep1(Request $request)
    {
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        $userId = $user->exists ? $user->id : null;
        $userEmail = $user->email;

        $isMitraRoute = $request->routeIs('onboarding.step1.store');

        $rules = [
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule::unique('users', 'nik')->ignore($userId),
            ],
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'],
            'gender' => ['required', 'in:Laki-laki,Perempuan'],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:13',
                'regex:/^[0-9]+$/',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'city_id' => ['required', Rule::exists('cities', 'id')->where('is_active', true)],
            'address' => ['required', 'string', 'max:500'],
        ];

        // Field tambahan domisili HANYA untuk alur mitra (step1)
        if ($isMitraRoute) {
            $rules['kelurahan'] = ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'];
            $rules['kecamatan'] = ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\,\'\-]+$/'];
            $rules['province'] = ['required', 'string', 'max:100'];
        }

        $request->validate($rules, [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berupa angka.',
            'nik.unique' => 'NIK ini sudah terdaftar pada akun lain. Mohon gunakan NIK Anda sendiri.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama lengkap hanya boleh berupa huruf.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.min' => 'Nomor HP minimal 10 digit.',
            'phone.max' => 'Nomor HP maksimal 13 digit.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar pada akun lain. Mohon gunakan nomor HP Anda yang lain.',
            'city_id.required' => 'Kota domisili wajib dipilih.',
            'city_id.exists' => 'Kota yang dipilih saat ini sedang tidak aktif.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'kelurahan.required' => 'Kelurahan / Desa domisili wajib diisi.',
            'kelurahan.regex' => 'Kelurahan / Desa domisili hanya boleh berisi huruf.',
            'kecamatan.required' => 'Kecamatan domisili wajib diisi.',
            'kecamatan.regex' => 'Kecamatan domisili hanya boleh berisi huruf.',
            'province.required' => 'Provinsi domisili wajib diisi.',
        ]);

        // Cek apakah NIK sudah digunakan oleh akun lain di tabel users
        $existsInUser = User::where('nik', $request->nik)
            ->when($userId, fn ($q) => $q->where('id', '!=', $userId))
            ->exists();

        // Cek juga apakah NIK sudah terdaftar di tabel registrations
        $existsInReg = Registration::where('nik', $request->nik)
            ->where('email', '!=', $userEmail)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($existsInUser || $existsInReg) {
            return back()->withErrors(['nik' => 'NIK ini sudah terdaftar pada akun lain. Mohon gunakan NIK Anda sendiri.'])->withInput();
        }

        // Cek apakah Nomor HP sudah digunakan oleh akun lain di tabel users
        $phoneExistsInUser = User::where('phone', $request->phone)
            ->when($userId, fn ($q) => $q->where('id', '!=', $userId))
            ->exists();

        if ($phoneExistsInUser) {
            return back()->withErrors(['phone' => 'Nomor HP ini sudah terdaftar pada akun lain. Mohon gunakan nomor HP Anda yang lain.'])->withInput();
        }

        $city = City::find($request->city_id);

        if (Auth::check()) {
            $authUser = $request->user();
            $authUser->update([
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
            $reg = Registration::where('email', $authUser->email)->first();
            Registration::updateOrCreate(
                ['email' => $authUser->email],
                [
                    'uuid' => $reg?->uuid ?? (string) Str::uuid(),
                    'role' => $authUser->role,
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
        } else {
            // Tamu: simpan ke session sementara
            session([
                'reg_step1' => [
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
                ],
            ]);
        }

        return redirect()->route('onboarding.step2');
    }

    // =========================================================================
    // 2. STEP 2: UPLOAD FOTO KTP
    // =========================================================================
    public function showStep2()
    {
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        if (Auth::check() && empty($user->password)) {
            return redirect()->route('onboarding.password');
        }
        if (empty($user->nik)) {
            return redirect()->route('onboarding.step1');
        }

        return view('onboarding.step2', compact('user'));
    }

    public function storeStep2(Request $request)
    {
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        $isExisting = ! empty($user->ktp_photo) || ! empty($user->ktp_path);

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

            if (Auth::check()) {
                $authUser = $request->user();
                $authUser->update([
                    'ktp_photo' => $ktpPath,
                    'ktp_path' => $ktpPath,
                ]);

                Registration::where('email', $authUser->email)->update([
                    'ktp_photo_path' => $ktpPath,
                ]);
            } else {
                session([
                    'reg_step2' => [
                        'ktp_photo' => $ktpPath,
                        'ktp_path' => $ktpPath,
                    ],
                ]);
            }
        }

        return redirect()->route('onboarding.step3');
    }

    // =========================================================================
    // 3. STEP 3: UPLOAD FOTO SELFIE DENGAN KTP
    // =========================================================================
    public function showStep3()
    {
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        if (Auth::check() && empty($user->password)) {
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
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        $isExisting = ! empty($user->selfie_photo);

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

            if (Auth::check()) {
                $authUser = $request->user();
                $authUser->update([
                    'selfie_photo' => $selfiePath,
                ]);

                Registration::where('email', $authUser->email)->update([
                    'selfie_photo_path' => $selfiePath,
                    'status' => 'pending_verification',
                ]);
            } else {
                session([
                    'reg_step3' => [
                        'selfie_photo' => $selfiePath,
                    ],
                ]);
            }
        }

        return redirect()->route('onboarding.step4')->with('message', 'Foto KTP dan Selfie berhasil diunggah! Silakan periksa kembali kelengkapan data Anda.');
    }

    // =========================================================================
    // 4. STEP 4: REVIEW & KONFIRMASI DATA (SIMPAN KE DATABASE DISINI)
    // =========================================================================
    public function showStep4()
    {
        $user = $this->getRegistrationUser();
        if (! $user) {
            return redirect()->route('register.choose-role');
        }

        if (Auth::check() && empty($user->password)) {
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
        if (Auth::check()) {
            $user = $request->user();

            $user->update([
                'is_completed' => true,
                'status' => 'active',
            ]);

            Registration::where('email', $user->email)->update([
                'status' => 'pending_verification',
            ]);

            try {
                $superAdmins = User::where('role', 'super_admin')->where('status', 'active')->get();
                foreach ($superAdmins as $admin) {
                    $admin->notify(new NewRegistrationNotification($user));
                }
            } catch (\Throwable $e) {
            }

            if ($user->role === 'mitra') {
                return redirect()->route('mitra.dashboard')->with('status', 'Pendaftaran selesai! Akun Anda aktif dengan kuota order terbatas. Silakan verifikasi email Anda untuk membuka kuota tanpa batas.');
            }

            return redirect()->route('customer.dashboard')->with('status', 'Pendaftaran selesai! Selamat datang di SayaBantu.');
        }

        // TAMU: Data pendaftaran BARU disimpan ke database pada saat ini (setelah review dan klik Selesai & Masuk)
        $account = session('reg_account');
        $step1 = session('reg_step1', []);
        $step2 = session('reg_step2', []);
        $step3 = session('reg_step3', []);

        if (! $account || empty($step1['nik'])) {
            return redirect()->route('register.choose-role');
        }

        // Cek kembali untuk menghindari duplikasi
        if (User::where('email', $account['email'])->exists()) {
            return redirect()->route('login')->with('error', 'Email ini sudah terdaftar. Silakan login.');
        }
        if (User::where('nik', $step1['nik'])->exists()) {
            return redirect()->route('onboarding.step1')->withErrors(['nik' => 'NIK ini sudah terdaftar pada akun lain.']);
        }
        if (! empty($step1['phone']) && User::where('phone', $step1['phone'])->exists()) {
            return redirect()->route('onboarding.step1')->withErrors(['phone' => 'Nomor HP ini sudah terdaftar pada akun lain.']);
        }

        $userData = array_merge($account, $step1, $step2, $step3, [
            'is_completed' => true,
            'status' => 'active',
            'verified' => false,
        ]);

        $user = User::create($userData);

        // Buat record Registration untuk verifikasi admin
        try {
            Registration::updateOrCreate(
                ['email' => $user->email],
                [
                    'uuid' => (string) Str::uuid(),
                    'role' => $user->role,
                    'nik' => $user->nik,
                    'full_name' => $user->name,
                    'phone' => $user->phone,
                    'place_of_birth' => $user->place_of_birth,
                    'date_of_birth' => $user->date_of_birth,
                    'gender' => $user->gender,
                    'address' => $user->address,
                    'rt' => $user->rt,
                    'rw' => $user->rw,
                    'kelurahan' => $user->kelurahan,
                    'kecamatan' => $user->kecamatan,
                    'city' => $user->city,
                    'city_id' => $user->city_id,
                    'province' => $user->province,
                    'ktp_photo_path' => $user->ktp_photo ?? $user->ktp_path,
                    'selfie_photo_path' => $user->selfie_photo,
                    'status' => 'pending_verification',
                ]
            );
        } catch (\Throwable $e) {
        }

        // Mengirim event registrasi & email verifikasi
        event(new Registered($user));

        // Kirim notifikasi pengguna baru ke super admin
        try {
            $superAdmins = User::where('role', 'super_admin')->where('status', 'active')->get();
            foreach ($superAdmins as $admin) {
                $admin->notify(new NewRegistrationNotification($user));
            }
        } catch (\Throwable $e) {
        }

        // Kirim notifikasi pengajuan KTP baru ke Admin Kota terkait
        try {
            $cityId = $user->city_id;
            if (! $cityId && ! empty($user->city)) {
                $cityId = \App\Models\City::where('name', 'like', '%'.trim($user->city).'%')->value('id');
                if ($cityId) {
                    $user->city_id = $cityId;
                    $user->save();
                }
            }
            if ($cityId) {
                $cityAdmins = User::getAdminsForCity($cityId);
                if ($cityAdmins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($cityAdmins, new \App\Notifications\NewKtpVerificationNotification($user));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notif KTP baru ke admin kota: '.$e->getMessage());
        }

        // Login kan user langsung
        Auth::login($user);

        // Hapus data registrasi sementara dari session
        session()->forget(['reg_account', 'reg_step1', 'reg_step2', 'reg_step3']);

        if ($user->role === 'mitra') {
            return redirect()->route('mitra.dashboard')->with('status', 'Pendaftaran selesai! Akun Anda aktif dengan kuota order terbatas. Silakan verifikasi email Anda untuk membuka kuota tanpa batas.');
        }

        return redirect()->route('customer.dashboard')->with('status', 'Pendaftaran selesai! Selamat datang di SayaBantu.');
    }

    /**
     * Cancel ongoing onboarding and reset session state.
     */
    public function cancel(Request $request)
    {
        session()->forget(['reg_account', 'reg_step1', 'reg_step2', 'reg_step3', 'reg_role']);

        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register.choose-role');
    }
}
