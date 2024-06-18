<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AboutUs; // Uistite sa, že ste importovali správny model
use Illuminate\Http\Response;

class AboutUsController extends Controller
{
    public function create()
    {
        return view('about-us.create');
    }

    public function index()
    {
        $aboutUs = AboutUs::all();
        return response()->json($aboutUs);;
    }

    public function store(Request $request): Response
    {
        $validatedData = $request->validate([
            'photo' => 'required|string',
            'quote' => 'required|string',
        ]);

        AboutUs::create($validatedData);

        return response(status: 200);
    }

    public function edit($id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('about-us.edit', compact('aboutUs'));
    }

    public function update(int $id, Request $request): Response
    {
        $validatedData = $request->validate([
            'photo' => 'required|string',
            'quote' => 'required|string',
        ]);

        $aboutUs = AboutUs::findOrFail($id);
        $aboutUs->update($validatedData);

        return response(status: 200);
    }

    public function delete(int $id): Response {
        $aboutUs = AboutUs::findOrFail($id);
        $aboutUs->delete();

        return response(status: 200);
    }

    public function show($id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('about-us.show', compact('aboutUs'));
    }

    // Implementovať metódu pre odstránenie

}
