{{-- Apply cozy theme card container --}}
<div class="p-6 bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light shadow-md rounded-lg">
    <header>
         {{-- Update header text color --}}
        <h2 class="text-lg font-medium text-cozy-brown-dark">
            {{ __('Update Password') }}
        </h2>

        {{-- Update descriptive text color --}}
        <p class="mt-1 text-sm text-cozy-text-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            {{-- Update label text color --}}
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-cozy-brown-dark"/>
            {{-- Apply cozy theme input styles --}}
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="block w-full mt-1 border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            {{-- Update label text color --}}
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-cozy-brown-dark"/>
             {{-- Apply cozy theme input styles --}}
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="block w-full mt-1 border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
             {{-- Update label text color --}}
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-cozy-brown-dark"/>
             {{-- Apply cozy theme input styles --}}
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="block w-full mt-1 border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
             {{-- Apply cozy theme primary button styles --}}
            <x-primary-button class="!bg-cozy-purple hover:!bg-cozy-purple-dark active:!bg-cozy-purple-dark focus:!outline-none focus:!border-cozy-purple-dark focus:!ring ring-cozy-purple-light">
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                 {{-- Style status message like cozy success message --}}
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)" {{-- Increased timeout slightly --}}
                    class="text-sm text-cozy-green-dark px-3 py-1 bg-cozy-green-light/60 rounded-md border border-cozy-green/50"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</div>