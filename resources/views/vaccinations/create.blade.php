<x-app-layout>
    <x-ui.card title="Create Vaccination" subtitle="Add a new vaccination record for a pet.">
        @include('vaccinations._form', [
            'action' => route('vaccinations.store'),
            'method' => 'POST',
            'vaccination' => null,
        ])
    </x-ui.card>
</x-app-layout>
