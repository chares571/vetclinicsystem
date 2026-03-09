<x-app-layout>
    @php
        $canManageVaccinations = auth()->user()?->isStaffOrAdmin();
        $statusFilter = $statusFilter ?? 'all';
        $filters = [
            ['label' => 'All', 'value' => 'all'],
            ['label' => 'Due Soon', 'value' => 'due_soon'],
            ['label' => 'Overdue', 'value' => 'overdue'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Vaccinations</h1>
            <p class="text-sm text-slate-500">
                {{ $canManageVaccinations ? 'Monitor vaccine schedules and due dates.' : 'Review your pets vaccination history and reminders.' }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex overflow-hidden rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
                @foreach($filters as $filter)
                    @php
                        $isActive = $statusFilter === $filter['value'];
                        $routeParameters = $filter['value'] === 'all' ? [] : ['status' => $filter['value']];
                    @endphp
                    <a
                        href="{{ route('vaccinations.index', $routeParameters) }}"
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] transition {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                    >
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
            @if($canManageVaccinations)
                <x-ui.button :href="route('vaccinations.create')">Add Vaccination</x-ui.button>
            @endif
        </div>
    </div>

    <x-ui.card>
        @if($vaccinations->isEmpty())
            @php
                $emptyTitle = match ($statusFilter) {
                    'overdue' => 'No overdue vaccination records.',
                    'due_soon' => 'No due-soon vaccination records.',
                    default => 'No vaccination records found.',
                };
                $emptyDescription = match ($statusFilter) {
                    'overdue' => 'No pets are currently marked as overdue.',
                    'due_soon' => 'No pets are due for vaccination within the next 7 days.',
                    default => 'Create vaccination entries to track pet immunization status.',
                };
            @endphp
            <x-ui.empty-state
                :title="$emptyTitle"
                :description="$emptyDescription"
            >
                @if($canManageVaccinations)
                    <x-ui.button :href="route('vaccinations.create')">Create Vaccination</x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet Name</th>
                            <th class="px-4 py-3">Vaccine</th>
                            <th class="px-4 py-3">Date Given</th>
                            <th class="px-4 py-3">Next Due</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($vaccinations as $vaccination)
                            <tr class="transition-colors hover:bg-pink-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $vaccination->display_pet_name }}</td>
                                <td class="px-4 py-3">{{ $vaccination->vaccine_name }}</td>
                                <td class="px-4 py-3">{{ optional($vaccination->date_given)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $dueDate = $vaccination->next_due_date;
                                        $isOverdue = $dueDate && $dueDate->isBefore(today());
                                        $isDueSoon = $dueDate && ! $isOverdue && $dueDate->isBetween(today(), today()->copy()->addDays(7));
                                    @endphp
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="{{ $isOverdue ? 'text-rose-600 font-semibold' : 'text-slate-700' }}">
                                            {{ optional($dueDate)->format('M d, Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($isOverdue)
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                                            Overdue
                                        </span>
                                    @elseif($isDueSoon)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase text-amber-700">
                                            Due Soon
                                        </span>
                                    @else
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase text-emerald-700">
                                            On Schedule
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($canManageVaccinations)
                                            <x-ui.button :href="route('vaccinations.show', $vaccination)" variant="secondary">View</x-ui.button>
                                            <form method="POST" action="{{ route('vaccinations.destroy', $vaccination) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="danger" onclick="return confirm('Delete this vaccination record?')">
                                                    Delete
                                                </x-ui.button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $vaccinations->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
