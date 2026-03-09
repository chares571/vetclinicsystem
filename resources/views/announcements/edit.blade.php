<x-app-layout>
    <x-ui.card title="Edit Announcement" subtitle="Update your clinic announcement details.">
        @include('announcements._form', [
            'announcement' => $announcement,
            'action' => route('announcements.update', $announcement),
            'method' => 'PUT',
        ])
    </x-ui.card>
</x-app-layout>
