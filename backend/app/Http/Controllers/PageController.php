<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return response()->json($pages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $slug = $this->slugify($request->title);

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
        return response()->json($page);
        // return view('page', compact('page'));
    }

    public function delete(int $id) : Response {
        Page::find($id)->delete();
        return response(status: 200);
    }

    private function slugify(string $text): string {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}