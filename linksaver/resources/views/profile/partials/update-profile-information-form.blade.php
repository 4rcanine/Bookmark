{{-- Apply cozy theme card container --}}
<div class="p-6 bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light shadow-md rounded-lg">
    <header>
        {{-- Update header text color --}}
        <h2 class="text-lg font-medium text-cozy-brown-dark">
            {{ __('Profile Information') }}
        </h2>

        {{-- Update descriptive text color --}}
        <p class="mt-1 text-sm text-cozy-text-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            {{-- Update label text color --}}
            <x-input-label for="name" :value="__('Name')" class="text-cozy-brown-dark"/>
            {{-- Apply cozy theme input styles --}}
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="block w-full mt-1 border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
             {{-- Update label text color --}}
            <x-input-label for="email" :value="__('Email')" class="text-cozy-brown-dark"/>
             {{-- Apply cozy theme input styles --}}
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="block w-full mt-1 border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                :value="old('email', $user->email)"
                required
                autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    {{-- Update text color --}}
                    <p class="text-sm mt-2 text-cozy-text">
                        {{ __('Your email address is unverified.') }}

                        {{-- Update link/button color --}}
                        <button form="send-verification" class="underline text-sm text-cozy-purple hover:text-cozy-purple-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cozy-purple">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                         {{-- Update success message color --}}
                        <p class="mt-2 font-medium text-sm text-cozy-green-dark">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            {{-- Apply cozy theme primary button styles --}}
            <x-primary-button class="!bg-cozy-purple hover:!bg-cozy-purple-dark active:!bg-cozy-purple-dark focus:!outline-none focus:!border-cozy-purple-dark focus:!ring ring-cozy-purple-light">
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
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