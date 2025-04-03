<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Bookmarks') }}
            </h2>
            {{-- Add Bookmark Button with Icon --}}
            <a href="{{ route('bookmarks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 dark:ring-indigo-600 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add Bookmark') }}
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Success/AJAX Messages Area --}}
        <div class="px-4 sm:px-0 mb-6"> {{-- Add padding consistent with grid --}}
            @if (session('success'))
                <div id="success-message" class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 rounded-lg shadow">
                    {{ session('success') }}
                </div>
            @endif
            <div id="ajax-success-message" class="hidden mb-4 p-4 rounded-lg shadow">
                <!-- JS will add styles and content here -->
            </div>
        </div>

        {{-- Filter/Search Section - Card Style --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 sm:p-6 mb-8">
             {{-- ============================================== --}}
             {{-- THE FIX IS HERE: Added method="GET"          --}}
             {{-- ============================================== --}}
             <form method="GET" action="{{ route('bookmarks.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                 {{-- Search Input --}}
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                          </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $currentFilters['search'] ?? '' }}" placeholder="Title, Desc, URL..." class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md">
                    </div>
                </div>

                 {{-- Category Filter --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="all" @selected(($currentFilters['category'] ?? 'all') == 'all')>All Categories</option>
                        <option value="uncategorized" @selected(($currentFilters['category'] ?? '') == 'uncategorized')>Uncategorized</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(($currentFilters['category'] ?? '') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tag Filter --}}
                <div>
                    <label for="tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tag</label>
                    <select name="tag" id="tag" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="" @selected(empty($currentFilters['tag']))>All Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->slug }}" @selected(($currentFilters['tag'] ?? '') == $tag->slug)>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort By Filter --}}
                <div>
                   <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                   <select name="sort" id="sort" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="created_at_desc" @selected(($currentFilters['sort'] ?? 'created_at_desc') == 'created_at_desc')>Newest First</option>
                        <option value="created_at_asc" @selected(($currentFilters['sort'] ?? '') == 'created_at_asc')>Oldest First</option>
                        <option value="title_asc" @selected(($currentFilters['sort'] ?? '') == 'title_asc')>Title (A-Z)</option>
                        <option value="title_desc" @selected(($currentFilters['sort'] ?? '') == 'title_desc')>Title (Z-A)</option>
                        <option value="favorites_first" @selected(($currentFilters['sort'] ?? '') == 'favorites_first')>Favorites First</option>
                   </select>
               </div>

                {{-- Favorites Checkbox & Buttons --}}
                <div class="md:col-span-5 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 pt-4">
                     <div class="flex items-center space-x-2 flex-shrink-0">
                         <input type="checkbox" name="favorites" id="favorites" value="1" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-offset-gray-800" @checked(($currentFilters['favorites'] ?? '') == '1')>
                         <label for="favorites" class="text-sm font-medium text-gray-700 dark:text-gray-300">Show Favorites Only</label>
                     </div>
                     <div class="flex space-x-2 flex-shrink-0">
                         <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                               <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                             </svg>
                             Filter
                         </button>
                         <a href="{{ route('bookmarks.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             Clear
                         </a>
                     </div>
                </div>
            </form>
        </div>


        {{-- Bookmark Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="bookmark-list">
            @forelse ($bookmarks as $bookmark)
                {{-- Bookmark Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200 ease-in-out flex flex-col">
                    {{-- Card Body --}}
                    <div class="p-5 flex-grow">
                        <div class="flex items-start justify-between mb-2">
                            {{-- Title --}}
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex-grow mr-2">
                                <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 dark:hover:text-indigo-400 break-words"> {{-- break-words --}}
                                    {{ $bookmark->title }}
                                </a>
                            </h3>
                            {{-- Favorite Button --}}
                            <form action="{{ route('bookmarks.toggleFavorite', $bookmark) }}" method="POST" class="inline-block align-middle favorite-toggle-form flex-shrink-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-gray-400 hover:text-yellow-500 focus:outline-none favorite-toggle-button p-1 rounded-full hover:bg-yellow-100 dark:hover:bg-gray-700 {{ $bookmark->is_favorite ? 'text-yellow-400' : '' }}" aria-label="{{ $bookmark->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 favorite-icon" viewBox="0 0 20 20" fill="currentColor"> <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                </button>
                            </form>
                        </div>

                        {{-- URL --}}
                        <p class="text-sm text-indigo-500 dark:text-indigo-400 break-all mb-3 truncate"> {{-- truncate long urls --}}
                            <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer" class="hover:underline" title="{{ $bookmark->url }}">
                                {{ Str::limit($bookmark->url, 60) }} {{-- Limit displayed URL length --}}
                            </a>
                        </p>

                        {{-- Description --}}
                        @if($bookmark->description)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 mb-3">{{ Str::limit($bookmark->description, 120) }}</p>
                        @endif

                        {{-- Tags --}}
                        @if($bookmark->tags->isNotEmpty())
                            <div class="mb-3 flex flex-wrap gap-1">
                                @foreach($bookmark->tags as $tag)
                                    <a href="{{ route('bookmarks.index', array_merge(request()->query(), ['tag' => $tag->slug, 'page' => 1])) }}"
                                       class="inline-block bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-full px-2.5 py-0.5 text-xs font-medium hover:bg-blue-200 dark:hover:bg-blue-800/50 whitespace-nowrap">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                         {{-- Category & Notes (inline block to wrap better) --}}
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs mb-3">
                            @if($bookmark->category)
                            <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-2.5 py-0.5 font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                {{ $bookmark->category->name }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-2.5 py-0.5 font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                Uncategorized
                            </span>
                            @endif

                            @if($bookmark->notes)
                            <div x-data="{ open: false }" class="inline-block">
                                <button @click="open = !open" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline focus:outline-none font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <span x-show="!open">Notes</span>
                                    <span x-show="open">Notes</span>
                                </button>
                                <div x-show="open" x-transition x-cloak class="mt-1 p-2 bg-gray-50 dark:bg-gray-700/50 border dark:border-gray-600 rounded text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap w-full clear-both">
                                    {{ $bookmark->notes }}
                                </div>
                            </div>
                            @endif
                        </div>

                    </div> {{-- End Card Body --}}

                    {{-- Card Footer --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 px-5 py-3 flex justify-between items-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                           Added: {{ $bookmark->created_at->diffForHumans() }} {{-- Use diffForHumans --}}
                        </p>
                         {{-- Action Buttons --}}
                        <div class="flex space-x-3">
                            <a href="{{ route('bookmarks.edit', $bookmark) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-xs font-medium inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Edit
                            </a>
                            <form action="{{ route('bookmarks.destroy', $bookmark) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-xs font-medium inline-flex items-center gap-1">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div> {{-- End Card --}}
            @empty
                 {{-- UPDATED Empty State --}}
                <div class="md:col-span-2 lg:col-span-3 text-center py-12 bg-white dark:bg-gray-800 shadow-md rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">No bookmarks found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a new bookmark.</p>
                     <div class="mt-6">
                       <a href="{{ route('bookmarks.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                         <svg class="h-5 w-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                           <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                         </svg>
                         New Bookmark
                       </a>
                     </div>
                </div>
            @endforelse
        </div> {{-- End Grid --}}


        {{-- Pagination Links --}}
        <div class="mt-8"> {{-- Increased margin --}}
             {{ $bookmarks->appends(request()->query())->links() }}
        </div>

    </div> {{-- End max-w-7xl --}}


    {{-- AJAX Script for Favorites (remains the same) --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ajaxSuccessMessageDiv = document.getElementById('ajax-success-message');
            const standardSuccessMessageDiv = document.getElementById('success-message');
            if (standardSuccessMessageDiv) { /* ... hide logic ... */ }
            const forms = document.querySelectorAll('.favorite-toggle-form');
            forms.forEach(form => { /* ... fetch logic ... */ });
        });
    </script>
    @endpush

</x-app-layout>