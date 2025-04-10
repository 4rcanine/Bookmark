
@csrf

<div>
    <label for="title" class="block text-sm font-medium text-cozy-text">{{ __('Title') }}</label>
    <input id="title" class="block mt-1 w-full border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text" type="text" name="title" value="{{ old('title', $bookmark->title) }}" required autofocus autocomplete="off" />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mt-4">
    <label for="url" class="block text-sm font-medium text-cozy-text">{{ __('URL') }}</label>
    <input id="url" class="block mt-1 w-full border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text" type="url" name="url" value="{{ old('url', $bookmark->url) }}" required placeholder="https://example.com" />
    <x-input-error :messages="$errors->get('url')" class="mt-2" />
</div>

<div class="mt-4">
    <label for="description" class="block text-sm font-medium text-cozy-text">{{ __('Description (Optional)') }}</label>
    <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted">{{ old('description', $bookmark->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-cozy-text">{{ __('Personal Notes (Optional)') }}</label>
    <textarea id="notes" name="notes" rows="4" class="block mt-1 w-full border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text placeholder-cozy-text-muted" placeholder="Add any personal context or reminders here...">{{ old('notes', $bookmark->notes) }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>

<div class="mt-4">
   <label for="category_id" class="block text-sm font-medium text-cozy-text">{{ __('Category (Optional)') }}</label>
   <select name="category_id" id="category_id" class="block mt-1 w-full border-cozy-brown-light rounded-md shadow-sm focus:border-cozy-purple focus:ring focus:ring-cozy-purple focus:ring-opacity-50 bg-white text-cozy-text">
       <option value="">-- Select Category --</option>
       @foreach($categories as $category)
           <option value="{{ $category->id }}" @selected(old('category_id', $bookmark->category_id) == $category->id)>
               {{ $category->name }}
           </option>
       @endforeach
   </select>
   <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
   <p class="text-sm text-cozy-text-muted mt-1">Manage categories <a href="{{ route('categories.index') }}" class="text-cozy-purple hover:underline">here</a>.</p>
</div>

<div class="mt-4">
    <label for="tags-input" class="block text-sm font-medium text-cozy-text">{{ __('Tags (Optional)') }}</label>
    @php
        $tagsValue = old('tags', ($bookmark->exists && $bookmark->relationLoaded('tags')) ? $bookmark->tags->pluck('name')->implode(', ') : '');
    @endphp
    <input id="tags-input"
           name="tags" 
           type="text"
           class="block mt-1 w-full tagify--outside" 
           value="{{ $tagsValue }}" 
           placeholder="Add tags..." 
           />
    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
</div>