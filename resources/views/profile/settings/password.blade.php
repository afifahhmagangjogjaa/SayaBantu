<x-app-layout>
    <x-slot name="title">Ubah Kata Sandi</x-slot>

    @include('partials.settings-password', [
        'backRoute' => auth()->user()?->role === 'mitra' ? 'mitra.profile' : 'profile',
    ])
</x-app-layout>
