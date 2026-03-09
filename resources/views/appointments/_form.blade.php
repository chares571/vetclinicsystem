@props([
    'appointment' => null,
    'pets' => collect(),
    'appointmentType' => null,
    'allowTypeSelection' => false,
    'action',
    'method' => 'POST',
])

@php
    $user = auth()->user();
    $isClient = $user?->isClient();
    $type = old('type', $appointment?->type ?? $appointmentType ?? \App\Models\Appointment::TYPE_VACCINATION);

    $showTypeSelection = $allowTypeSelection && ! $appointment;
    $typeOptions = [
        \App\Models\Appointment::TYPE_VACCINATION => 'Vaccination Appointment',
        \App\Models\Appointment::TYPE_CHECKUP => 'Checkup Appointment',
    ];

    $isVaccination = $type === \App\Models\Appointment::TYPE_VACCINATION;
    $isCheckup = $type === \App\Models\Appointment::TYPE_CHECKUP;
    $isGrooming = $type === \App\Models\Appointment::TYPE_GROOMING;
    $groomingServices = \App\Models\Appointment::GROOMING_SERVICE_LABELS;
    $vaccineOptions = \App\Models\Appointment::VACCINE_PURPOSE_OPTIONS;

    $appointmentPurpose = old('purpose', $appointment?->purpose);
    $vaccinationPurpose = old('vaccination_purpose');
    if ($vaccinationPurpose === null) {
        if ($isVaccination && filled($appointmentPurpose)) {
            $vaccinationPurpose = in_array($appointmentPurpose, $vaccineOptions, true) ? $appointmentPurpose : 'Others';
        } else {
            $vaccinationPurpose = '';
        }
    }
    $otherVaccine = old('other_vaccine');
    if ($otherVaccine === null && $isVaccination && filled($appointmentPurpose) && ! in_array($appointmentPurpose, $vaccineOptions, true)) {
        $otherVaccine = $appointmentPurpose;
    }
    $checkupPurpose = old('checkup_purpose', $isCheckup ? $appointmentPurpose : null);

    $clientVisibleStatuses = [
        \App\Models\Appointment::STATUS_PENDING,
        \App\Models\Appointment::STATUS_APPROVED,
        \App\Models\Appointment::STATUS_COMPLETED,
        \App\Models\Appointment::STATUS_REJECTED,
    ];
    $staffStatuses = [
        \App\Models\Appointment::STATUS_PENDING,
        \App\Models\Appointment::STATUS_APPROVED,
        \App\Models\Appointment::STATUS_COMPLETED,
        \App\Models\Appointment::STATUS_REJECTED,
        \App\Models\Appointment::STATUS_CANCELLED,
    ];
    $currentStatus = old('status', $appointment?->status ?? \App\Models\Appointment::STATUS_PENDING);
    if ($currentStatus === 'confirmed') {
        $currentStatus = \App\Models\Appointment::STATUS_APPROVED;
    }
    $isEmergency = (bool) old('is_emergency', (bool) ($appointment?->is_emergency ?? false));
    $preferredTime = old('preferred_time', $appointment?->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : null);
    $isStaffWalkInMode = ! $isClient && ! $appointment;
@endphp

