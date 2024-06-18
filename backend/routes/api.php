<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SessionUserController;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GalleryController;
use App\Models\Sponsor;
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


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/session', [SessionController::class, 'create']);
Route::get('/session', [SessionController::class, 'index']);
Route::post('/session_users', [SessionUserController::class, 'store']);
Route::get('/session_users', [SessionUserController::class, 'index']);
Route::post('/create-gallery', [GalleryController::class, 'create']);
Route::get('/galleries/{id}/years', [GalleryController::class, 'getYears']);
Route::post('/upload-image', [GalleryController::class, 'uploadImage']);
Route::get('/images', [GalleryController::class, 'getImages']);




Route::get('/galleries', [GalleryController::class, 'index']);



Route::get('/sponsors', function () {
    $sponsors = Sponsor::all(['name']);
    $names = $sponsors->pluck('name');
    return response()->json($names);
});



Route::middleware('auth:sanctum')->group(function () {

    Route::post('/tokens/create', [ApiTokenController::class, 'create']);


    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::get('/users', function () {

        $users = User::all();


        return response()->json($users);
    });


    Route::put('/users/{userId}/set-sponsor', function ($userId) {

        $user = User::find($userId);
        if ($user) {

            if ($user->is_sponsor == 1) {

                DB::beginTransaction();

                try {

                    Sponsor::where('name', $user->name)->delete();


                    $user->is_sponsor = 0;
                    $user->save();


                    DB::commit();


                    return response()->json(['message' => 'User sponsor status updated'], 200);
                } catch (\Exception $e) {

                    DB::rollback();


                    return response()->json(['error' => 'Failed to update sponsor status'], 500);
                }
            }


            DB::beginTransaction();

            try {

                $sponsor = new Sponsor();
                $sponsor->name = $user->name;

                $sponsor->save();


                $user->is_sponsor = 1;
                $user->save();


                DB::commit();


                return response()->json(['message' => 'User sponsor status updated'], 200);
            } catch (\Exception $e) {

                DB::rollback();


                return response()->json(['error' => 'Failed to update sponsor status'], 500);
            }
        } else {

            return response()->json(['error' => 'User not found'], 404);
        }
    });




    Route::post('/admin', function (Request $request) {

        $user = Auth::user();
        if ($user && $user->is_admin) {

            return $user;
        } else {

            return response()->json(['error' => 'Unauthorized.'], 403);
        }
    });

    Route::get('/send-mail', function () {
        Mail::to('matuspohanka498@gmail.com')->send(new WelcomeMail());
        return response()->json(['message' => 'Email sent!']);
    });
});
