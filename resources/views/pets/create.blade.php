<x-app-layout>
    <x-ui.card title="Pet Patient" subtitle="Register a new patient profile.">
        @include('pets._form', [
            'action' => route('pets.store'),
            'method' => 'POST',
            'pet' => null,
            'clients' => $clients ?? collect(),
        ])
    </x-ui.card>
</x-app-layout>
