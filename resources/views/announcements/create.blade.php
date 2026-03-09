<x-app-layout>
    <x-ui.card title="Create Announcement" subtitle="Post a clinic update for visitors and clients.">
        @include('announcements._form', [
            'announcement' => null,
            'action' => route('announcements.store'),
            'method' => 'POST',
        ])
    </x-ui.card>
</x-app-layout>
