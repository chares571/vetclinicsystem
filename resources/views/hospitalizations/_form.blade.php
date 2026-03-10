@props([
    'hospitalization' => null,
    'action',
    'method' => 'POST',
])

@php
    $status = old('status', $hospitalization?->status ?? \App\Models\Hospitalization::STATUS_ACTIVE);
    $petName = old('pet_name', $hospitalization?->pet?->pet_name);
    $ownerName = old('owner_name', $hospitalization?->pet?->owner_name);
@endphp

<form id="hospitalization-form" method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="pet_name" :value="__('Pet Name')" />
            <x-text-input
                id="pet_name"
                name="pet_name"
                class="mt-1 block w-full"
                :value="$petName"
                placeholder="Enter Pet Name"
                required
            />
            <x-input-error :messages="$errors->get('pet_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="owner_name" :value="__('Pet Owner Name')" />
            <x-text-input
                id="owner_name"
                name="owner_name"
                class="mt-1 block w-full"
                :value="$ownerName"
                placeholder="Enter Pet Owner Name"
                required
            />
            <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-300">
                <option value="{{ \App\Models\Hospitalization::STATUS_ACTIVE }}" @selected($status === \App\Models\Hospitalization::STATUS_ACTIVE)>Active</option>
                <option value="{{ \App\Models\Hospitalization::STATUS_DISCHARGED }}" @selected($status === \App\Models\Hospitalization::STATUS_DISCHARGED)>Discharged</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="admitted_date" :value="__('Admitted Date')" />
            <x-text-input id="admitted_date" name="admitted_date" type="date" class="mt-1 block w-full" :value="old('admitted_date', optional($hospitalization?->admitted_date)->toDateString())" required />
            <x-input-error :messages="$errors->get('admitted_date')" class="mt-2" />
        </div>

        <div id="discharge-date-wrapper" class="{{ $status === \App\Models\Hospitalization::STATUS_DISCHARGED ? '' : 'hidden' }}">
            <x-input-label for="discharge_date" :value="__('Discharge Date')" />
            <x-text-input id="discharge_date" name="discharge_date" type="date" class="mt-1 block w-full" :value="old('discharge_date', optional($hospitalization?->discharge_date)->toDateString())" />
            <x-input-error :messages="$errors->get('discharge_date')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="notes" :value="__('Admission Notes')" />
            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('notes', $hospitalization?->notes) }}</textarea>
            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="medication_schedule" :value="__('Medication Schedule')" />
            <textarea id="medication_schedule" name="medication_schedule" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('medication_schedule', $hospitalization?->medication_schedule) }}</textarea>
            <x-input-error :messages="$errors->get('medication_schedule')" class="mt-2" />
        </div>

        <div id="discharge-summary-wrapper" class="md:col-span-2 {{ $status === \App\Models\Hospitalization::STATUS_DISCHARGED ? '' : 'hidden' }}">
            <x-input-label for="discharge_summary" :value="__('Discharge Summary')" />
            <textarea id="discharge_summary" name="discharge_summary" rows="3" class="mt-1 block w-full rounded-md border-slate-300">{{ old('discharge_summary', $hospitalization?->discharge_summary) }}</textarea>
            <x-input-error :messages="$errors->get('discharge_summary')" class="mt-2" />
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('hospitalizations.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>

<script>
    (() => {
        const form = document.getElementById('hospitalization-form');
        if (!form) return;

        const statusField = form.querySelector('#status');
        const dischargeDateWrapper = form.querySelector('#discharge-date-wrapper');
        const dischargeSummaryWrapper = form.querySelector('#discharge-summary-wrapper');
        const dischargeDateField = form.querySelector('#discharge_date');

        const syncDischargeFields = () => {
            const isDischarged = statusField?.value === '{{ \App\Models\Hospitalization::STATUS_DISCHARGED }}';
            dischargeDateWrapper?.classList.toggle('hidden', !isDischarged);
            dischargeSummaryWrapper?.classList.toggle('hidden', !isDischarged);

            if (isDischarged) {
                dischargeDateField?.setAttribute('required', 'required');
            } else {
                dischargeDateField?.removeAttribute('required');
            }
        };

        statusField?.addEventListener('change', syncDischargeFields);
        syncDischargeFields();
    })();
</script>
