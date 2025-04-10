<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cozy-brown-dark leading-tight">
            {{ __('Manage Categories') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6"> 
            @if (session('success'))
                <div class="mb-4 p-4 bg-cozy-green-light border border-cozy-green text-cozy-green-dark rounded-lg shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light overflow-hidden shadow-md rounded-lg">
                <div class="p-6"> 
                    <h3 class="text-lg font-medium text-cozy-brown-dark mb-4">Add New Category</h3>
                    <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        @csrf
                        <div class="flex-grow">
                            <x-input-label for="name" :value="__('Category Name')" class="sr-only"/>
                            <x-text-input
                                id="name"
                                class="block w-full border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                placeholder="e.g., Tech, Work, Fun"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <x-primary-button class="!bg-cozy-purple hover:!bg-cozy-purple-dark active:!bg-cozy-purple-dark focus:!outline-none focus:!border-cozy-purple-dark focus:!ring ring-cozy-purple-light justify-center sm:justify-start"> {{-- Added ! for override, check if needed --}}
                            {{ __('Add') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>


             
            <div class="bg-cozy-cream/90 backdrop-blur-sm border border-cozy-brown-light overflow-hidden shadow-md rounded-lg">
                <div class="p-6"> 
                    <h3 class="text-lg font-medium text-cozy-brown-dark mb-4">Your Categories</h3>
                    @if($categories->count() > 0)
                    <ul class="space-y-3">
                        @foreach ($categories as $category)
                            <li class="flex justify-between items-center p-3 border border-cozy-brown-light bg-white/50 rounded-md shadow-sm">
                                <span class="text-cozy-text">{{ $category->name }}</span>
                                <div class="flex space-x-3 items-center">
                                  
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure? Deleting a category will make its bookmarks uncategorized.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                     @else
                        <p class="text-center text-cozy-text-muted">You haven't created any categories yet.</p>
                     @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>