<?php

namespace App\Http\Controllers;


use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function suggest(Request $request)
    {
        $search = $request->get('search', '');
        $tags = Tag::where('name', 'LIKE', "%{$search}%")
            ->pluck('name');

        return response()->json($tags);
    }
}
