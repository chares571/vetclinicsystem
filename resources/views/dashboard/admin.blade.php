<x-app-layout>
    <section class="rounded-3xl bg-gradient-to-r from-blue-700 via-blue-500 to-pink-400 p-6 text-white shadow-xl animate-rise">
        <p class="text-xs uppercase tracking-[0.25em] text-blue-100">Veterinary Staff Dashboard</p>
        <h1 class="mt-2 text-3xl font-extrabold">Clinic Operations</h1>
        <p class="mt-2 text-sm text-blue-100">Track your assigned patients, schedules, and vaccination timelines.</p>
    </section>

    <section class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">System Overview</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.stat-card label="Pet Patients" :value="$metrics['totalPets']" tone="blue" variant="solid" icon="pets" />
            <x-ui.stat-card label="Appointments" :value="$metrics['totalAppointments']" tone="blue" variant="solid" icon="appointments" />
            <x-ui.stat-card label="Vaccinations" :value="$metrics['totalVaccinations']" tone="blue" variant="solid" icon="vaccination" />
        </div>
    </section>

    <section class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Clinical Alerts</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.stat-card label="Due Soon" :value="$metrics['dueSoonVaccinations']" tone="yellow" variant="solid" icon="clock" />
            <x-ui.stat-card label="Overdue" :value="$metrics['overdueVaccinations']" tone="red" variant="solid" icon="alert" :critical="true" />
            <x-ui.stat-card label="Confined Pets" :value="$metrics['activeHospitalizations']" tone="purple" variant="solid" icon="hospital" />
        </div>
    </section>

    <section class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Inventory Status</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-ui.stat-card label="Low Stock Meds" :value="$metrics['lowStockMedicines']" tone="yellow" variant="solid" icon="medicine" />
            <x-ui.stat-card label="Expired Meds" :value="$metrics['expiredMedicines']" tone="red" variant="solid" icon="alert" :critical="true" />
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-5">
        <div class="space-y-4 lg:col-span-3">
            <x-ui.card title="Vaccination Overview" subtitle="Vaccination appointment analytics">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-blue-600">Total Vaccination Appointments</p>
                        <p class="mt-2 text-2xl font-bold text-blue-700">{{ $vaccinationAnalytics['totalAppointments'] }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-blue-600">Most Common Vaccine</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $vaccinationAnalytics['mostCommonVaccine'] }}</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-blue-600">Vaccination Count This Month</p>
                        <p class="mt-2 text-2xl font-bold text-blue-700">{{ $vaccinationAnalytics['thisMonthCount'] }}</p>
                    </div>
                </div>
                <div class="mt-4 border-t border-blue-100 pt-4">
                    <canvas id="staffVaccinationChart" height="110"></canvas>
                </div>
            </x-ui.card>

            <x-ui.card title="Checkup Overview" subtitle="Checkup appointment analytics">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-pink-100 bg-pink-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-pink-600">Total Checkup Appointments</p>
                        <p class="mt-2 text-2xl font-bold text-pink-600">{{ $checkupAnalytics['totalAppointments'] }}</p>
                    </div>
                    <div class="rounded-xl border border-pink-100 bg-pink-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-pink-600">Most Common Checkup Reason</p>
                        <p class="mt-2 text-sm font-semibold text-slate-700">{{ $checkupAnalytics['mostCommonReason'] }}</p>
                    </div>
                    <div class="rounded-xl border border-pink-100 bg-pink-50 p-4">
                        <p class="text-xs uppercase tracking-[0.15em] text-pink-600">Checkups This Month</p>
                        <p class="mt-2 text-2xl font-bold text-pink-600">{{ $checkupAnalytics['thisMonthCount'] }}</p>
                    </div>
                </div>
                <div class="mt-4 border-t border-pink-100 pt-4">
                    <canvas id="staffCheckupChart" height="110"></canvas>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-4 lg:col-span-2">
            <x-ui.card title="Quick Access">
                <div class="grid grid-cols-1 gap-3">
                    <x-ui.button :href="route('pets.index')">Manage Pets</x-ui.button>
                    <x-ui.button :href="route('appointments.index')" variant="secondary">Manage Appointments</x-ui.button>
                    <x-ui.button :href="route('vaccinations.overdue')" variant="secondary">View Overdue Vaccines</x-ui.button>
                    <x-ui.button :href="route('hospitalizations.index')" variant="secondary">Hospitalizations</x-ui.button>
                    <x-ui.button :href="route('medicines.index')" variant="secondary">Medicines</x-ui.button>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Vaccination Reminders</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase text-amber-700">
                            Due Soon: {{ $metrics['dueSoonVaccinations'] }}
                        </span>
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase text-rose-700">
                            Overdue: {{ $metrics['overdueVaccinations'] }}
                        </span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-base font-bold text-slate-900">Reports</h3>
                    <button
                        type="button"
                        class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 hover:scale-[1.02] hover:bg-blue-700 hover:shadow-lg hover:shadow-pink-100"
                        x-on:click="$dispatch('open-modal', 'print-report-modal')"
                    >
                        Print Report
                    </button>
                </div>
                <p class="mt-2 text-sm text-slate-500">Generate monthly or custom reports for your handled consultations.</p>
                <div class="mt-4">
                    <x-ui.button :href="route('reports.index')" variant="secondary">Open Reports</x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </section>

    <section>
        <x-ui.card title="My Performance" subtitle="Your handled consultations and appointment outcomes.">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-blue-600">Consultations Handled</p>
                    <p class="mt-2 text-2xl font-bold text-blue-700">{{ $staffPerformance['total_consultations'] }}</p>
                </div>
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-blue-600">Vaccinations Administered</p>
                    <p class="mt-2 text-2xl font-bold text-blue-700">{{ $staffPerformance['vaccinations_administered'] }}</p>
                </div>
                <div class="rounded-xl border border-green-100 bg-green-50 p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-green-700">Grooming Handled</p>
                    <p class="mt-2 text-2xl font-bold text-green-700">{{ $staffPerformance['grooming_handled'] }}</p>
                </div>
                <div class="rounded-xl border border-pink-100 bg-pink-50 p-4">
                    <p class="text-xs uppercase tracking-[0.15em] text-pink-600">Completed Appointments</p>
                    <p class="mt-2 text-2xl font-bold text-pink-600">{{ $staffPerformance['completed_appointments'] }}</p>
                </div>
            </div>
        </x-ui.card>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-ui.card title="Active Hospitalizations">
            @if($activeHospitalizationsList->isEmpty())
                <x-ui.empty-state
                    title="No active confinement records."
                    description="Assigned active confinements will appear here."
                />
            @else
                <div class="space-y-2">
                    @foreach($activeHospitalizationsList as $hospitalization)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $hospitalization->pet?->pet_name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $hospitalization->pet?->owner_name ?? 'N/A' }} · Admitted {{ optional($hospitalization->admitted_date)->format('M d, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-4">
                <x-ui.button :href="route('hospitalizations.index')" variant="secondary">Open Hospitalizations</x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card title="Medicine Alerts">
            @if($lowStockMedicinesList->isEmpty())
                <x-ui.empty-state
                    title="No low-stock medicines."
                    description="Inventory alerts will appear when stock reaches threshold."
                />
            @else
                <div class="space-y-2">
                    @foreach($lowStockMedicinesList as $medicine)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $medicine->name }}</p>
                            <p class="text-xs text-amber-700">
                                Stock {{ $medicine->stock_quantity }} / Threshold {{ $medicine->low_stock_threshold }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.1em]">
                <span class="rounded-full bg-amber-100 px-3 py-1 font-semibold text-amber-700">Low Stock: {{ $metrics['lowStockMedicines'] }}</span>
                <span class="rounded-full bg-rose-100 px-3 py-1 font-semibold text-rose-700">Expired: {{ $metrics['expiredMedicines'] }}</span>
            </div>
            <div class="mt-3">
                <x-ui.button :href="route('medicines.index')" variant="secondary">Open Medicines</x-ui.button>
            </div>
        </x-ui.card>
    </section>

    <section class="space-y-5">
        <x-ui.card>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-blue-700">Vaccination Appointments ({{ $vaccinationAppointments->total() }})</h2>
                <x-ui.button :href="route('appointments.index')" variant="secondary">Manage All</x-ui.button>
            </div>
            @if($vaccinationAppointments->isEmpty())
                <x-ui.empty-state
                    title="No vaccination appointments."
                    description="Vaccination requests will appear here once submitted."
                />
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Pet Name</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($vaccinationAppointments as $appointment)
                                <tr class="transition-colors hover:bg-pink-50/60">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $appointment->display_pet_name }}</td>
                                    <td class="px-4 py-3">{{ $appointment->pet?->owner_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">{{ $appointment->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : 'N/A' }}</td>
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
                                        <div class="flex items-center justify-end gap-2">
                                            <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary">Edit</x-ui.button>
                                            @if(in_array($appointment->status, ['pending', 'approved'], true))
                                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                                    @csrf
                                                    <x-ui.button variant="danger" onclick="return confirm('Cancel this appointment?')">Cancel</x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $vaccinationAppointments->links() }}</div>
            @endif
        </x-ui.card>

        <div class="border-t border-pink-100"></div>

        <x-ui.card>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-blue-700">Grooming Appointments ({{ $groomingAppointments->total() }})</h2>
                <x-ui.button :href="route('appointments.index')" variant="secondary">Manage All</x-ui.button>
            </div>
            @if($groomingAppointments->isEmpty())
                <x-ui.empty-state
                    title="No grooming appointments."
                    description="Grooming requests will appear here once submitted."
                />
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.15em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Pet Name</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($groomingAppointments as $appointment)
                                <tr class="transition-colors hover:bg-pink-50/60">
                                    <td class="px-4 py-3 font-semibold text-slate-700">{{ $appointment->display_pet_name }}</td>
                                    <td class="px-4 py-3">{{ $appointment->pet?->owner_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ optional($appointment->appointment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3">{{ $appointment->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : 'N/A' }}</td>
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
                                        <div class="flex items-center justify-end gap-2">
                                            <x-ui.button :href="route('appointments.edit', $appointment)" variant="secondary">Edit</x-ui.button>
                                            @if(in_array($appointment->status, ['pending', 'approved'], true))
                                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                                    @csrf
                                                    <x-ui.button variant="danger" onclick="return confirm('Cancel this appointment?')">Cancel</x-ui.button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $groomingAppointments->links() }}</div>
            @endif
        </x-ui.card>
    </section>

    @include('reports._print_modal', ['modalName' => 'print-report-modal'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        const staffVaccinationChart = document.getElementById('staffVaccinationChart');
        if (staffVaccinationChart) {
            new Chart(staffVaccinationChart, {
                type: 'bar',
                data: {
                    labels: @json($vaccinationChart['labels']),
                    datasets: [
                        {
                            label: 'Vaccination Appointments',
                            data: @json($vaccinationChart['totals']),
                            backgroundColor: '#2563eb',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        const staffCheckupChart = document.getElementById('staffCheckupChart');
        if (staffCheckupChart) {
            new Chart(staffCheckupChart, {
                type: 'line',
                data: {
                    labels: @json($checkupChart['labels']),
                    datasets: [
                        {
                            label: 'Checkup Appointments',
                            data: @json($checkupChart['totals']),
                            borderColor: '#ec4899',
                            backgroundColor: 'rgba(236, 72, 153, 0.12)',
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
</x-app-layout>
