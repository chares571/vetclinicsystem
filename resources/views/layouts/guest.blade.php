<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Vet Clinic System') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php($isLoginPage = request()->routeIs('login'))

        <main class="relative min-h-screen overflow-hidden">
            <img
                src="{{ asset('images/clinic-hero.jpg.png') }}"
                alt="Veterinary clinic background"
                class="absolute inset-0 h-full w-full object-cover"
            >

            @if($isLoginPage)
                <div class="absolute inset-0 bg-gradient-to-r from-blue-700/70 via-teal-500/60 to-pink-500/65 animate-gradient-slow"></div>
                <div class="absolute inset-0 bg-black/55"></div>

                <div class="pointer-events-none absolute inset-0">
                    <svg class="absolute left-[8%] top-[18%] h-10 w-10 text-white opacity-10 animate-float" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9a2 2 0 11-4 0 2 2 0 014 0zm5-2a2 2 0 100-4 2 2 0 000 4zm5 2a2 2 0 11-4 0 2 2 0 014 0zM8.1 14.8c1.9-2 5.9-2 7.8 0 2 2.1 1.8 5.4-.3 6.9-1.4.9-2.7.6-3.6.2-.9-.4-1.7-.4-2.6 0-.9.4-2.2.7-3.6-.2-2.1-1.5-2.3-4.8.3-6.9z" />
                    </svg>

                    <svg class="absolute right-[10%] top-[22%] h-12 w-12 text-white opacity-10 animate-float" style="animation-delay: 0.8s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14M9 21v-4h6v4M10 9h4m-2-2v4" />
                    </svg>

                    <svg class="absolute left-[14%] bottom-[20%] h-10 w-10 text-white opacity-10 animate-float" style="animation-delay: 1.4s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-8 8a6 6 0 00-6 6h20a6 6 0 00-6-6H8z" />
                    </svg>

                    <svg class="absolute right-[15%] bottom-[18%] h-11 w-11 text-white opacity-10 animate-float" style="animation-delay: 2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 3l13 13m-3 3l-6-6m-3 3l-4 4m0 0l-2-2m2 2l2 2m2-2l2-2" />
                    </svg>
                </div>
            @else
                <div class="absolute inset-0 bg-black/60"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-transparent to-emerald-700/35"></div>
            @endif

            <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8 sm:px-6">
                <div class="{{ $isLoginPage ? 'w-full max-w-lg rounded-2xl border border-white/30 bg-white/20 p-8 shadow-2xl backdrop-blur-lg animate-fade-in sm:p-10' : 'w-full max-w-md rounded-2xl bg-white/85 p-8 shadow-2xl backdrop-blur-md animate-fade-in sm:p-10' }}">
                    <a href="{{ route('home') }}" class="group mb-6 block text-center">
                        <span class="mx-auto mb-4 flex h-28 w-28 items-center justify-center rounded-full bg-white shadow-xl">
                            <img
                                src="{{ asset('images/clinic-logo.jpg') }}"
                                alt="NEW CREATION Animal Clinic and Diagnostic Center logo"
                                class="h-20 w-20 object-contain drop-shadow-md transition duration-300 group-hover:scale-105"
                            >
                        </span>
                        <p class="font-outfit text-3xl font-bold tracking-tight {{ $isLoginPage ? 'text-white' : 'text-blue-800' }}">
                            NEW CREATION
                        </p>
                        <p class="mt-1 text-xs font-bold uppercase tracking-[0.16em] {{ $isLoginPage ? 'text-blue-100' : 'text-blue-700' }}">
                            Animal Clinic and Diagnostic Center
                        </p>
                        <p class="mt-2 text-xs tracking-[0.08em] {{ $isLoginPage ? 'text-slate-100/90' : 'text-slate-600' }}">
                            Compassionate Veterinary Care You Can Trust
                        </p>
                    </a>

                    {{ $slot }}
                </div>
            </div>
        </main>

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
