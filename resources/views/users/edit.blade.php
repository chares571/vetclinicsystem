<x-app-layout>
    <x-ui.card title="Edit User" subtitle="Update user role, credentials, and security flags.">
        @include('users._form', [
            'action' => route('admin.users.update', $user),
            'method' => 'PUT',
            'user' => $user,
        ])
    </x-ui.card>
</x-app-layout>
