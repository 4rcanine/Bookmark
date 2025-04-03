<?php

namespace App\Policies;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookmarkPolicy
{
    /**
     * Determine whether the user can view any models.
     * (Anyone logged in can view their own list)
     */
    public function viewAny(User $user): bool
    {
        return true; // Or check for specific roles/permissions if needed later
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bookmark $bookmark): bool
    {
        // Does the bookmark's user_id match the logged-in user's id?
        return $user->id === $bookmark->user_id;
    }

    /**
     * Determine whether the user can create models.
     * (Anyone logged in can create)
     */
    public function create(User $user): bool
    {
         return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bookmark $bookmark): bool
    {
         return $user->id === $bookmark->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bookmark $bookmark): bool
    {
         return $user->id === $bookmark->user_id;
    }

    /**
     * Determine whether the user can toggle the favorite status.
     * (Custom action - needs a matching method name)
     */
    public function toggleFavorite(User $user, Bookmark $bookmark): bool
    {
         return $user->id === $bookmark->user_id;
    }


    // --- Methods below are less common for this specific app but good practice ---

    /**
     * Determine whether the user can restore the model. (For soft deletes)
     */
    // public function restore(User $user, Bookmark $bookmark): bool
    // {
    //     return $user->id === $bookmark->user_id;
    // }

    /**
     * Determine whether the user can permanently delete the model. (For soft deletes)
     */
    // public function forceDelete(User $user, Bookmark $bookmark): bool
    // {
    //     return $user->id === $bookmark->user_id; // Or restrict further, e.g., only admins
    // }
}