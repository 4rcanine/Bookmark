<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * (Less likely needed as we mainly list/create/delete)
     */
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * (Add this if you implement category editing later)
     */
    // public function update(User $user, Category $category): bool
    // {
    //     return $user->id === $category->user_id;
    // }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    // Add restore/forceDelete if using soft deletes for categories
}