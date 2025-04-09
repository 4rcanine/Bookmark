<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User; // Make sure User is imported
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection; // Keep if used elsewhere, not strictly needed here now
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookmarkController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     * (Corrected Version)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // Start base query for the authenticated user's bookmarks
        // Eager load relationships to avoid N+1 queries in the view
        $query = Bookmark::where('user_id', $user->id)->with(['category', 'tags']);

        // --- Category Filter (FIXED) ---
        if ($request->filled('category') && $request->category !== 'all') {
            $categoryValue = $request->category; // Value from dropdown ('uncategorized' or ID)

            if ($categoryValue === 'uncategorized') {
                // Filter bookmarks where category_id is NULL
                $query->whereNull('category_id');
            } else {
                // Filter bookmarks where category_id matches the selected ID
                // Ensure the value is treated as an integer for safety
                $query->where('category_id', (int)$categoryValue);
            }
        }
        // --- End Category Filter (FIXED) ---

        // --- Favorites Filter ---
        if ($request->filled('favorites') && $request->favorites == '1') {
            $query->where('is_favorite', true);
        }

        // --- Tag Filter ---
        if ($request->filled('tag')) {
            $tagSlug = $request->tag;
             $query->whereHas('tags', function ($tagQuery) use ($user, $tagSlug) {
                 $tagQuery->where('user_id', $user->id)->where('slug', $tagSlug);
             });
         }

        // --- Search Filter ---
        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function ($q) use ($searchTerm) {
                 $q->where('title', 'like', $searchTerm)
                   ->orWhere('description', 'like', $searchTerm)
                   ->orWhere('url', 'like', $searchTerm);
             });
         }

        // --- Sorting ---
        $sort = $request->input('sort', 'created_at_desc');
        switch ($sort) {
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'favorites_first': // Added handler
                 $query->orderBy('is_favorite', 'desc')->orderBy('created_at', 'desc');
                 break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // --- Pagination ---
        $bookmarks = $query->paginate(15)->withQueryString();

        // --- Data for View Filters ---
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $tags = Tag::where('user_id', $user->id)->orderBy('name')->get();
        $currentFilters = $request->only(['category', 'search', 'sort', 'favorites', 'tag']);

        return view('bookmarks.index', compact('bookmarks', 'categories', 'tags', 'currentFilters'));
    }


    // --- ALL OTHER METHODS BELOW ARE FROM YOUR PREVIOUS CODE ---

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Bookmark::class);
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark = new Bookmark(); // For form model binding
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all(); // For tag suggestions
        return view('bookmarks.create', compact('categories', 'bookmark', 'userTags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Bookmark::class);
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:10000', // Make sure 'notes' is fillable in Bookmark model
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
            'tags' => 'nullable|string|max:1000',
        ]);

        // Explicit assignment
        $bookmark = new Bookmark();
        $bookmark->user_id = $user->id;
        $bookmark->title = $validated['title'];
        $bookmark->url = $validated['url'];
        $bookmark->description = $validated['description'] ?? null;
        $bookmark->notes = $validated['notes'] ?? null;
        $bookmark->category_id = $validated['category_id'] ?? null;
        // is_favorite defaults to false based on your model/migration presumably
        $bookmark->save(); // Save first to get ID

        // Sync tags
        $this->syncTags($user, $bookmark, $validated['tags'] ?? null);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark added successfully!');
    }

    /**
     * Display the specified resource.
     * (Typically redirects to edit)
     */
    public function show(Bookmark $bookmark)
    {
        $this->authorize('view', $bookmark);
        // Usually better UX to go straight to edit
        return redirect()->route('bookmarks.edit', $bookmark);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $user = $bookmark->user; // Get user from the loaded bookmark relationship
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark->loadMissing('tags'); // Eager load tags if not already loaded
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all(); // For suggestions
        return view('bookmarks.edit', compact('bookmark', 'categories', 'userTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $user = $bookmark->user; // Get user from existing bookmark
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:10000', // Ensure fillable
            'category_id' => [
                'nullable',
                'integer',
                 Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                     return $query->where('user_id', $user->id);
                 }),
            ],
            'tags' => 'nullable|string|max:1000',
        ]);

        // Exclude tags from data used for bookmark update
        $bookmarkData = collect($validated)->except('tags')->toArray();
        // Handle nullable category_id correctly if 'Uncategorized' is intended to be NULL
        if (isset($bookmarkData['category_id']) && $bookmarkData['category_id'] === '') { // Or however you handle unsetting category
            $bookmarkData['category_id'] = null;
        }

        $bookmark->update($bookmarkData); // Use mass assignment

        // Sync tags
        $this->syncTags($user, $bookmark, $validated['tags'] ?? null);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('delete', $bookmark);
        $bookmark->delete();
        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted successfully!');
    }

    /**
     * Toggle the favorite status of the specified resource.
     */
    public function toggleFavorite(Request $request, Bookmark $bookmark)
    {
        $this->authorize('toggleFavorite', $bookmark);
        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();

        // Handle AJAX request if needed (based on your script)
        if ($request->expectsJson()) {
             return response()->json([
                 'success' => true,
                 'is_favorite' => $bookmark->is_favorite,
                 'message' => 'Favorite status updated.'
             ]);
         }

        // Fallback for non-AJAX
        return back()->with('success', 'Favorite status updated.');
    }

    /** Helper method to sync tags */
    private function syncTags(User $user, Bookmark $bookmark, ?string $tagsInput): void
    {
         if (is_null($tagsInput)) {
             $bookmark->tags()->detach();
             return;
         }
         $tagNames = collect(explode(',', $tagsInput))
             ->map(fn($name) => trim($name))
             ->filter()
             ->unique();
         if ($tagNames->isEmpty()) {
             $bookmark->tags()->detach();
             return;
         }
         $tagIds = [];
         foreach ($tagNames as $tagName) {
            if (empty($tagName)) continue;
            $slug = Str::slug($tagName);
            // Uses firstOrCreate based on user_id + slug
            $tag = Tag::firstOrCreate(
                ['user_id' => $user->id, 'slug' => $slug],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
         }
         // Sync attaches necessary, detaches unnecessary
         $bookmark->tags()->sync($tagIds);
    }
}