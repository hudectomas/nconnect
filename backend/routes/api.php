<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ApiTokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes for authentication and registration
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Routes requiring authentication with Sanctum middleware
Route::middleware('auth:sanctum')->group(function () {
    // Route for creating API tokens
    Route::post('/tokens/create', [ApiTokenController::class, 'create']);
    
    // Route for retrieving user details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Route for accessing admin-specific features
    Route::post('/admin', function (Request $request) {
        // Check if the authenticated user has admin privileges
        if ($request->user()->isAdmin()) {
            // If user is admin, return user details
            return $request->user();
        } else {
            // If user is not admin, return 403 Forbidden status
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
    });
});
