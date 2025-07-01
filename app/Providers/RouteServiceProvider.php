<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::bind('post', function ($value) {
  return Post::where('slug', $value)->firstOrFail();
});
