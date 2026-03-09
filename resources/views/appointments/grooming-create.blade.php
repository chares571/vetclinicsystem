<x-app-layout>
    <x-ui.card title="Request Grooming Appointment" subtitle="Book a grooming service for your pet.">
        @include('appointments._form', [
            'action' => route('appointments.store'),
            'method' => 'POST',
            'appointment' => null,
            'pets' => $pets,
            'appointmentType' => $appointmentType ?? \App\Models\Appointment::TYPE_GROOMING,
            'allowTypeSelection' => false,
        ])
    </x-ui.card>
</x-app-layout>
