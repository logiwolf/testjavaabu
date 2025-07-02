<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Fake the public disk for file uploads
    }

    #[Test]
    public function user_can_create_a_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $image = UploadedFile::fake()->image('post.jpg');

        $response = $this->post('/posts', [
            'title' => 'Test Post',
            'body' => str_repeat('Body ', 6), // > 25 characters
            'image' => $image,
            'tags' => json_encode([
                ['value' => 'Laravel'],
                ['value' => 'PHP']
            ]),
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);

        Storage::disk('public')->exists('posts/' . $image->hashName());
    }

    #[Test]
    public function user_can_view_a_post(): void
    {
        /** @var Post $post */
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    #[Test]
    public function user_can_update_a_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Post $post */
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $image = UploadedFile::fake()->image('updated.jpg');

        $response = $this->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'body' => str_repeat('Updated Body ', 6),
            'image' => $image,
            'tags' => json_encode([
                ['value' => 'UpdatedTag']
            ]),
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', ['title' => 'Updated Title']);

        Storage::disk('public')->exists('posts/' . $image->hashName());
    }

    #[Test]
    public function user_can_delete_a_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Post $post */
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
