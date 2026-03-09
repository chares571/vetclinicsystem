<x-app-layout>
    <x-ui.card title="Create Report" subtitle="Document and summarize clinic performance snapshots.">
        @include('reports._form', [
            'action' => route('reports.store'),
            'method' => 'POST',
            'report' => null,
        ])
    </x-ui.card>
</x-app-layout>
