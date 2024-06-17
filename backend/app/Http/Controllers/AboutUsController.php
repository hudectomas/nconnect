<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AboutUs; // Uistite sa, že ste importovali správny model

class AboutUsController extends Controller
{
    public function index()
    {
        $aboutUs = AboutUs::all();
        return view('about-us.index', compact('aboutUs'));
    }

    public function create()
    {
        return view('about-us.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'photo' => 'required|string',
            'quote' => 'required|string',
        ]);

        AboutUs::create($validatedData);

        return redirect()->route('about-us.index');
    }

    public function edit($id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('about-us.edit', compact('aboutUs'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'photo' => 'required|string',
            'quote' => 'required|string',
        ]);

        $aboutUs = AboutUs::findOrFail($id);
        $aboutUs->update($validatedData);

        return redirect()->route('about-us.index');
    }

    public function show($id)
    {
        $aboutUs = AboutUs::findOrFail($id);
        return view('about-us.show', compact('aboutUs'));
    }

    // Implementovať metódu pre odstránenie

}
