<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like(Request $request, Post $post)
    {
        $user = Auth::user();
        $ip = $request->ip();

        //  Checking if user or IP has already liked
        $alreadyLiked = $post->likes()
            ->where(function ($query) use ($user, $ip) {
                $query->when($user, fn($q) => $q->where('user_id', $user->id))
                    ->when(!$user, fn($q) => $q->where('guest_ip', $ip));
            })->exists();

        if ($alreadyLiked) {
            return response()->json(['liked' => true, 'count' => $post->like_count]);
        }

        // Add like
        $post->likes()->create([
            'user_id' => $user?->id,
            'guest_ip' => $user ? null : $ip,
        ]);

        $post->increment('like_count');

        return response()->json(['liked' => true, 'count' => $post->like_count]);
    }
}
