<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-5 text-center">
        <p class="text-sm tracking-wide text-white/90">Sign in to continue managing patient records and appointments.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" class="!text-white/90" :value="__('Email')" />
            <div class="mt-1 flex items-center rounded-lg border border-white/40 bg-white/70 px-4 py-3 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 6 9-6m-18 10h18a2 2 0 002-2V9a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                <input
                    id="email"
                    class="w-full border-0 bg-transparent p-0 text-slate-800 outline-none ring-0 placeholder:text-slate-400 focus:ring-0"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" class="!text-white/90" :value="__('Password')" />
            <div class="mt-1 flex items-center rounded-lg border border-white/40 bg-white/70 px-4 py-3 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2h-1V9a5 5 0 00-10 0v2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
                <input
                    id="password"
                    class="w-full border-0 bg-transparent p-0 text-slate-800 outline-none ring-0 placeholder:text-slate-400 focus:ring-0"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                >
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-white/60 bg-white/80 text-blue-600 shadow-sm focus:ring-blue-400" name="remember">
                <span class="ms-2 text-sm text-white/90">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-center">
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-100 underline underline-offset-2 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-300" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            @if (Route::has('register'))
                <a class="text-sm font-medium text-blue-100 underline underline-offset-2 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-300" href="{{ route('register') }}">
                    {{ __('Create client account') }}
                </a>
            @endif
        </div>

        <div class="flex justify-center pt-2">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-10 py-3 text-sm font-semibold uppercase tracking-[0.1em] text-white shadow-lg shadow-blue-900/30 transition duration-200 hover:scale-105 hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-900/45 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-0"
            >
                Log In
            </button>
        </div>
    </form>
</x-guest-layout>
