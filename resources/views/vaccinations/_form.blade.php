@props([
    'vaccination' => null,
    'action',
    'method' => 'POST',
])

<form id="vaccination-form" method="POST" action="{{ $action }}" class="space-y-4">
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
                :value="old('pet_name', $vaccination?->pet_name ?? $vaccination?->pet?->pet_name)"
                placeholder="Enter Pet Name"
                required
            />
            <x-input-error :messages="$errors->get('pet_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="vaccine_name" :value="__('Vaccine Name')" />
            <x-text-input id="vaccine_name" name="vaccine_name" class="mt-1 block w-full" :value="old('vaccine_name', $vaccination?->vaccine_name)" required />
            <x-input-error :messages="$errors->get('vaccine_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="owner_name" :value="__('Owner Name')" />
            <x-text-input
                id="owner_name"
                name="owner_name"
                class="mt-1 block w-full"
                :value="old('owner_name', $vaccination?->owner_name ?? $vaccination?->pet?->owner_name)"
                placeholder="Enter Owner Name"
            />
            <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="contact_number" :value="__('Contact Number')" />
            <x-text-input
                id="contact_number"
                name="contact_number"
                class="mt-1 block w-full"
                :value="old('contact_number', $vaccination?->contact_number ?? $vaccination?->pet?->contact_number)"
                placeholder="Enter Contact Number"
            />
            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="date_given" :value="__('Date Given')" />
            <x-text-input id="date_given" name="date_given" type="date" class="mt-1 block w-full" :value="old('date_given', optional($vaccination?->date_given)->toDateString())" required />
            <x-input-error :messages="$errors->get('date_given')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="next_due_date" :value="__('Next Due Date')" />
            <x-text-input id="next_due_date" name="next_due_date" type="date" class="mt-1 block w-full bg-slate-100" :value="old('next_due_date', optional($vaccination?->next_due_date)->toDateString())" readonly />
            <p class="mt-1 text-xs text-slate-500">Auto-calculated based on vaccine type and date given.</p>
            <x-input-error :messages="$errors->get('next_due_date')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('vaccinations.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>

<script>
    (() => {
        const form = document.getElementById('vaccination-form');
        if (!form) return;

        const dateGivenField = form.querySelector('#date_given');
        const nextDueField = form.querySelector('#next_due_date');

        const syncNextDueDate = () => {
            if (!dateGivenField || !nextDueField || !dateGivenField.value) {
                return;
            }

            const baseDate = new Date(dateGivenField.value + 'T00:00:00');
            if (Number.isNaN(baseDate.getTime())) {
                return;
            }

            const nextDate = new Date(baseDate);
            nextDate.setFullYear(nextDate.getFullYear() + 1);
            nextDueField.value = nextDate.toISOString().slice(0, 10);
        };

        dateGivenField?.addEventListener('change', syncNextDueDate);
        syncNextDueDate();
    })();
</script>
