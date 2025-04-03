<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category; // Import Category model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Validation\Rule; // For validation rules

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
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
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('bookmarks.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

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
     * (Optional: Not strictly needed if everything is on the index page)
     */
    public function show(Bookmark $bookmark)
    {
         // Authorize: Ensure the bookmark belongs to the logged-in user
         if ($bookmark->user_id !== Auth::id()) {
             abort(403); // Forbidden
         }
         return view('bookmarks.show', compact('bookmark')); // If you create a show view
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bookmark $bookmark)
    {
         // Authorize: Ensure the bookmark belongs to the logged-in user
         if ($bookmark->user_id !== Auth::id()) {
             abort(403);
         }

        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('bookmarks.edit', compact('bookmark', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        // Authorize: Ensure the bookmark belongs to the logged-in user
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
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
             // No validation needed for is_favorite here, handled by toggleFavorite
        ]);

        $bookmark->update($validated);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
         // Authorize: Ensure the bookmark belongs to the logged-in user
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $bookmark->delete();
        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted successfully!');
    }

    /**
     * Toggle the favorite status of the specified resource.
     */
    public function toggleFavorite(Request $request, Bookmark $bookmark)
    {
        // Authorize: Ensure the bookmark belongs to the logged-in user
        if ($bookmark->user_id !== Auth::id()) {
             return response()->json(['message' => 'Unauthorized'], 403); // Respond with JSON for potential AJAX calls
            // Or abort(403); if only using form submissions
        }

        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();

        // Determine if request is AJAX or standard form submission
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Favorite status toggled.',
                'is_favorite' => $bookmark->is_favorite
            ]);
        }

         // Redirect back with a success message (for non-AJAX)
         // Consider redirecting back to the previous page with filters preserved
         return back()->with('success', 'Favorite status updated.');
    }
}