<x-app-layout>
    @php
        $pet = $vaccination->pet;
        $petName = $vaccination->display_pet_name;
        $today = today();
        $dueDate = $vaccination->next_due_date;
        $isDueSoon = $dueDate && ! $isOverdue && $dueDate->isBetween($today, $today->copy()->addDays(7));
        $statusLabel = $isOverdue ? 'OVERDUE' : ($isDueSoon ? 'DUE SOON' : 'ON SCHEDULE');
        $statusBadgeClass = $isOverdue
            ? 'bg-rose-100 text-rose-700'
            : ($isDueSoon ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
        $ownerName = $vaccination->display_owner_name;
        $ownerContactNumber = $vaccination->display_contact_number;
        $ownerAddress = $pet?->owner?->address ?? null;
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Vaccination Details</h1>
            <p class="text-sm text-slate-500">Review pet and owner information, then notify the owner when follow-up is needed.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button :href="route('vaccinations.edit', $vaccination)" variant="secondary">Edit</x-ui.button>
            <x-ui.button :href="route('vaccinations.index')" variant="secondary">Back to Vaccinations</x-ui.button>
        </div>
    </div>

    @if($isOverdue)
        <x-ui.alert type="error">
            This vaccination is overdue. Notify the pet owner immediately.
        </x-ui.alert>
    @endif

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-ui.card title="Pet Information">
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Pet Name</dt>
                    <dd class="text-slate-800">{{ $petName }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Species</dt>
                    <dd class="text-slate-800">{{ $pet?->species ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Breed</dt>
                    <dd class="text-slate-800">{{ $pet?->breed ?: 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Sex</dt>
                    <dd class="text-slate-800">{{ $pet?->sex ? ucfirst($pet->sex) : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Age</dt>
                    <dd class="text-slate-800">{{ $pet?->display_age ?? 'N/A' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Owner Information">
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Owner Name</dt>
                    <dd class="text-slate-800">{{ $ownerName }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Contact Number</dt>
                    <dd class="text-slate-800">{{ $ownerContactNumber }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Address</dt>
                    <dd class="text-slate-800">{{ $ownerAddress ?: 'N/A' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Vaccination Information">
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Vaccine Name</dt>
                    <dd class="text-slate-800">{{ $vaccination->vaccine_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Date Given</dt>
                    <dd class="text-slate-800">{{ optional($vaccination->date_given)->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Next Due Date</dt>
                    <dd class="text-slate-800">{{ optional($dueDate)->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Status</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase {{ $statusBadgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </dd>
                </div>
            </dl>
        </x-ui.card>
    </section>

    <x-ui.card title="Notify Pet Owner" subtitle="Use quick actions to send a reminder for overdue or due-soon vaccines.">
        @if($smsLink || $callLink)
            <div class="space-y-3">
                <div>
                    <x-input-label for="owner-reminder-message" :value="__('Reminder Message')" />
                    <textarea
                        id="owner-reminder-message"
                        class="mt-1 block w-full rounded-md border-slate-300 bg-slate-50 text-sm text-slate-700"
                        rows="9"
                        readonly
                    >{{ $reminderMessage }}</textarea>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if($smsLink)
                        <x-ui.button :href="$smsLink">Notify Owner</x-ui.button>
                        <x-ui.button :href="$smsLink" variant="secondary">Send SMS</x-ui.button>
                    @endif
                    <x-ui.button type="button" id="copy-reminder-message" variant="secondary">Copy Message</x-ui.button>
                    @if($callLink)
                        <x-ui.button :href="$callLink" variant="secondary">Call Owner</x-ui.button>
                    @endif
                </div>

                <p id="copy-reminder-feedback" class="hidden text-xs font-semibold uppercase tracking-[0.08em] text-emerald-700">
                    Reminder message copied.
                </p>
            </div>
        @else
            <x-ui.alert type="warning">
                No contact number is saved for this vaccination record yet.
            </x-ui.alert>
        @endif
    </x-ui.card>

    <script>
        (() => {
            const copyButton = document.getElementById('copy-reminder-message');
            const messageField = document.getElementById('owner-reminder-message');
            const feedback = document.getElementById('copy-reminder-feedback');

            if (!copyButton || !messageField) return;

            const showFeedback = () => {
                if (!feedback) return;
                feedback.classList.remove('hidden');
                window.setTimeout(() => feedback.classList.add('hidden'), 1800);
            };

            const fallbackCopy = (text) => {
                const tempTextArea = document.createElement('textarea');
                tempTextArea.value = text;
                tempTextArea.style.position = 'fixed';
                tempTextArea.style.opacity = '0';
                document.body.appendChild(tempTextArea);
                tempTextArea.focus();
                tempTextArea.select();
                document.execCommand('copy');
                tempTextArea.remove();
            };

            copyButton.addEventListener('click', async () => {
                const message = messageField.value;
                try {
                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(message);
                    } else {
                        fallbackCopy(message);
                    }
                    showFeedback();
                } catch (error) {
                    fallbackCopy(message);
                    showFeedback();
                }
            });
        })();
    </script>
</x-app-layout>
