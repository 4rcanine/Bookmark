{{-- resources/views/bookmarks/_form.blade.php --}}
{{-- Expects $categories and $bookmark variables --}}
@csrf

<!-- Title -->
<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $bookmark->title)" required autofocus autocomplete="off" />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<!-- URL -->
<div class="mt-4">
    <x-input-label for="url" :value="__('URL')" />
    <x-text-input id="url" class="block mt-1 w-full" type="url" name="url" :value="old('url', $bookmark->url)" required placeholder="https://example.com" />
    <x-input-error :messages="$errors->get('url')" class="mt-2" />
</div>

<!-- Description -->
<div class="mt-4">
    <x-input-label for="description" :value="__('Description (Optional)')" />
    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $bookmark->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<!-- Notes -->
<div class="mt-4">
    <x-input-label for="notes" :value="__('Personal Notes (Optional)')" />
    <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Add any personal context or reminders here...">{{ old('notes', $bookmark->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>

<!-- Category -->
<div class="mt-4">
   <x-input-label for="category_id" :value="__('Category (Optional)')" />
   <select name="category_id" id="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
       <option value="">-- Select Category --</option>
       @foreach($categories as $category)
           <option value="{{ $category->id }}" @selected(old('category_id', $bookmark->category_id) == $category->id)>
               {{ $category->name }}
           </option>
       @endforeach
   </select>
   <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
   <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage categories <a href="{{ route('categories.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">here</a>.</p>
</div>

{{-- START: Tags Input Update for Tagify --}}
<div class="mt-4">
    <x-input-label for="tags-input" :value="__('Tags (Optional)')" /> {{-- Changed label 'for' --}}
    @php
        // Prepare the initial value string for editing
        $tagsValue = old('tags', ($bookmark->exists && $bookmark->relationLoaded('tags')) ? $bookmark->tags->pluck('name')->implode(', ') : '');
    @endphp
    {{-- Input has specific ID for Tagify JS --}}
    <input id="tags-input"
           name="tags" {{-- Keep name="tags" --}}
           type="text"
           class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
           value="{{ $tagsValue }}" {{-- Set initial value --}}
           placeholder="Add tags..." {{-- New placeholder --}}
           />
    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
    {{-- Removed comma-separated help text --}}
</div>
{{-- END: Tags Input Update for Tagify --}}