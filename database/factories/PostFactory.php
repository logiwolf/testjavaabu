<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = \App\Models\Post::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);
        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'body' => fake()->paragraph(5),
            'views' => rand(0, 500),
            'like_count' => rand(0, 100),
            'image_path' => null
        ];
    }


    public function withImage()
    {
        return $this->state(function (array $attributes) {
            $filename = 'posts/' . uniqid() . '.jpg';

            // Download and store a random image
            Storage::disk('public')->put(
                $filename,
                file_get_contents('https://picsum.photos/600/400')
            );

            return [
                'image_path' => $filename,
            ];
        });
    }
    public function featured()
    {
        return $this->state(fn() => [
            'featured' => true,
        ]);
    }
}