<form id="appointment-form" method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @if($showTypeSelection)
            <div class="md:col-span-2">
                <x-input-label for="appointment_type" :value="__('Appointment Category')" />
                <select id="appointment_type" name="type" class="mt-1 block w-full rounded-md border-slate-300" required>
                    @foreach($typeOptions as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}" @selected($type === $typeKey)>{{ $typeLabel }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>
        @else
            <input type="hidden" name="type" value="{{ $type }}">
        @endif

        @if($isStaffWalkInMode)
            <div>
                <x-input-label for="pet_name" :value="__('Pet Name')" />
                <x-text-input
                    id="pet_name"
                    name="pet_name"
                    class="mt-1 block w-full"
                    :value="old('pet_name')"
                    placeholder="Enter pet name (walk-in patient)"
                    required
                />
                <x-input-error :messages="$errors->get('pet_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="owner_name" :value="__('Owner Name')" />
                <x-text-input
                    id="owner_name"
                    name="owner_name"
                    class="mt-1 block w-full"
                    :value="old('owner_name')"
                    placeholder="Pet owner name"
                    required
                />
                <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_number" :value="__('Contact Number')" />
                <x-text-input
                    id="contact_number"
                    name="contact_number"
                    class="mt-1 block w-full"
                    :value="old('contact_number')"
                    placeholder="Owner contact number"
                    required
                />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="pet_id" :value="__('Pet')" />
                <select id="pet_id" name="pet_id" class="mt-1 block w-full rounded-md border-slate-300">
                    <option value="">Select Pet</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" @selected((string) old('pet_id', $appointment?->pet_id) === (string) $pet->id)>
                            {{ $pet->pet_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('pet_id')" class="mt-2" />
            </div>
        @endif

        <div>
            <x-input-label id="appointment_date_label" for="appointment_date" :value="$isGrooming ? __('Preferred Date') : __('Appointment Date')" />
            <x-text-input id="appointment_date" name="appointment_date" type="date" class="mt-1 block w-full" :value="old('appointment_date', optional($appointment?->appointment_date)->toDateString())" required />
            <x-input-error :messages="$errors->get('appointment_date')" class="mt-2" />
        </div>

        <div id="vaccination-fields" class="md:col-span-2 {{ $isVaccination ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="vaccination_purpose" :value="__('Vaccine Purpose')" />
                    <select id="vaccination_purpose" name="vaccination_purpose" class="mt-1 block w-full rounded-md border-slate-300">
                        <option value="">Select Vaccine</option>
                        @foreach($vaccineOptions as $vaccine)
                            <option value="{{ $vaccine }}" @selected($vaccinationPurpose === $vaccine)>{{ $vaccine }}</option>
                        @endforeach
                        <option value="Others" @selected($vaccinationPurpose === 'Others')>Others</option>
                    </select>
                    <x-input-error :messages="$errors->get('vaccination_purpose')" class="mt-2" />
                </div>

                <div id="other-vaccine-wrapper" class="{{ $vaccinationPurpose === 'Others' ? '' : 'hidden' }}">
                    <x-input-label for="other_vaccine" :value="__('Other Vaccine Name')" />
                    <x-text-input id="other_vaccine" name="other_vaccine" class="mt-1 block w-full" :value="$otherVaccine" />
                    <x-input-error :messages="$errors->get('other_vaccine')" class="mt-2" />
                </div>
            </div>
        </div>

        <div id="checkup-fields" class="md:col-span-2 {{ $isCheckup ? '' : 'hidden' }}">
            <x-input-label for="checkup_purpose" :value="__('Checkup Reason')" />
            <x-text-input
                id="checkup_purpose"
                name="checkup_purpose"
                class="mt-1 block w-full"
                :value="$checkupPurpose"
                placeholder="Enter reason for checkup (e.g., skin allergy, follow-up visit)"
            />
            <x-input-error :messages="$errors->get('checkup_purpose')" class="mt-2" />
        </div>

        <div id="grooming-fields" class="contents {{ $isGrooming ? '' : 'hidden' }}">
            <div>
                <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                <x-text-input id="preferred_time" name="preferred_time" type="time" class="mt-1 block w-full" :value="$preferredTime" />
                <x-input-error :messages="$errors->get('preferred_time')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="grooming_service_type" :value="__('Grooming Service Type')" />
                <select id="grooming_service_type" name="grooming_service_type" class="mt-1 block w-full rounded-md border-slate-300">
                    <option value="">Select Service</option>
                    @foreach($groomingServices as $serviceKey => $serviceLabel)
                        <option value="{{ $serviceKey }}" @selected(old('grooming_service_type', $appointment?->grooming_service_type) === $serviceKey)>
                            {{ $serviceLabel }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('grooming_service_type')" class="mt-2" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="notes" :value="__('Notes (Optional)')" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('notes', $appointment?->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
        </div>

        @if($isClient)
            @if($appointment)
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" class="mt-1 block w-full rounded-md border-slate-300 bg-slate-100" disabled>
                        @foreach($clientVisibleStatuses as $status)
                            <option value="{{ $status }}" @selected($currentStatus === $status)>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            @else
                <input type="hidden" name="status" value="{{ \App\Models\Appointment::STATUS_PENDING }}">
            @endif
        @else
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-300">
                    @foreach($staffStatuses as $status)
                        <option value="{{ $status }}" @selected($currentStatus === $status)>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="is_emergency" :value="__('Emergency Flag')" />
                <label for="is_emergency" class="mt-1 flex items-center gap-2 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    <input
                        id="is_emergency"
                        name="is_emergency"
                        type="checkbox"
                        value="1"
                        class="rounded border-rose-300 text-rose-600 focus:ring-rose-500"
                        @checked($isEmergency)
                    >
                    <span>Mark as emergency appointment</span>
                </label>
                <x-input-error :messages="$errors->get('is_emergency')" class="mt-2" />
            </div>
        @endif
    </div>

    <div class="flex items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('appointments.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>

<script>
    (() => {
        const form = document.getElementById('appointment-form');
        if (!form) return;

        const typeSelect = form.querySelector('#appointment_type');
        const hiddenTypeInput = form.querySelector('input[name="type"]');
        const appointmentDateLabel = form.querySelector('#appointment_date_label');
        const vaccinationFields = form.querySelector('#vaccination-fields');
        const checkupFields = form.querySelector('#checkup-fields');
        const groomingFields = form.querySelector('#grooming-fields');
        const vaccinationPurposeField = form.querySelector('#vaccination_purpose');
        const otherVaccineWrapper = form.querySelector('#other-vaccine-wrapper');
        const otherVaccineField = form.querySelector('#other_vaccine');
        const checkupPurposeField = form.querySelector('#checkup_purpose');
        const preferredTimeField = form.querySelector('#preferred_time');
        const groomingServiceField = form.querySelector('#grooming_service_type');

        const getType = () => {
            if (typeSelect) return typeSelect.value;
            if (hiddenTypeInput) return hiddenTypeInput.value;

            return '{{ \App\Models\Appointment::TYPE_VACCINATION }}';
        };

        const toggle = (el, show) => {
            if (!el) return;
            el.classList.toggle('hidden', !show);
        };

        const setRequired = (el, required) => {
            if (!el) return;
            if (required) {
                el.setAttribute('required', 'required');
                return;
            }

            el.removeAttribute('required');
        };

        const syncFields = () => {
            const type = getType();
            const isVaccinationType = type === '{{ \App\Models\Appointment::TYPE_VACCINATION }}';
            const isCheckupType = type === '{{ \App\Models\Appointment::TYPE_CHECKUP }}';
            const isGroomingType = type === '{{ \App\Models\Appointment::TYPE_GROOMING }}';

            toggle(vaccinationFields, isVaccinationType);
            toggle(checkupFields, isCheckupType);
            toggle(groomingFields, isGroomingType);

            if (appointmentDateLabel) {
                appointmentDateLabel.textContent = isGroomingType ? 'Preferred Date' : 'Appointment Date';
            }

            setRequired(vaccinationPurposeField, isVaccinationType);
            setRequired(checkupPurposeField, isCheckupType);
            setRequired(preferredTimeField, isGroomingType);
            setRequired(groomingServiceField, isGroomingType);

            const showOtherVaccine = isVaccinationType && vaccinationPurposeField?.value === 'Others';
            toggle(otherVaccineWrapper, showOtherVaccine);
            setRequired(otherVaccineField, showOtherVaccine);
        };

        typeSelect?.addEventListener('change', syncFields);
        vaccinationPurposeField?.addEventListener('change', syncFields);

        syncFields();
    })();
</script>
