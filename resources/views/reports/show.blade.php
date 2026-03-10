<x-app-layout>
    <x-ui.card :title="$report->title" subtitle="Report details">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Scope</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ ucfirst($report->scope) }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Prepared By</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $report->user?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Start Date</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ optional($report->starts_on)->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.15em] text-slate-500">End Date</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ optional($report->ends_on)->format('M d, Y') ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Summary</p>
            <p class="mt-2 whitespace-pre-wrap text-sm leading-relaxed text-slate-700">{{ $report->summary ?: 'No summary provided.' }}</p>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-ui.button :href="route('reports.edit', $report)">Edit Report</x-ui.button>
            <x-ui.button :href="route('reports.index')" variant="secondary">Back</x-ui.button>
        </div>
    </x-ui.card>
</x-app-layout>
