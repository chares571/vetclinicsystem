<x-app-layout>
    <x-ui.card title="Add Hospitalization" subtitle="Register a pet confinement record.">
        @include('hospitalizations._form', [
            'hospitalization' => null,
            'action' => route('hospitalizations.store'),
            'method' => 'POST',
        ])
    </x-ui.card>
</x-app-layout>
