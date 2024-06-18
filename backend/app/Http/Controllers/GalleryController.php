<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\Image;


class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::all();
        return response()->json($galleries);
    }

    public function getImageUrl(Request $request)
    {
        $request->validate([
            'galleryName' => 'required|string',
            'year' => 'required|integer|min:1900|max:9999',
            'imageName' => 'required|string',
        ]);

        $galleryName = $request->input('galleryName');
        $year = $request->input('year');
        $imageName = $request->input('imageName');


        $gallery = Gallery::where('name', $galleryName)->first();

        if (!$gallery) {
            return response()->json(['error' => 'Galéria nebola nájdená.'], 404);
        }


        $imageNames = json_decode($gallery->image_names, true) ?? [];


        if (in_array($imageName, $imageNames)) {

            $imageUrl = asset('storage/images/' . $galleryName . '/' . $year . '/' . $imageName);
            return response()->json(['image_url' => $imageUrl]);
        } else {
            return response()->json(['error' => 'Obrázok s daným názvom nebol nájdený pre zvolenú galériu a rok.'], 404);
        }
    }

    public function getImages(Request $request)
    {
        $request->validate([
            'galleryName' => 'required|string',
            'year' => 'required|integer|min:1900|max:9999',
        ]);

        $galleryName = $request->input('galleryName');
        $year = $request->input('year');


        $gallery = Gallery::where('name', $galleryName)->first();

        if (!$gallery) {
            return response()->json(['error' => 'Galéria nebola nájdená.'], 404);
        }


        $imageNames = json_decode($gallery->image_names, true) ?? [];


        $images = [];


        foreach ($imageNames as $imageName) {
            $imageUrl = asset('storage/images/' . $galleryName . '/' . $year . '/' . $imageName);
            $images[] = ['image_url' => $imageUrl, 'image_name' => $imageName];
        }

        return response()->json($images);
    }


    public function getYears($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);
            $years = $gallery->years;
            return response()->json($years);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ročníky pre galériu neboli nájdené.'], 404);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'years' => 'required|array',
            'years.*' => 'integer|min:1900|max:9999',
        ]);

        $gallery = new Gallery();
        $gallery->name = $request->name;
        $gallery->years = json_encode($request->years);
        $gallery->save();

        return response()->json(['message' => 'Galéria bola úspešne vytvorená.', 'gallery' => $gallery], 200);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'gallery_id' => 'required|exists:galleries,id',
            'gallery_name' => 'required|string',
            'years' => 'required|array',
            'years.*' => 'integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $galleryId = $request->input('gallery_id');
        $galleryName = $request->input('gallery_name');
        $years = $request->input('years');
        $image = $request->file('image');
        $encryptedImageName = $image->hashName();


        $imagePath = $image->store('public/images/' . $galleryName . '/' . implode('_', $years));


        $imageUrl = asset(str_replace('public/', 'storage/', $imagePath));


        $gallery = Gallery::find($galleryId);
        $imageNames = json_decode($gallery->image_names, true) ?? [];
        $imageNames[] = $encryptedImageName;
        $gallery->image_names = json_encode($imageNames);
        $gallery->save();

        return response()->json(['message' => 'Obrázok bol úspešne nahratý.', 'image_url' => $imageUrl]);
    }
}
