<x-app-layout>
    <x-ui.card title="Edit Vaccination" subtitle="Update vaccine details and due date.">
        @include('vaccinations._form', [
            'action' => route('vaccinations.update', $vaccination),
            'method' => 'PUT',
            'vaccination' => $vaccination,
        ])
    </x-ui.card>
</x-app-layout>
