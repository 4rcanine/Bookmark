<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class CategoryController extends Controller
{
    use AuthorizesRequests; 

  
    public function index()
    {
        
        $user = Auth::user(); 
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
       
        $user = Auth::user(); 
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


  
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category); 

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
    }

}