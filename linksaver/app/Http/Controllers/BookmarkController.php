<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag; // Correctly included
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\User; // Correctly included

class BookmarkController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ... (index logic remains the same) ...

        $user = Auth::user();
        $query = Bookmark::where('user_id', $user->id)->with(['category', 'tags']);
        if ($request->filled('category') && $request->category !== 'all') { /* ... */ }
        if ($request->filled('favorites') && $request->favorites == '1') { /* ... */ }
        if ($request->filled('tag')) {
            $tagSlug = $request->tag;
             $query->whereHas('tags', function ($tagQuery) use ($user, $tagSlug) {
                 $tagQuery->where('user_id', $user->id)->where('slug', $tagSlug);
             });
         }
        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function ($q) use ($searchTerm) {
                 $q->where('title', 'like', $searchTerm)
                   ->orWhere('description', 'like', $searchTerm)
                   ->orWhere('url', 'like', $searchTerm);
             });
         }
        $sort = $request->input('sort', 'created_at_desc');
        switch ($sort) { /* ... */ }
        $bookmarks = $query->paginate(15);
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $tags = Tag::where('user_id', $user->id)->orderBy('name')->get();
        $currentFilters = $request->only(['category', 'search', 'sort', 'favorites', 'tag']);
        return view('bookmarks.index', compact('bookmarks', 'categories', 'tags', 'currentFilters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ... (remains the same - passes $userTags) ...
        $this->authorize('create', Bookmark::class);
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark = new Bookmark();
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all();
        return view('bookmarks.create', compact('categories', 'bookmark', 'userTags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) // <-- MODIFIED THIS METHOD
    {
        $this->authorize('create', Bookmark::class);
        $user = Auth::user();

        // Validation remains the same
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:10000',
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
            'tags' => 'nullable|string|max:1000', // Tag input validation
        ]);

        // START: Explicit Assignment Fix
        // Instantiate the model
        $bookmark = new Bookmark();
        // Assign validated fields explicitly
        $bookmark->user_id = $user->id;
        $bookmark->title = $validated['title']; // REQUIRED
        $bookmark->url = $validated['url'];     // REQUIRED
        $bookmark->description = $validated['description'] ?? null;
        $bookmark->notes = $validated['notes'] ?? null;
        $bookmark->category_id = $validated['category_id'] ?? null;
        // is_favorite defaults to false in DB/model

        // Save the bookmark with core data FIRST
        $bookmark->save();
        // END: Explicit Assignment Fix

        // Now process and sync tags AFTER bookmark is saved and has an ID
        $this->syncTags($user, $bookmark, $validated['tags'] ?? null); // Use helper method

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bookmark $bookmark)
    {
        // ... (remains the same) ...
        $this->authorize('view', $bookmark);
        return redirect()->route('bookmarks.edit', $bookmark);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bookmark $bookmark)
    {
        // ... (remains the same - passes $userTags) ...
        $this->authorize('update', $bookmark);
        $user = $bookmark->user;
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark->loadMissing('tags');
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all();
        return view('bookmarks.edit', compact('bookmark', 'categories', 'userTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        // Note: Update uses $bookmark->update($bookmarkData) which relies on $fillable
        // This is generally okay for update as the model exists.
        // The NOT NULL error typically happens only on INSERT (store).
        // If you encounter issues here, you could apply explicit assignment too.

        $this->authorize('update', $bookmark);
        $user = $bookmark->user;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:10000',
            'category_id' => [ /* ... */ ],
            'tags' => 'nullable|string|max:1000',
        ]);
        $bookmarkData = collect($validated)->except('tags')->toArray();
        $bookmark->update($bookmarkData); // Mass assignment for update is usually fine
        $this->syncTags($user, $bookmark, $validated['tags'] ?? null);
        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
        // ... (remains the same) ...
        $this->authorize('delete', $bookmark);
        $bookmark->delete();
        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted successfully!');
    }

    /**
     * Toggle the favorite status of the specified resource.
     */
    public function toggleFavorite(Request $request, Bookmark $bookmark)
    {
        // ... (remains the same) ...
        $this->authorize('toggleFavorite', $bookmark);
        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();
        if ($request->expectsJson()) { /* ... */ }
        return back()->with('success', 'Favorite status updated.');
    }

    /** Helper method to sync tags */
    private function syncTags(User $user, Bookmark $bookmark, ?string $tagsInput): void
    {
        // ... (remains the same) ...
         if (is_null($tagsInput)) { $bookmark->tags()->detach(); return; }
         $tagNames = collect(explode(',', $tagsInput))->map(fn($name) => trim($name))->filter()->unique();
         if ($tagNames->isEmpty()) { $bookmark->tags()->detach(); return; }
         $tagIds = [];
         foreach ($tagNames as $tagName) {
            if (empty($tagName)) continue;
            $slug = Str::slug($tagName);
            $tag = Tag::firstOrCreate(
                ['user_id' => $user->id, 'slug' => $slug],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
         }
         $bookmark->tags()->sync($tagIds);
    }
}