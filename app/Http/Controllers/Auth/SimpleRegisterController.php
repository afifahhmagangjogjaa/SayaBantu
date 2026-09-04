<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:customer,mitra'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan login.',
            'email.required' => 'Email wajib diisi.',
            'role.required' => 'Pilih peran terlebih dahulu.',
        ]);

        // Buat akun baru dengan email dan role yang dipilih
        $user = User::create([
            'name' => explode('@', $request->email)[0],
            'email' => $request->email,
            'password' => null,
            'role' => $request->role,
            'status' => 'active',
            'verified' => false,
        ]);

        // Mengirim link verifikasi email secara otomatis
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}