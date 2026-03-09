<x-app-layout>
    <x-ui.card title="Create User" subtitle="Provision a new `admin` or `veterinary_staff` account.">
        @include('users._form', [
            'action' => route('admin.users.store'),
            'method' => 'POST',
            'user' => null,
        ])
    </x-ui.card>
</x-app-layout>
