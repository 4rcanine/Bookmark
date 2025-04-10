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
        $user = Auth::user();
        $query = Bookmark::where('user_id', $user->id)->with(['category', 'tags']); // Eager load relationships

        // Apply Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            if ($request->category === 'uncategorized') {
                $query->whereNull('category_id');
            } else {
                // Filter by the selected category ID belonging to the user
                $categoryId = $request->category;
                $query->where('category_id', $categoryId);
                // More secure alternative (if needed, uncomment and remove the line above):
                // $query->whereHas('category', function ($catQuery) use ($user, $categoryId) {
                //     $catQuery->where('user_id', $user->id)->where('id', $categoryId);
                // });
            }
        }

        // Apply Favorites Filter
        if ($request->filled('favorites') && $request->favorites == '1') {
            $query->where('is_favorite', true);
        }

        // Apply Tag Filter
        if ($request->filled('tag')) {
            $tagSlug = $request->tag;
             $query->whereHas('tags', function ($tagQuery) use ($user, $tagSlug) {
                 $tagQuery->where('user_id', $user->id)->where('slug', $tagSlug);
             });
        }

        // Apply Search Filter
        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(function ($q) use ($searchTerm) {
                 $q->where('title', 'like', $searchTerm)
                   ->orWhere('description', 'like', $searchTerm)
                   ->orWhere('url', 'like', $searchTerm);
                 // Optional: Search in notes too
                 // ->orWhere('notes', 'like', $searchTerm);
             });
        }

        // Apply Sorting
        $sort = $request->input('sort', 'created_at_desc'); // Default sort
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
            case 'favorites_first':
                // Order by favorite status descending (true=1, false=0), then by creation date
                $query->orderBy('is_favorite', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Execute query with pagination, preserving filter parameters
        $bookmarks = $query->paginate(15)->withQueryString(); // <-- Added withQueryString()

        // Data for filters dropdowns
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $tags = Tag::where('user_id', $user->id)->orderBy('name')->get();

        // Pass current filters back to the view to repopulate form fields
        $currentFilters = $request->only(['category', 'search', 'sort', 'favorites', 'tag']);

        return view('bookmarks.index', compact('bookmarks', 'categories', 'tags', 'currentFilters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Bookmark::class);
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark = new Bookmark();
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all(); // For Tagify whitelist
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
            'notes' => 'nullable|string|max:10000',
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
            'tags' => 'nullable|string|max:1000', // Comma-separated tags from Tagify
        ]);

        // Explicit Assignment to avoid mass assignment issues on create
        $bookmark = new Bookmark();
        $bookmark->user_id = $user->id;
        $bookmark->title = $validated['title'];
        $bookmark->url = $validated['url'];
        $bookmark->description = $validated['description'] ?? null;
        $bookmark->notes = $validated['notes'] ?? null;
        $bookmark->category_id = $validated['category_id'] ?? null;
        // is_favorite defaults to false in migration/model

        $bookmark->save(); // Save bookmark to get an ID

        // Sync tags after bookmark exists
        $this->syncTags($user, $bookmark, $validated['tags'] ?? null);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark added successfully!');
    }

    /**
     * Display the specified resource. (Redirects to edit)
     */
    public function show(Bookmark $bookmark)
    {
        $this->authorize('view', $bookmark);
        // Typically 'show' would display details, but redirecting to edit is fine too
        return redirect()->route('bookmarks.edit', $bookmark);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $user = $bookmark->user; // Get user from the bookmark model
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark->loadMissing('tags'); // Ensure tags are loaded
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all(); // For Tagify whitelist
        return view('bookmarks.edit', compact('bookmark', 'categories', 'userTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $user = $bookmark->user;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:10000',
            'category_id' => [
                'nullable',
                'integer',
                 // Ensure the category exists and belongs to the user
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
             ],
            'tags' => 'nullable|string|max:1000', // Comma-separated tags from Tagify
        ]);

        // Use mass assignment for update (generally safe as model exists)
        // Ensure 'user_id' is NOT in $fillable or handle it carefully if it is
        $bookmarkData = collect($validated)->except('tags')->toArray();
        $bookmark->update($bookmarkData);

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
         // Ensure the user owns the bookmark before toggling
        $this->authorize('toggleFavorite', $bookmark); // Assumes you have a policy method

        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();

        // Handle AJAX request if sent with appropriate headers
        if ($request->expectsJson()) {
             return response()->json([
                 'success' => true,
                 'is_favorite' => $bookmark->is_favorite,
                 'message' => $bookmark->is_favorite ? 'Bookmark added to favorites.' : 'Bookmark removed from favorites.'
             ]);
        }

        // Standard redirect for non-AJAX
        return back()->with('success', 'Favorite status updated.');
    }

    /** Helper method to sync tags */
    private function syncTags(User $user, Bookmark $bookmark, ?string $tagsInput): void
    {
         // Detach all tags if input is null or empty string after trim
        if (is_null($tagsInput) || trim($tagsInput) === '') {
            $bookmark->tags()->detach();
            return;
        }

        // Process the input string
        $tagNames = collect(explode(',', $tagsInput))
                        ->map(fn($name) => trim($name)) // Trim whitespace
                        ->filter() // Remove empty entries resulting from extra commas (e.g., "tag1,,tag2")
                        ->unique(); // Ensure unique tag names

        // If no valid tag names remain, detach all
        if ($tagNames->isEmpty()) {
            $bookmark->tags()->detach();
            return;
        }

        // Find or create tags and get their IDs
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            // Double check for empty tag name just in case filter didn't catch something unusual
            if (empty($tagName)) continue;

            $slug = Str::slug($tagName);

            // Use firstOrCreate to find existing tag or create a new one for the user
            // IMPORTANT: Ensures tags are scoped by user_id AND slug for uniqueness per user
            $tag = Tag::firstOrCreate(
                ['user_id' => $user->id, 'slug' => $slug], // Attributes to find
                ['name' => $tagName] // Attributes to use if creating
            );
            $tagIds[] = $tag->id;
        }

        // Sync the bookmark's tags with the processed list of tag IDs
        // This will attach new tags, detach removed tags, and leave existing ones.
        $bookmark->tags()->sync($tagIds);
    }
}