<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    
    public function viewAny(User $user): bool
    {
        return true;
    }

   
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

}