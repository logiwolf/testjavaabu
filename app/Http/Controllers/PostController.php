<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;





class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'explore']);
    }
    public function index(Request $request)
    {
        $featuredPost = Post::where('featured', true)->first();

        $query = Post::query();
        if ($featuredPost) {
            $query->where('id', '!=', $featuredPost->id);
        }

        $loadMore  = (int) $request->get('load_more', 1);
        $takeCount = 3 + (5 * $loadMore);

        $allPosts = $query->latest()
            ->take($takeCount)
            ->get();

        $recentPosts  = $allPosts->take(3);
        $explorePosts = $allPosts->slice(3);

        $totalCount = $query->count();
        $hasMore    = $takeCount < $totalCount;

        //trending post
        $popularPosts = Post::popular()->take(8)->get();

        return view('home', compact(
            'featuredPost',
            'recentPosts',
            'explorePosts',
            'loadMore',
            'hasMore',
            'popularPosts'
        ));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|min:3',
            'body'     => 'required|min:25',
            'image'    => 'required|image|max:2048',
            'featured' => 'nullable|boolean',
            'tags'     => 'required',
        ]);

        if ($request->boolean('featured')) {
            Post::where('featured', true)->update(['featured' => false]);
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public'); // ✅ stores image
        }

        $post = Post::create([
            'user_id'    => Auth::id(),
            'title'      => $validated['title'],
            'body'       => $validated['body'],
            'image_path' => $imagePath ?? null,
            'featured'   => $request->boolean('featured'),
            'views'      => 0,
            'like_count' => 0,
        ]);

        // Store tags
        $tags = collect(json_decode($request->input('tags', '[]')))
            ->map(function ($tag) {
                return \App\Models\Tag::firstOrCreate(['name' => ltrim($tag->value, '#')])->id;
            });

        $post->tags()->sync($tags);

        return redirect()->route('posts.index')->with('success', 'Post created!');
    }

    public function show(Post $post)
    {


        //show related post
        $post->increment('views');
        $post->load('media');
        $relatedPosts = $post->relatedPosts();
        $popularPosts = Post::popular()->take(5)->get();

        //show similar post
        $similarPosts = Post::whereHas('tags', function ($q) use ($post) {
            return $q->whereIn('tag_id', $post->tags->pluck('id'));
        })
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();
        return view('posts.show', compact('post', 'relatedPosts', 'similarPosts', 'popularPosts'));
    }


    public function edit(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);
        return view('posts.edit', compact('post'));
    }




    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'title' => 'required|min:3',
            'body'     => 'required|min:25',
            'image' => 'nullable|image|max:2048',
            'featured' => 'nullable|boolean',
            'tags'     => 'required',

        ]);

        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],

        ]);

        //handle image if uploaded
        // Handle image update
        if ($request->hasFile('image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }

            $post->image_path = $request->file('image')->store('posts', 'public');
        } elseif ($request->has('old_image')) {
            // Keep the old image if no new one uploaded
            $post->image_path = $request->input('old_image');
        }

        $post->save();

        // Handle tags
        $tags = collect(json_decode($request->input('tags', '[]')))
            ->map(function ($tag) {
                return Tag::firstOrCreate(['name' => ltrim($tag->value, '#')])->id;
            });

        $post->tags()->sync($tags);

        return redirect()->route('posts.index');
    }

    public function editFeatured()
    {
        $post = Post::where('featured', true)->firstOrFail();

        abort_if($post->user_id !== Auth::id(), 403);

        return view('posts.edit-featured', compact('post'));
    }

    public function updateFeatured(Request $request)
    {
        $post = Post::where('featured', true)->firstOrFail();

        abort_if($post->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'title' => 'required|min:3',
            'body'     => 'required|min:25',
            'image'    => 'required|image|max:2048',
            'featured' => 'nullable|boolean',
            'tags'     => 'required',
        ]);

        $post->title = $validated['title'];
        $post->body = $validated['body'];

        if ($request->hasFile('image')) {
            if ($post->image_path && Storage::disk('public')->exists($post->image_path)) {
                Storage::disk('public')->delete($post->image_path);
            }

            $imagePath = $request->file('image')->store('posts', 'public');
            $post->image_path = $imagePath;
        }

        $post->save();

        $tags = collect(json_decode($request->input('tags', '[]')))
            ->map(function ($tag) {
                return Tag::firstOrCreate(['name' => ltrim($tag->value, '#')])->id;
            });

        $post->tags()->sync($tags);

        return redirect()->route('posts.index')->with('success', 'Featured post updated.');
    }


    public function destroy(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);
        $post->delete();
        return redirect()->route('posts.index');
    }

    public function toggleLike(Post $post)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $liked = $post->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $post->likes()->where('user_id', $user->id)->delete();
            $post->decrement('like_count');
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $post->increment('like_count');
        }

        return response()->json([
            'liked' => !$liked,
            'count' => $post->like_count
        ]);
    }

    public function like(Post $post)
    {
        // Increment the like count for the post
        $post->increment('like_count');

        // Return JSON response with success and updated count
        return response()->json([
            'success' => true,
            'count' => $post->like_count,
        ]);
    }

    public function explore(Request $request)
    {
        $query = Post::query();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date from
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter by date to
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort by created_at date
        if ($request->filled('date') && in_array($request->date, ['asc', 'desc'])) {
            $query->orderBy('created_at', $request->date);
        } else {
            $query->latest();
        }

        $posts = $query->paginate(6)->withQueryString();

        // All users who wrote posts for the filter dropdown
        $users = User::has('posts')->orderBy('name')->get();

        return view('explore-all', compact('posts', 'users'));
    }
}
