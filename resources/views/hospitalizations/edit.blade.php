<x-app-layout>
    <x-ui.card title="Edit Hospitalization" subtitle="Update confinement status and notes.">
        @include('hospitalizations._form', [
            'hospitalization' => $hospitalization,
            'action' => route('hospitalizations.update', $hospitalization),
            'method' => 'PUT',
        ])
    </x-ui.card>
</x-app-layout>
