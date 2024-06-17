<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seminar; // Make sure to use your actual model
use Log;

class SeminarController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'speaker_id' => 'required|exists:speakers,id', // Ensure the speaker exists
            'short_description' => 'required',
            'long_description' => 'required',
        ]);

        $seminar = Seminar::create($validatedData);

        return response()->json($seminar, 201);
    }

    public function index()
    {
        $seminars = Seminar::all();
        return response()->json($seminars);
    }

    public function show(Seminar $seminar)
    {
        return response()->json($seminar);
    }

    public function destroy(Seminar $seminar)
    {
        // Then delete the seminar record
        $seminar->delete();

        return response()->json(['message' => 'Seminar deleted successfully']);
    }

    public function update(Request $request, Seminar $seminar)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'speaker_id' => 'required|exists:speakers,id',
            'short_description' => 'required',
            'long_description' => 'required',
        ]);

        // Update the seminar with the validated data
        $seminar->update($validatedData);

        return response()->json($seminar);
    }

}
