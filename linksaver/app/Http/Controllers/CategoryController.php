<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Keep for Auth::user()
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- ADDED THIS LINE

class CategoryController extends Controller
{
    use AuthorizesRequests; // <-- ADDED THIS LINE

    /**
     * Display a listing of the user's categories.
     */
    public function index()
    {
        // Authorization handled by 'auth' middleware
        // $this->authorize('viewAny', Category::class); // Optional explicit check

        $user = Auth::user(); // Still needed
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        // Authorization handled by 'auth' middleware
        // $this->authorize('create', Category::class); // Optional explicit check

        $user = Auth::user(); // Still needed
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
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
     */
    public function destroy(Category $category)
    {
        // Use Policy for authorization
        $this->authorize('delete', $category); // Checks CategoryPolicy::delete()

        // No need for the manual check anymore:
        // if ($category->user_id !== Auth::id()) {
        //     abort(403);
        // }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }

    // Add edit/update methods with appropriate authorize() calls if needed later
}