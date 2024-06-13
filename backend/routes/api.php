<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\SessionController;
use App\Models\User;
use App\Http\Controllers\SessionUserController;

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
// Routes for authentication and registration
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/session', [SessionController::class, 'create']);
Route::get('/session', [SessionController::class, 'index']);
Route::post('/session_users', [SessionUserController::class, 'store']); // POST route for storing session users
Route::get('/session_users', [SessionUserController::class, 'index']); // GET route for fetching session users

Route::middleware('auth:sanctum')->group(function () {
    // Route for creating API tokens
    Route::post('/tokens/create', [ApiTokenController::class, 'create']);

    // Route for retrieving user details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route for retrieving all users
    Route::get('/users', function () {
        // Fetch all users
        $users = User::all();

        // Return users as JSON response
        return response()->json($users);
    });

    // Route for setting user as sponsor
    Route::put('/users/{userId}/set-sponsor', function ($userId) {
        // Find the user by ID
        $user = User::find($userId);

        // If user exists
        if ($user) {
            // Toggle is_sponsor value
            $user->is_sponsor = $user->is_sponsor == 1 ? 0 : 1;
            $user->save();

            // Return success message
            return response()->json(['message' => 'User sponsor status updated'], 200);
        } else {
            // If user not found, return error
            return response()->json(['error' => 'User not found'], 404);
        }
    });

    // Route for accessing admin-specific features
    Route::post('/admin', function (Request $request) {
        // Check if the authenticated user is an admin
        $user = Auth::user();
        if ($user && $user->is_admin) {
            // If user is admin, return user details
            return $user;
        } else {
            // If user is not admin, return 403 Forbidden status
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
    });
});
