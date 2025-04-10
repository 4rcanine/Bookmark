<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Tag; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Models\User;

class BookmarkController extends Controller
{
    use AuthorizesRequests;


    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Bookmark::where('user_id', $user->id)->with(['category', 'tags']); // Eager load relationships

        if ($request->filled('category') && $request->category !== 'all') {
            if ($request->category === 'uncategorized') {
                $query->whereNull('category_id');
            } else {
                $categoryId = $request->category;
                $query->where('category_id', $categoryId);
                
            }
        }

        if ($request->filled('favorites') && $request->favorites == '1') {
            $query->where('is_favorite', true);
        }

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
                $query->orderBy('is_favorite', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'created_at_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $bookmarks = $query->paginate(15)->withQueryString(); 

        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $tags = Tag::where('user_id', $user->id)->orderBy('name')->get();

        $currentFilters = $request->only(['category', 'search', 'sort', 'favorites', 'tag']);

        return view('bookmarks.index', compact('bookmarks', 'categories', 'tags', 'currentFilters'));
    }


    public function create()
    {
        $this->authorize('create', Bookmark::class);
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark = new Bookmark();
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all(); // For Tagify whitelist
        return view('bookmarks.create', compact('categories', 'bookmark', 'userTags'));
    }

 
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
            'tags' => 'nullable|string|max:1000', 
        ]);

        $bookmark = new Bookmark();
        $bookmark->user_id = $user->id;
        $bookmark->title = $validated['title'];
        $bookmark->url = $validated['url'];
        $bookmark->description = $validated['description'] ?? null;
        $bookmark->notes = $validated['notes'] ?? null;
        $bookmark->category_id = $validated['category_id'] ?? null;

        $bookmark->save(); 

        $this->syncTags($user, $bookmark, $validated['tags'] ?? null);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark added successfully!');
    }


    public function show(Bookmark $bookmark)
    {
        $this->authorize('view', $bookmark);
        return redirect()->route('bookmarks.edit', $bookmark);
    }


    public function edit(Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $user = $bookmark->user;
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        $bookmark->loadMissing('tags'); 
        $userTags = Tag::where('user_id', $user->id)->pluck('name')->all(); // For Tagify whitelist
        return view('bookmarks.edit', compact('bookmark', 'categories', 'userTags'));
    }

  
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
                Rule::exists('categories', 'id')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
             ],
            'tags' => 'nullable|string|max:1000', 
        ]);

      
        $bookmarkData = collect($validated)->except('tags')->toArray();
        $bookmark->update($bookmarkData);


        $this->syncTags($user, $bookmark, $validated['tags'] ?? null);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully!');
    }

   
    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('delete', $bookmark);
        $bookmark->delete();
        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted successfully!');
    }

    
    public function toggleFavorite(Request $request, Bookmark $bookmark)
    {
        $this->authorize('toggleFavorite', $bookmark);

        $bookmark->is_favorite = !$bookmark->is_favorite;
        $bookmark->save();

        if ($request->expectsJson()) {
             return response()->json([
                 'success' => true,
                 'is_favorite' => $bookmark->is_favorite,
                 'message' => $bookmark->is_favorite ? 'Bookmark added to favorites.' : 'Bookmark removed from favorites.'
             ]);
        }

        return back()->with('success', 'Favorite status updated.');
    }

    private function syncTags(User $user, Bookmark $bookmark, ?string $tagsInput): void
    {
        if (is_null($tagsInput) || trim($tagsInput) === '') {
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

            $tag = Tag::firstOrCreate(
                ['user_id' => $user->id, 'slug' => $slug], 
                ['name' => $tagName] 
            );
            $tagIds[] = $tag->id;
        }

        $bookmark->tags()->sync($tagIds);
    }
}