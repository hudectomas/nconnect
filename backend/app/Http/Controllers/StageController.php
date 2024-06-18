<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stage;

class StageController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $stage = Stage::create($validatedData);

        return response()->json($stage, 201);
    }

    public function index()
    {
        $stages = Stage::all();
        return response()->json($stages);
    }

    public function show(Stage $stage)
    {
        return response()->json($stage);
    }

    public function update(Request $request, Stage $stage)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $stage->update($validatedData);

        return response()->json($stage);
    }

    public function destroy(Stage $stage)
    {
        $stage->delete();

        return response()->json(['message' => 'Stage deleted successfully']);
    }
}

