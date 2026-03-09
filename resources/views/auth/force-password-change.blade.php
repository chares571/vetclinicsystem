<x-guest-layout>
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900">Change Default Password</h1>
        <p class="mt-2 text-sm text-slate-600">
            For security, you must update your default password before continuing.
        </p>

        <form method="POST" action="{{ route('password.force.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="current_password" :value="__('Current Password')" />
                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('New Password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
            </div>

            <x-primary-button class="w-full justify-center">
                Update Password
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
