@props([
    'report' => null,
    'action',
    'method' => 'POST',
])

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(!in_array($method, ['POST', 'GET'], true))
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $report?->title)" required />
            <x-input-error :messages="$errors->get('title')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="scope" :value="__('Scope')" />
            <select id="scope" name="scope" class="mt-1 block w-full rounded-md border-slate-300">
                @foreach(['daily', 'weekly', 'monthly', 'custom'] as $scope)
                    <option value="{{ $scope }}" @selected(old('scope', $report?->scope ?? 'monthly') === $scope)>
                        {{ ucfirst($scope) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('scope')" class="mt-2" />
        </div>

        <div></div>

        <div>
            <x-input-label for="starts_on" :value="__('Start Date')" />
            <x-text-input id="starts_on" name="starts_on" type="date" class="mt-1 block w-full" :value="old('starts_on', optional($report?->starts_on)->toDateString())" />
            <x-input-error :messages="$errors->get('starts_on')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="ends_on" :value="__('End Date')" />
            <x-text-input id="ends_on" name="ends_on" type="date" class="mt-1 block w-full" :value="old('ends_on', optional($report?->ends_on)->toDateString())" />
            <x-input-error :messages="$errors->get('ends_on')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="summary" :value="__('Summary')" />
            <textarea id="summary" name="summary" rows="5" class="mt-1 block w-full rounded-md border-slate-300">{{ old('summary', $report?->summary) }}</textarea>
            <x-input-error :messages="$errors->get('summary')" class="mt-2" />
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <x-ui.button type="submit">Save</x-ui.button>
        <x-ui.button :href="route('reports.index')" variant="secondary">Cancel</x-ui.button>
    </div>
</form>
