<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::post('/api/pages', [PageController::class, 'store']);
Route::get('/page/{slug}', [PageController::class, 'show']);


Route::get('/', function () {
    return view('welcome');
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
