<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Speaker; // Make sure to use your actual model
use Illuminate\Support\Facades\Storage;
use Log;


class SpeakerController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'short_description' => 'required',
            'long_description' => 'required',
            'image' => 'required|image', // Validate that it's an image
            'social_links' => 'required'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/speakers'); // Store the image
            $validatedData['image_url'] = Storage::url($imagePath); // Get the URL of the stored image
        }

        $speaker = Speaker::create($validatedData);

        return response()->json($speaker, 201);
    }

    public function index()
    {
        $speakers = Speaker::all()->map(function ($speaker) {
            // Correctly set the imageUrl
            $speaker->imageUrl = asset($speaker->image_url);
            return $speaker;
        });
        return response()->json($speakers);
    }


    public function show(Speaker $speaker)
    {
        return response()->json($speaker);
    }

    public function destroy(Speaker $speaker)
    {
        // Delete the image file from local storage
        Storage::delete($speaker->image_url);

        // Then delete the speaker record
        $speaker->delete();

        return response()->json(['message' => 'Speaker and image deleted successfully']);
    }

    public function update(Request $request, Speaker $speaker)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'short_description' => 'required',
            'long_description' => 'required',
            'image' => 'sometimes|image', // Validate that it's an image if provided
            'social_links' => 'required'
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image file from local storage
            Storage::delete($speaker->image_url);

            // Store the new image
            $imagePath = $request->file('image')->store('public/speakers');
            $validatedData['image_url'] = Storage::url($imagePath);
        }

        $speaker->update($validatedData);

        return response()->json($speaker);
    }



}
