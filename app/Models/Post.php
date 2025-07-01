<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;





class Post extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;


    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($post) {
            $post->slug = static::generateUniqueSlug($post->title);
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function relatedPosts()
    {
        return Post::whereHas('tags', function ($q) {
            $q->whereIn('tags.id', $this->tags->pluck('id'));
        })->where('id', '!=', $this->id)->limit(5)->get();
    }
    public function likes()
    {
        return $this->hasMany(\App\Models\PostLike::class);
    }




    public function scopePopular($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderByDesc('like_count')
            ->orderByDesc('views');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
