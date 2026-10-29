@props(['user' => null, 'alt' => null])

@php
    $avatarUrl = $user?->avatar_url;
    $name = trim((string) ($user?->name ?? ''));
    $initial = $name !== '' ? strtoupper(substr($name, 0, 1)) : '?';
    $alt = $alt ?? ($name !== '' ? $name : 'Avatar');
@endphp

{{--
    Inisial selalu dirender lebih dulu, foto profil menimpanya secara absolut.
    Bila foto gagal dimuat (mis. URL foto Google kedaluwarsa atau diblokir
    jaringan), gambar dihapus dan inisial kelihatan lagi tanpa JavaScript lain.

    Ukuran/warna/rounded diatur lewat class yang dikirim pemakai, contoh:
    <x-user-avatar :user="$user" class="h-full w-full" />
--}}
<span {{ $attributes->class('relative overflow-hidden inline-flex items-center justify-center') }}>
    <span aria-hidden="true" class="select-none">{{ $initial }}</span>

    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="{{ $alt }}"
            class="absolute inset-0 h-full w-full object-cover"
            onerror="this.remove()">
    @endif
</span>
