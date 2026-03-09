<x-app-layout>
    <x-ui.card title="Edit Medicine" subtitle="Update inventory stock and expiration details.">
        @include('medicines._form', [
            'medicine' => $medicine,
            'action' => route('medicines.update', $medicine),
            'method' => 'PUT',
        ])
    </x-ui.card>
</x-app-layout>
