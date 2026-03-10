<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') === 'Laravel' ? 'NEW CREATION Animal Clinic and Diagnostic Center' : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/clinic-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/clinic-logo.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft-pattern text-slate-800 antialiased">
@auth
    @php
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $isStaff = $user->isVeterinaryStaff();
        $dashboardRoute = $isAdmin
            ? 'admin.dashboard'
            : ($isStaff ? 'staff.dashboard' : 'client.dashboard');
        $appointmentsNavActive = request()->routeIs('appointments.*') && ! request()->routeIs('appointments.completed');
        $completedAppointmentsNavActive = request()->routeIs('appointments.completed');
    @endphp

    <div x-data="{ sidebarOpen: false }" class="relative min-h-screen">
        <div
            class="fixed inset-0 z-40 bg-slate-900/55 backdrop-blur-sm md:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 overflow-y-auto border-r border-blue-100 bg-white/95 px-5 py-6 shadow-xl transition-transform duration-300 md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-blue-600 via-blue-500 to-pink-400 text-sm font-bold text-white shadow-md">
                    VC
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-500">Portal</p>
                    <p class="text-base font-bold text-slate-900">Vet Clinic</p>
                </div>
            </div>

            <nav class="mt-8 space-y-2 text-sm font-semibold">
                <a href="{{ route($dashboardRoute) }}"
                   class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('admin.dashboard') || request()->routeIs('staff.dashboard') || request()->routeIs('client.dashboard') || request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                    Dashboard
                </a>

                <a href="{{ route('pets.index') }}"
                   class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('pets.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                    Pets
                </a>

                <a href="{{ route('appointments.index') }}"
                   class="flex items-center rounded-xl px-3 py-2 transition {{ $appointmentsNavActive ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                    <span>Appointments</span>
                    @if(($isAdmin || $isStaff) && ($pendingAppointments ?? 0) > 0)
                        <span class="badge bg-danger ml-auto inline-flex min-w-6 items-center justify-center rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold leading-none text-white">
                            {{ $pendingAppointments }}
                        </span>
                    @endif
                </a>

                @if($isAdmin || $isStaff)
                    <a href="{{ route('appointments.completed') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ $completedAppointmentsNavActive ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        Completed Appointments
                    </a>
                @endif

                <a href="{{ route('vaccinations.index') }}"
                   class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('vaccinations.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                    Vaccinations
                </a>

                @if($isAdmin || $isStaff)
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        Reports
                    </a>

                    <a href="{{ route('hospitalizations.index') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('hospitalizations.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        Hospitalizations
                    </a>

                    <a href="{{ route('medicines.index') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('medicines.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        Medicines
                    </a>

                    <a href="{{ route('announcements.index') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('announcements.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        Announcements
                    </a>
                @endif

                @if($isAdmin)
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        User Management
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="flex items-center rounded-xl px-3 py-2 transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-600 hover:bg-pink-50 hover:text-blue-700' }}">
                        Audit Trail
                    </a>
                @endif
            </nav>

            <div class="mt-10 rounded-2xl border border-pink-100 bg-pink-50/75 p-4">
                <p class="text-xs uppercase tracking-[0.2em] text-blue-500">Signed in as</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                <p class="text-xs uppercase text-slate-500">{{ str_replace('_', ' ', $user->role) }}</p>
            </div>
        </aside>

        <div class="md:pl-72">
            <header class="sticky top-0 z-30 border-b border-blue-100 bg-white/85 backdrop-blur">
                <div class="flex min-h-16 flex-wrap items-center justify-between gap-2 px-4 py-2 md:h-16 md:flex-nowrap md:py-0 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-lg border border-blue-100 bg-white px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm md:hidden"
                        @click="sidebarOpen = true"
                    >
                        Menu
                    </button>

                    <div class="hidden md:block">
                        <p class="text-sm font-medium text-slate-500">Veterinary Clinic Management System</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <a href="{{ route('profile.edit') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-700 sm:text-sm">
                            Profile
                        </a>
                        <button
                            type="button"
                            class="rounded-lg bg-blue-600 px-2.5 py-2 text-xs font-semibold text-white hover:bg-blue-700 sm:px-3 sm:text-sm"
                            x-on:click="$dispatch('open-modal', 'confirm-logout')"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl space-y-4 animate-fade-in">
                    @isset($header)
                        <div class="rounded-2xl border border-blue-100 bg-white/90 px-5 py-4 shadow-sm animate-rise">
                            {{ $header }}
                        </div>
                    @endisset

                    @if (session('success'))
                        <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
                    @endif

                    @if (session('error'))
                        <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
                    @endif

                    @if ($errors->any())
                        <x-ui.alert type="error">
                            Please review the highlighted form errors and try again.
                        </x-ui.alert>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-logout-confirmation-modal />
    </div>
@else
    <main>
        {{ $slot }}
    </main>
@endauth

<script>
    (() => {
        const submissionLockAttribute = 'data-submission-locked';
        const navigationLockAttribute = 'data-navigation-locked';
        const navigationFallbackUnlockMs = 9000;
        let navigationLocked = false;
        let navigationUnlockTimer = null;

        const toggleSubmitButtons = (form, disabled) => {
            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                button.disabled = disabled;
                button.classList.toggle('cursor-not-allowed', disabled);
                button.classList.toggle('opacity-60', disabled);
            });
        };

        const isPrimaryActivation = (event) => {
            const isPointerEvent = typeof PointerEvent !== 'undefined' && event instanceof PointerEvent;
            if (event instanceof MouseEvent || isPointerEvent) {
                return event.button === 0;
            }

            return true;
        };

        const shouldIgnoreNavigationClick = (anchor, event) => {
            if (event.defaultPrevented || !isPrimaryActivation(event)) {
                return true;
            }

            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return true;
            }

            if (anchor.dataset.allowDuplicates === 'true') {
                return true;
            }

            if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
                return true;
            }

            const href = anchor.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return true;
            }

            const url = new URL(anchor.href, window.location.href);
            if (url.href === window.location.href) {
                return true;
            }

            return url.origin !== window.location.origin;
        };

        const unlockNavigation = () => {
            if (navigationUnlockTimer !== null) {
                window.clearTimeout(navigationUnlockTimer);
                navigationUnlockTimer = null;
            }

            navigationLocked = false;
            document.documentElement.classList.remove('cursor-wait');

            document.querySelectorAll(`a[${navigationLockAttribute}]`).forEach((anchor) => {
                anchor.removeAttribute(navigationLockAttribute);
                anchor.removeAttribute('aria-disabled');
                anchor.classList.remove('pointer-events-none', 'opacity-60', 'cursor-wait');
            });
        };

        document.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            const anchor = target.closest('a[href]');
            if (!(anchor instanceof HTMLAnchorElement)) {
                return;
            }

            if (shouldIgnoreNavigationClick(anchor, event)) {
                return;
            }

            if (navigationLocked) {
                event.preventDefault();
                return;
            }

            navigationLocked = true;
            anchor.setAttribute(navigationLockAttribute, 'true');
            anchor.setAttribute('aria-disabled', 'true');
            anchor.classList.add('pointer-events-none', 'opacity-60', 'cursor-wait');
            document.documentElement.classList.add('cursor-wait');

            navigationUnlockTimer = window.setTimeout(() => {
                unlockNavigation();
            }, navigationFallbackUnlockMs);

            event.preventDefault();
            window.location.assign(anchor.href);
        }, true);

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;

            if (form.method.toLowerCase() === 'get' || form.dataset.allowDuplicates === 'true') {
                return;
            }

            if (form.hasAttribute(submissionLockAttribute)) {
                event.preventDefault();
                return;
            }

            form.setAttribute(submissionLockAttribute, 'true');
            toggleSubmitButtons(form, true);
        }, true);

        window.addEventListener('pageshow', (event) => {
            if (!event.persisted) return;

            document.querySelectorAll(`form[${submissionLockAttribute}]`).forEach((form) => {
                form.removeAttribute(submissionLockAttribute);
                toggleSubmitButtons(form, false);
            });

            unlockNavigation();
        });

        window.addEventListener('pagehide', () => {
            unlockNavigation();
        });
    })();
</script>

</body>
</html>
