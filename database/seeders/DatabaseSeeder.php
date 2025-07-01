<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Create 10 posts with images
        \App\Models\Post::factory()->count(20)->withImage()->create();

        Tag::factory(20)->create();
        Post::factory()->withImage()->featured()->create();
        Post::factory(30)->create()->each(function ($post) {
            $tags = Tag::inRandomOrder()->take(rand(1, 4))->pluck('id');
            $post->tags()->attach($tags);
        });
    }
}
