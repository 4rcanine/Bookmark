<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Keep Auth for Auth::user() where needed
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- FIX: ADD THIS LINE

class BookmarkController extends Controller
{
    use AuthorizesRequests; // <-- FIX: ADD THIS LINE

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Authorization for viewing the list is typically handled by the 'auth' middleware
        // but you could add an explicit check if needed:
        // $this->authorize('viewAny', Bookmark::class);

        $user = Auth::user(); // Still needed to scope queries/data
        $query = Bookmark::where('user_id', $user->id)->with('category'); // Eager load category

        // --- Filtering ---
        if ($request->filled('category') && $request->category !== 'all') {
             if ($request->category === 'uncategorized') {
                 $query->whereNull('category_id');
             } else {
                 $query->where('category_id', $request->category);
             }
        }
         if ($request->filled('favorites') && $request->favorites == '1') {
             $query->where('is_favorite', true);
         }

        // --- Searching ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('url', 'like', $searchTerm);
            });
        }

        // --- Sorting ---
        $sort = $request->input('sort', 'created_at_desc'); // Default sort
        switch ($sort) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
             case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
             case 'favorites_first':
                 $query->orderBy('is_favorite', 'desc')->orderBy('created_at', 'desc'); // Favorites first, then newest
                 break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $bookmarks = $query->paginate(15); // Paginate results

        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();

        // Pass sorting/filtering parameters back to the view
        $currentFilters = $request->only(['category', 'search', 'sort', 'favorites']);


        return view('bookmarks.index', compact('bookmarks', 'categories', 'currentFilters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Authorization handled by 'auth' middleware
        // $this->authorize('create', Bookmark::class); // Optional explicit check

        $user = Auth::user(); // Still needed to get user's categories
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('bookmarks.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Authorization handled by 'auth' middleware
        // $this->authorize('create', Bookmark::class); // Optional explicit check

        $user = Auth::user(); // Still needed for validation and setting user_id

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'category_id' => [
                'nullable',
                'integer',
                // Ensure the category exists and belongs to the current user
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
        ]);

        $bookmark = new Bookmark($validated);
        $bookmark->user_id = $user->id; // Assign the logged-in user's ID
        $bookmark->save();

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bookmark $bookmark)
    {
        // Use Policy for authorization
        $this->authorize('view', $bookmark); // Checks BookmarkPolicy::view()

        // Or redirect to edit or index if show view doesn't exist
        return redirect()->route('bookmarks.edit', $bookmark);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bookmark $bookmark)
    {
        // Use Policy for authorization
        // Using 'update' permission check here makes sense, as viewing the edit form
        // implies the intent/ability to update.
        $this->authorize('update', $bookmark); // Checks BookmarkPolicy::update()

        // Get the user from the authorized bookmark model relationship
        $user = $bookmark->user; // Or Auth::user() still works fine
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('bookmarks.edit', compact('bookmark', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        // Use Policy for authorization
        $this->authorize('update', $bookmark); // Checks BookmarkPolicy::update()

        // Get user from bookmark or Auth, needed for validation context
        $user = $bookmark->user; // Or Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:5000',
            'category_id' => [
                'nullable',
                'integer',
                 Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
             // is_favorite handled by toggleFavorite
        ]);

        $bookmark->update($validated);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
         // Use Policy for authorization
         $this->authorize('delete', $bookmark); // Checks BookmarkPolicy::delete()

        $bookmark->delete();
        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted successfully!');
    }

    /**
     * Toggle the favorite status of the specified resource.
     */
    public function toggleFavorite(Request $request, Bookmark $bookmark)
    {
        // Use Policy for authorization - matching the method name in the Policy
        $this->authorize('toggleFavorite', $bookmark); // Checks BookmarkPolicy::toggleFavorite()

        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();

        // Determine if request is AJAX or standard form submission
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Favorite status toggled.',
                'is_favorite' => $bookmark->is_favorite // Send back the new status
            ]);
        }

         // Redirect back with a success message (for non-AJAX)
         return back()->with('success', 'Favorite status updated.');
    }
}