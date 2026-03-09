<x-app-layout>
    <x-ui.card title="Edit Report" subtitle="Update report scope, dates, and summary details.">
        @include('reports._form', [
            'action' => route('reports.update', $report),
            'method' => 'PUT',
            'report' => $report,
        ])
    </x-ui.card>
</x-app-layout>
