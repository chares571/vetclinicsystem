<x-app-layout>
    <x-ui.card title="Create Appointment" subtitle="Schedule a new clinic visit.">
        @include('appointments._form', [
            'action' => route('appointments.store'),
            'method' => 'POST',
            'appointment' => null,
            'pets' => $pets,
            'appointmentType' => $appointmentType,
            'allowTypeSelection' => $allowTypeSelection ?? false,
        ])
    </x-ui.card>
</x-app-layout>
