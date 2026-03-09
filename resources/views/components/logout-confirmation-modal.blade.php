@props([
    'name' => 'confirm-logout',
])

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') { show = true }"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') { show = false }"
    x-on:keydown.escape.window="show = false"
    x-effect="document.body.classList.toggle('overflow-hidden', show)"
>
    <div
        x-show="show"
        class="fixed inset-0 z-[70] flex items-center justify-center p-4"
        style="display: none;"
        aria-labelledby="logout-modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
            x-on:click="show = false"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <div
            class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <h2 id="logout-modal-title" class="text-lg font-bold text-slate-900">Confirm Logout</h2>
            <p class="mt-2 text-sm text-slate-600">Are you sure you want to log out?</p>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    x-on:click="show = false"
                >
                    Cancel
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Confirm Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
