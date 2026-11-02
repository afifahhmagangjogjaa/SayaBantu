<x-app-layout>
    <x-slot name="title">Ubah Kata Sandi</x-slot>

    @include('partials.settings-password', [
        'backRoute' => 'profile.settings',
    ])
</x-app-layout>
