<x-app-layout>
    <x-slot name="title">Pengaturan Notifikasi</x-slot>

    @include('partials.settings-notifications', ['notificationsRoute' => 'customer.notifications.index'])
</x-app-layout>
