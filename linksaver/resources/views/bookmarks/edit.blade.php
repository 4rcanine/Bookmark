<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Bookmark') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('bookmarks.update', $bookmark) }}">
                        @method('PUT')
                        {{-- Include the form partial --}}
                        {{-- Pass categories and the existing bookmark --}}
                        {{-- $userTags is now passed from the controller --}}
                        @include('bookmarks._form', ['categories' => $categories, 'bookmark' => $bookmark])

                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('bookmarks.index') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Bookmark') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Tagify Initialization Script (Identical to create view) --}}
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
                        classname: "tags-look",
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