<x-app-layout>
    <x-slot name="header">
        {{-- Use your cozy header style --}}
        <h2 class="font-semibold text-xl text-cozy-brown-dark leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    {{-- Use consistent padding like your Categories page --}}
    <div class="py-8">
        {{-- Adjust max-w- if needed (3xl was used on Categories) --}}
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Include the partial directly.
                 The styled cozy div is INSIDE the partial file itself. --}}
            @include('profile.partials.update-profile-information-form')

            {{-- Include the partial directly. --}}
            @include('profile.partials.update-password-form')

            {{-- Include the partial directly. --}}
            @include('profile.partials.delete-user-form')

        </div>
    </div>
</x-app-layout>