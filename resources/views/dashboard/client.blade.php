<x-app-layout>
    <section class="rounded-3xl bg-gradient-to-r from-blue-700 via-blue-500 to-pink-400 p-6 text-white shadow-xl animate-rise">
        <p class="text-xs uppercase tracking-[0.25em] text-blue-100">Client Dashboard</p>
        <h1 class="mt-2 text-3xl font-extrabold">My Pet Care Portal</h1>
        <p class="mt-2 text-sm text-blue-100">Monitor pets, appointments, and upcoming vaccination reminders.</p>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="My Pets" :value="$metrics['myPets']" tone="brand" />
        <x-ui.stat-card label="Appointments" :value="$metrics['myAppointments']" tone="slate" />
        <x-ui.stat-card label="Pending Requests" :value="$metrics['pendingAppointments']" tone="accent" />
        <x-ui.stat-card label="Vaccine Reminders" :value="$metrics['upcomingVaccinations']" tone="pink" />
    </section>

    <section>
        <x-ui.card title="Quick Access">
            <div class="grid grid-cols-1 gap-3">
                <x-ui.button :href="route('pets.create')">Register Pet</x-ui.button>
                <x-ui.button :href="route('appointments.create')" variant="secondary">Request Appointment</x-ui.button>
                <x-ui.button :href="route('appointments.grooming.create')" variant="secondary">Request Grooming Appointment</x-ui.button>
                <x-ui.button :href="route('vaccinations.overdue')" variant="secondary">View Vaccine Reminders</x-ui.button>
            </div>
        </x-ui.card>
    </section>

    <x-ui.card title="Upcoming Appointments">
        @if($upcomingAppointments->isEmpty())
            <x-ui.empty-state
                title="No upcoming appointments."
                description="Request a consultation to start your pet care timeline."
            >
                <x-ui.button :href="route('appointments.create')">Request Appointment</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Pet</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Purpose</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($upcomingAppointments as $appointment)
                            <tr class="transition-colors hover:bg-pink-50/60">
                                <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $appointment->display_pet_name }}</td>
                                <td class="px-4 py-3">{{ $appointment->type_label }}</td>
                                <td class="px-4 py-3">{{ $appointment->display_purpose }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-600">
                                        {{ $appointment->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>

</x-app-layout>
