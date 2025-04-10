<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cozy-brown-dark leading-tight">
            {{ __('Add New Bookmark') }}
        </h2>
    </x-slot>

    {{-- Content area styling is handled by app.blade.php wrapper --}}
    <div class="py-8"> {{-- Adjust padding if needed --}}
        {{-- Style the form container card --}}
        <div class="max-w-2xl mx-auto bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light overflow-hidden shadow-md rounded-lg">
            <div class="p-6 md:p-8">
                <form method="POST" action="{{ route('bookmarks.store') }}">
                    {{-- Include the form partial --}}
                    @include('bookmarks._form', ['categories' => $categories, 'bookmark' => $bookmark])

                    <div class="flex items-center justify-end mt-6">
                        {{-- Style Cancel link --}}
                        <a href="{{ route('bookmarks.index') }}" class="text-sm text-cozy-text hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cozy-purple mr-4">
                            {{ __('Cancel') }}
                        </a>
                        {{-- Style Primary Button (Purple) --}}
                         <button type="submit" class="inline-flex items-center px-4 py-2 bg-cozy-purple border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cozy-purple-dark active:bg-cozy-purple-dark focus:outline-none focus:border-cozy-purple-dark focus:ring ring-cozy-purple-light disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                            {{ __('Save Bookmark') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Tagify Initialization Script --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.querySelector('#tags-input');
            if(input) {
                // Use the $userTags passed from controller
                var tagWhitelist = @json($userTags ?? []);
                var tagify = new Tagify(input, {
                    whitelist: tagWhitelist,
                    dropdown: {
                        maxItems: 20,
                        classname: "tags-look", // Optional class for styling dropdown
                        enabled: 0,
                        closeOnSelect: false
                    },
                    // Tell Tagify to update the original input value as a comma-separated string
                    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
                });
            } else {
                console.error('Tagify input element #tags-input not found.');
            }
        });
    </script>
    @endpush

</x-app-layout>