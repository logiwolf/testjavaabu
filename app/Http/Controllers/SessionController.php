<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class SessionController extends Controller
{
    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3',
            'body' => 'required',
            'created_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'created_at' => $validated['created_at'] ?? now(),
        ]);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')
                ->usingName($post->title)
                ->toMediaCollection('images');
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }
}
