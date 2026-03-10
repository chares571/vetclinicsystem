<x-app-layout>
    @php
        $isClient = auth()->user()?->isClient();
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Appointments</h1>
            <p class="text-sm text-slate-500">
                {{ $isClient ? 'Track your appointment requests and clinic confirmations.' : 'Track and manage clinic schedules.' }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(! $isClient)
                <x-ui.button :href="route('appointments.completed')" variant="secondary">Completed Appointments</x-ui.button>
            @endif
            <x-ui.button :href="route('appointments.create')">{{ $isClient ? 'Request Appointment' : 'Add Appointment' }}</x-ui.button>
            @if($isClient)
                <x-ui.button :href="route('appointments.grooming.create')" variant="secondary">Request Grooming</x-ui.button>
            @endif
        </div>
    </div>

    <x-ui.card>
        @if($appointments->isEmpty())
            <x-ui.empty-state
                title="No active appointments found."
                description="Only pending and approved appointments appear here."
            >
                <x-ui.button :href="route('appointments.create')">Create Appointment</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Pet Name</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Purpose</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($appointments as $appointment)
                            <tr class="transition-colors hover:bg-pink-50/60">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $appointment->display_pet_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->type_label }}</td>
                                <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $appointment->display_purpose }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col items-start gap-1">
                                        @if($appointment->is_emergency)
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                                                Emergency
                                            </span>
                                        @endif
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600">
                                            {{ $appointment->status_label }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary">Edit</x-ui.button>
                                        @if($isClient && $appointment->status === \App\Models\Appointment::STATUS_PENDING)
                                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-xl bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2"
                                                    onclick="return confirm('Are you sure you want to cancel this appointment?')"
                                                >
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                        @if(! $isClient)
                                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button variant="danger" onclick="return confirm('Delete this appointment?')">
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
                {{ $appointments->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>
