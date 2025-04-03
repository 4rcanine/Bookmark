<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Bookmarks') }}
            </h2>
            <a href="{{ route('bookmarks.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Add Bookmark') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Success Message --}}
                    @if (session('success'))
                        <div id="success-message" class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                     {{-- Placeholder for AJAX success messages --}}
                    <div id="ajax-success-message" class="hidden mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 rounded">
                        <!-- Message will be inserted here by JS -->
                    </div>


                    {{-- Filter/Search Form --}}
                    <form method="GET" action="{{ route('bookmarks.index') }}" class="mb-6 space-y-4 md:space-y-0 md:flex md:space-x-4 md:items-end">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search" value="{{ $currentFilters['search'] ?? '' }}" placeholder="Title, Desc, URL..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option value="all" {{ ($currentFilters['category'] ?? 'all') == 'all' ? 'selected' : '' }}>All Categories</option>
                                <option value="uncategorized" {{ ($currentFilters['category'] ?? '') == 'uncategorized' ? 'selected' : '' }}>Uncategorized</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ ($currentFilters['category'] ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                         <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                            <select name="sort" id="sort" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                 <option value="created_at_desc" {{ ($currentFilters['sort'] ?? 'created_at_desc') == 'created_at_desc' ? 'selected' : '' }}>Newest First</option>
                                 <option value="created_at_asc" {{ ($currentFilters['sort'] ?? '') == 'created_at_asc' ? 'selected' : '' }}>Oldest First</option>
                                 <option value="title_asc" {{ ($currentFilters['sort'] ?? '') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                                 <option value="title_desc" {{ ($currentFilters['sort'] ?? '') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                                 <option value="favorites_first" {{ ($currentFilters['sort'] ?? '') == 'favorites_first' ? 'selected' : '' }}>Favorites First</option>
                            </select>
                        </div>

                        <div class="flex items-center space-x-2 pt-5">
                             <input type="checkbox" name="favorites" id="favorites" value="1" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ ($currentFilters['favorites'] ?? '') == '1' ? 'checked' : '' }}>
                             <label for="favorites" class="text-sm font-medium text-gray-700 dark:text-gray-300">Favorites Only</label>
                         </div>


                        <div class="pt-5">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Filter
                            </button>
                            <a href="{{ route('bookmarks.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-400 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Clear
                            </a>
                        </div>
                    </form>


                    {{-- Bookmark List --}}
                    {{-- Added ID for potential event delegation if needed later --}}
                    <div class="space-y-4" id="bookmark-list">
                        @forelse ($bookmarks as $bookmark)
                            <div class="p-4 border dark:border-gray-700 rounded-lg flex justify-between items-start">
                                <div class="flex-grow mr-4">
                                    <div class="flex items-center space-x-2 mb-1">
                                        {{-- Favorite Toggle Form -- START MODIFICATION --}}
                                        <form action="{{ route('bookmarks.toggleFavorite', $bookmark) }}"
                                              method="POST"
                                              class="inline-block align-middle favorite-toggle-form"> {{-- ADDED CLASS --}}
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-gray-400 hover:text-yellow-500 focus:outline-none favorite-toggle-button {{ $bookmark->is_favorite ? 'text-yellow-400' : '' }}" {{-- ADDED CLASS --}}
                                                    aria-label="{{ $bookmark->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"> {{-- Improved accessibility --}}

                                                {{-- Added specific class to the icon --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 favorite-icon" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </button>
                                        </form>
                                         {{-- END MODIFICATION --}}

                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-blue-600 dark:hover:text-blue-400 break-all">{{ $bookmark->title }}</a>
                                        </h3>
                                    </div>

                                    <p class="text-sm text-gray-500 dark:text-gray-400 break-all">
                                        <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $bookmark->url }}</a>
                                    </p>
                                    @if($bookmark->description)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                        {{ Str::limit($bookmark->description, 150) }} {{-- Limit description length --}}
                                    </p>
                                    @endif
                                    @if($bookmark->category)
                                    <span class="mt-2 inline-block bg-gray-200 dark:bg-gray-700 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $bookmark->category->name }}
                                    </span>
                                    @else
                                    <span class="mt-2 inline-block bg-gray-100 dark:bg-gray-600 rounded-full px-3 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        Uncategorized
                                    </span>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Added: {{ $bookmark->created_at->format('M d, Y') }}</p> {{-- Show created date --}}
                                </div>

                                <div class="flex-shrink-0 flex space-x-2">
                                    <a href="{{ route('bookmarks.edit', $bookmark) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                                    <form action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bookmark?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400">You haven't saved any bookmarks yet.</p>
                        @endforelse
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-6">
                         {{-- Append query string parameters to pagination links --}}
                         {{ $bookmarks->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- START: JavaScript for AJAX Favorite Toggle                  --}}
    {{-- =========================================================== --}}
    @push('scripts')
    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Get the container for AJAX messages
             const ajaxSuccessMessageDiv = document.getElementById('ajax-success-message');
             // Hide the standard success message after a few seconds if it exists
             const standardSuccessMessageDiv = document.getElementById('success-message');
             if (standardSuccessMessageDiv) {
                 setTimeout(() => {
                     standardSuccessMessageDiv.style.display = 'none';
                 }, 5000); // Hide after 5 seconds
             }


            // Find all favorite toggle forms
            const forms = document.querySelectorAll('.favorite-toggle-form');

            forms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    // Prevent the default form submission (page reload)
                    event.preventDefault();

                    const currentForm = event.target;
                    const actionUrl = currentForm.action;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const icon = currentForm.querySelector('.favorite-icon');
                    const button = currentForm.querySelector('.favorite-toggle-button');

                    // Hide any previous AJAX message
                    if (ajaxSuccessMessageDiv) {
                        ajaxSuccessMessageDiv.classList.add('hidden');
                        ajaxSuccessMessageDiv.textContent = '';
                    }


                    // Optional: Provide immediate visual feedback (e.g., disable button)
                    button.disabled = true;
                    icon.classList.add('opacity-50'); // Dim the icon slightly

                    // Send the AJAX request using the Fetch API
                    fetch(actionUrl, {
                        method: 'PATCH', // Method is PATCH
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,       // Include CSRF token
                            'Accept': 'application/json',    // Tell Laravel we want JSON back
                            'X-Requested-With': 'XMLHttpRequest', // Common header for AJAX requests
                            'Content-Type': 'application/json'
                        },
                        // No body needed for toggle
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Handle HTTP errors
                            console.error('Network response was not ok:', response.status, response.statusText);
                            // Try to parse potential JSON error message from Laravel validation
                             return response.json().then(errData => {
                                 throw { status: response.status, data: errData }; // Throw an object with status and data
                             }).catch(() => {
                                 // If parsing JSON fails, throw a generic error
                                throw new Error(`HTTP error! status: ${response.status}`);
                             });
                        }
                        return response.json(); // Parse the JSON response from the controller
                    })
                    .then(data => {
                        // Successfully received JSON data
                        console.log('Success:', data); // Log success data

                        // Update the icon's appearance based on the new status
                        if (data.is_favorite) {
                            button.classList.add('text-yellow-400');
                            button.setAttribute('aria-label', 'Remove from favorites');
                        } else {
                            button.classList.remove('text-yellow-400');
                             button.setAttribute('aria-label', 'Add to favorites');
                        }

                        // Show success message in the dedicated div
                         if (ajaxSuccessMessageDiv && data.message) {
                             ajaxSuccessMessageDiv.textContent = data.message;
                             ajaxSuccessMessageDiv.classList.remove('hidden');
                             // Optional: Hide message after a few seconds
                             setTimeout(() => {
                                 ajaxSuccessMessageDiv.classList.add('hidden');
                                 ajaxSuccessMessageDiv.textContent = '';
                             }, 3000); // Hide after 3 seconds
                         }
                    })
                    .catch(error => {
                        // Handle errors
                        console.error('Error toggling favorite status:', error);
                        let errorMessage = 'Could not update favorite status. Please try again.';
                         // Check if it's our custom error object with data
                         if (error && error.data && error.data.message) {
                             errorMessage = error.data.message; // Use message from server if available (e.g., validation)
                         } else if (error && error.message) {
                            errorMessage = error.message; // Use generic fetch error message
                         }

                         // Display error message (consider a dedicated error div)
                         if (ajaxSuccessMessageDiv) { // Re-using success div for errors for simplicity
                             ajaxSuccessMessageDiv.textContent = `Error: ${errorMessage}`;
                             ajaxSuccessMessageDiv.classList.remove('hidden');
                             ajaxSuccessMessageDiv.classList.remove('bg-green-100', 'dark:bg-green-900', 'border-green-400', 'text-green-700', 'dark:text-green-300');
                             ajaxSuccessMessageDiv.classList.add('bg-red-100', 'dark:bg-red-900', 'border-red-400', 'text-red-700', 'dark:text-red-300');
                         } else {
                             alert(errorMessage); // Fallback to alert
                         }

                    })
                    .finally(() => {
                         // Re-enable the button and remove dimming regardless of success/failure
                        button.disabled = false;
                        icon.classList.remove('opacity-50');
                    });
                });
            });
        });
    </script>
    @endpush
    {{-- =========================================================== --}}
    {{-- END: JavaScript for AJAX Favorite Toggle                    --}}
    {{-- =========================================================== --}}

</x-app-layout>