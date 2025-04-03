<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the user's categories.
     */
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('categories.index', compact('categories')); // We'll create this view
    }

    /**
     * Store a newly created category in storage.
     * (We'll trigger this from the category index page for simplicity)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique category name per user
                Rule::unique('categories')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
        ]);

        $category = new Category($validated);
        $category->user_id = $user->id;
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }


    /**
     * Remove the specified category from storage.
     * Note: Bookmarks in this category will have their category_id set to null
     * due to the 'onDelete('set null')' constraint in the migration.
     */
    public function destroy(Category $category)
    {
        // Authorize: Ensure category belongs to the logged-in user
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }

    // Add edit/update methods if you want full category CRUD later
}