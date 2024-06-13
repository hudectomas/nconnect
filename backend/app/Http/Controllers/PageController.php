<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $slug = \Str::slug($request->title);

        $page = Page::create([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => $slug,
        ]);

        return response()->json($page, 201);
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('page', compact('page'));
    }
}

