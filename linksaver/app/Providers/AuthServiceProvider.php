<?php

namespace App\Providers;

// Add these lines for your policies:
use App\Models\Bookmark;
use App\Policies\BookmarkPolicy;
use App\Models\Category;
use App\Policies\CategoryPolicy;

// Uncomment the line below if you need to define Gates later
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // Default example line

        // --- Your Policy Mappings ---
        Bookmark::class => BookmarkPolicy::class,
        Category::class => CategoryPolicy::class,
        // --- End Policy Mappings ---
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Here you can define your authorization gates using Gate::define() if needed
        // Example:
        // Gate::define('edit-settings', function (User $user) {
        //     return $user->isAdmin();
        // });
    }
}