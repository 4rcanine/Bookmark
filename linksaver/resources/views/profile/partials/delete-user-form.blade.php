{{-- Apply cozy theme card container --}}
<div class="p-6 bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light shadow-md rounded-lg">
    <header>
        {{-- Update header text color --}}
        <h2 class="text-lg font-medium text-cozy-brown-dark">
            {{ __('Delete Account') }}
        </h2>

        {{-- Update descriptive text color --}}
        <p class="mt-1 text-sm text-cozy-text-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- Danger button styling usually remains standard red for UX consistency --}}
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="mt-4" {{-- Added margin-top for spacing --}}
    >{{ __('Delete Account') }}</x-danger-button>

    {{-- Style the modal itself if possible (depends on component implementation) --}}
    {{-- Add panel classes if the component supports it: 'bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light shadow-md rounded-lg' --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable >
        {{-- Apply cozy theme styles within the modal content --}}
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-cozy-cream/90 border border-cozy-brown-light rounded-lg shadow-lg"> {{-- Added cozy bg/border directly to form for modal panel effect --}}
            @csrf
            @method('delete')

            {{-- Update modal header text color --}}
            <h2 class="text-lg font-medium text-cozy-brown-dark">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            {{-- Update modal descriptive text color --}}
            <p class="mt-1 text-sm text-cozy-text-muted">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                 {{-- Update label text color (sr-only, but good practice) --}}
                <x-input-label for="password_delete" value="{{ __('Password') }}" class="sr-only text-cozy-brown-dark" />

                {{-- Apply cozy theme input styles --}}
                <x-text-input
                    id="password_delete" {{-- Changed id slightly to avoid conflict if modal is on same page as other password fields --}}
                    name="password"
                    type="password"
                    class="mt-1 block w-full md:w-3/4 border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end space-x-3"> {{-- Added space-x-3 --}}
                {{-- Style secondary button for cozy theme --}}
                <x-secondary-button
                    x-on:click="$dispatch('close')"
                    class="!bg-white hover:!bg-gray-50 !text-cozy-text !border-cozy-brown-light focus:!ring-cozy-purple" {{-- More specific overrides --}}
                >
                    {{ __('Cancel') }}
                </x-secondary-button>

                 {{-- Danger button styling remains standard red --}}
                <x-danger-button>
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</div>