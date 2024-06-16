<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::all(); // Predpokladá sa, že máte model Gallery

        return response()->json($galleries);
    }

    public function getYears($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);
            $years = $gallery->years; // Predpokladáme, že máte reláciu years definovanú v modeli Gallery

            return response()->json($years);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ročníky pre galériu neboli nájdené.'], 404);
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'years' => 'required|array', // Požaduje pole
            'years.*' => 'integer|min:1900|max:9999', // Požaduje integer hodnoty v rozsahu 1900 až 9999
            // Ďalšie validácie podľa potreby
        ]);

        // Vytvorenie nového záznamu v databáze pre galériu
        $gallery = new Gallery();
        $gallery->name = $request->name;
        $gallery->years = json_encode($request->years); // Uloženie poľa ročníkov ako JSON reťazec

        $gallery->save();

        return response()->json(['message' => 'Galéria bola úspešne vytvorená.', 'gallery' => $gallery], 200);
    }
    // Kontrolér pre upload obrázku
    // Kontrolér pre upload obrázku
    // Kontrolér pre upload obrázku
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

        // Uloženie obrázka do priečinka 'storage/app/public/images/{galleryName}/{years}'
        $imagePath = $image->store('public/images/' . $galleryName . '/' . implode('_', $years));

        // Ak chcete získať verejnú URL adresu k uloženému obrázku, môžete ju získať pomocou helpera asset()
        $imageUrl = asset(str_replace('public/', 'storage/', $imagePath));

        // Uloženie údajov do databázy alebo iná logika
        // ...

        return response()->json(['message' => 'Obrázok bol úspešne nahratý.', 'image_url' => $imageUrl]);
    }



}
