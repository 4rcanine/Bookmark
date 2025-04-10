<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController; 
use App\Http\Controllers\CategoryController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   
    if (auth()->check()) {
        return redirect()->route('bookmarks.index');
    }
    return view('welcome'); 
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('bookmarks.index'); 
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('bookmarks', BookmarkController::class); // index, create, store, show, edit, update, destroy

    Route::patch('/bookmarks/{bookmark}/toggle-favorite', [BookmarkController::class, 'toggleFavorite'])->name('bookmarks.toggleFavorite');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index'); // View categories
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');   // Store new category
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // Delete category

});


require __DIR__.'/auth.php'; 