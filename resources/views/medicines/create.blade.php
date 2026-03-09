<x-app-layout>
    <x-ui.card title="Add Medicine" subtitle="Create a medicine inventory item.">
        @include('medicines._form', [
            'medicine' => null,
            'action' => route('medicines.store'),
            'method' => 'POST',
        ])
    </x-ui.card>
</x-app-layout>
