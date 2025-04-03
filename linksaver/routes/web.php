<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController; // Add this
use App\Http\Controllers\CategoryController; // Add this
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Optional: Redirect logged-in users from home to bookmarks,
    // or show a landing page if not logged in.
    if (auth()->check()) {
        return redirect()->route('bookmarks.index');
    }
    return view('welcome'); // Or your landing page view
});


// Routes accessible only when logged in
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (optional, can redirect to bookmarks)
    Route::get('/dashboard', function () {
        return redirect()->route('bookmarks.index'); // Redirect dashboard to bookmarks list
        // return view('dashboard'); // Or keep the original dashboard
    })->name('dashboard');

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookmark Resource Routes (CRUD)
    Route::resource('bookmarks', BookmarkController::class); // index, create, store, show, edit, update, destroy

    // Custom Bookmark Routes
    Route::patch('/bookmarks/{bookmark}/toggle-favorite', [BookmarkController::class, 'toggleFavorite'])->name('bookmarks.toggleFavorite');

    // Category Routes (Simplified for now)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index'); // View categories
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');   // Store new category
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // Delete category

});


require __DIR__.'/auth.php'; // Keep Breeze auth routes