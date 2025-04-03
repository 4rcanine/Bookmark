<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str; // Import Str facade for slug generation

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Add user_id
        'name',
        'slug',
    ];

    /**
     * The "booted" method of the model.
     * Automatically generate slug from name if slug is not set.
     */
    protected static function booted(): void
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
            // Ensure slug uniqueness per user within this creating event (might need adjustment if high concurrency)
             $count = static::where('user_id', $tag->user_id)->where('slug', $tag->slug)->count();
             if ($count > 0) {
                 // Append a number if slug already exists for this user
                 $tag->slug .= '-' . ($count + 1);
                 // Ideally, add more robust unique slug generation logic if needed
             }

        });

         static::updating(function (Tag $tag) {
             if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                 // If name changes, regenerate slug (consider implications if slug is used in URLs)
                 // $tag->slug = Str::slug($tag->name);
                 // For simplicity, maybe disallow slug changes or handle separately
             }
         });
    }


    /**
     * Get the user that owns the tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The bookmarks that belong to the tag.
     */
    public function bookmarks(): BelongsToMany
    {
        // Specify the pivot table name if it doesn't follow convention
        // ->using(PivotModel::class) if you have a custom pivot model
        // ->withTimestamps() if the pivot table has timestamps
        return $this->belongsToMany(Bookmark::class, 'bookmark_tag');
    }
}