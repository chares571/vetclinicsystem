<x-app-layout>
    <x-ui.card title="Edit Pet" subtitle="Update patient information and owner details.">
        @include('pets._form', [
            'action' => route('pets.update', $pet),
            'method' => 'PUT',
            'pet' => $pet,
            'clients' => $clients ?? collect(),
        ])
    </x-ui.card>
</x-app-layout>
