<x-app-layout>
    <x-ui.card title="Edit Appointment" subtitle="Update date, status, and purpose.">
        @include('appointments._form', [
            'action' => route('appointments.update', $appointment),
            'method' => 'PUT',
            'appointment' => $appointment,
            'pets' => $pets,
            'appointmentType' => $appointmentType ?? ($appointment->type ?? \App\Models\Appointment::TYPE_VACCINATION),
            'allowTypeSelection' => false,
        ])
    </x-ui.card>
</x-app-layout>
