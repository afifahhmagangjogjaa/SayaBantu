<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SimpleRegisterController extends Controller
{
    public function show(Request $request)
    {
        if (!$request->has('role') || !in_array($request->query('role'), ['customer', 'mitra'])) {
            return redirect()->route('register.choose-role');
        }

        $role = $request->query('role');
        return view('auth.register-email', compact('role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:customer,mitra'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan login.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Pilih peran terlebih dahulu.',
        ]);

        // Simpan data akun sementara di session (data baru disimpan ke database di step terakhir setelah review)
        session([
            'reg_account' => [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]
        ]);

        return redirect()->route('onboarding.step1')->with('message', 'Data akun tersimpan. Silakan lengkapi data diri dan dokumen Anda.');
    }
}