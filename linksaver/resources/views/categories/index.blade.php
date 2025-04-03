<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 rounded shadow-sm sm:rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add Category Form --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Add New Category</h3>
                    <form method="POST" action="{{ route('categories.store') }}" class="flex items-center space-x-4">
                         @csrf
                        <div class="flex-grow">
                            <x-input-label for="name" :value="__('Category Name')" class="sr-only"/>
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="e.g., Tech, Work, Fun"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <x-primary-button>
                            {{ __('Add') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>


             {{-- Category List --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                   <h3 class="text-lg font-medium mb-4">Your Categories</h3>
                    @if($categories->count() > 0)
                    <ul class="space-y-3">
                        @foreach ($categories as $category)
                            <li class="flex justify-between items-center p-3 border dark:border-gray-700 rounded-md">
                                <span class="text-gray-800 dark:text-gray-200">{{ $category->name }}</span>
                                {{-- Add Edit link here later if needed --}}
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure? Deleting a category will make its bookmarks uncategorized.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium">Delete</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                     @else
                         <p class="text-center text-gray-500 dark:text-gray-400">You haven't created any categories yet.</p>
                     @endif
                </div>
            </div>


        </div>
    </div>
</x-app-layout>