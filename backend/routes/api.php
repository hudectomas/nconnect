<?php

use App\Http\Controllers\ApiTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/tokens/create', [ApiTokenController::class, 'create']);
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/admin', function (Request $request) {
    return $request->user(); // Return user details if they are authenticated as admin
})->middleware('auth:sanctum');
