<?php

namespace App\Providers;

use App\Models\Bookmark;
use App\Policies\BookmarkPolicy;
use App\Models\Category;
use App\Policies\CategoryPolicy;


use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

        Bookmark::class => BookmarkPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

   
    public function boot(): void
    {
        $this->registerPolicies();
    }
}