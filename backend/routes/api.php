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
use App\Http\Controllers\PageController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\SeminarController;
use App\Models\Sponsor;
use App\Http\Controllers\StageController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\TimeSlotAssignmentController;
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
Route::post('/session', [SessionController::class, 'create']);
Route::get('/session', [SessionController::class, 'index']);
Route::post('/session_users', [SessionUserController::class, 'store']);
Route::get('/session_users', [SessionUserController::class, 'index']);
Route::post('/create-gallery', [GalleryController::class, 'create']);
Route::get('/galleries/{id}/years', [GalleryController::class, 'getYears']);
Route::post('/upload-image', [GalleryController::class, 'uploadImage']);
Route::get('/images', [GalleryController::class, 'getImages']);



Route::post('/create-pages', [PageController::class, 'store']);
Route::get('/pages', [PageController::class, 'index']);
Route::get('/page/{slug}', [PageController::class, 'show']);
Route::put('/page/{id}', [\App\Http\Controllers\PageController::class, 'update']);
Route::delete('/page/{id}', [\App\Http\Controllers\PageController::class, 'delete']);


Route::get('/about-us', [\App\Http\Controllers\AboutUsController::class, 'index']);
Route::post('/about-us', [\App\Http\Controllers\AboutUsController::class, 'store']);
Route::put('/about-us/{id}', [\App\Http\Controllers\AboutUsController::class, 'update']);
Route::delete('/about-us/{id}', [\App\Http\Controllers\AboutUsController::class, 'delete']);

Route::post('/speaker', [SpeakerController::class, 'store']);
Route::get('/speakers', [SpeakerController::class, 'index']);
Route::get('/speakers/{speaker}', [SpeakerController::class, 'show']);
Route::put('/speakers/{speaker}', [SpeakerController::class, 'update']);
Route::delete('/speakers/{speaker}', [SpeakerController::class, 'destroy']);

Route::get('/seminars', [SeminarController::class, 'index']);
Route::post('/seminars', [SeminarController::class, 'store']);
Route::get('/seminars/{seminar}', [SeminarController::class, 'show']);
Route::put('/seminars/{seminar}', [SeminarController::class, 'update']);
Route::delete('/seminars/{seminar}', [SeminarController::class, 'destroy']);

Route::post('/stages', [StageController::class, 'store']);
Route::get('/stages', [StageController::class, 'index']);
Route::get('/stages/{stage}', [StageController::class, 'show']);
Route::put('/stages/{stage}', [StageController::class, 'update']);
Route::delete('/stages/{stage}', [StageController::class, 'destroy']);

Route::post('/time-slots', [TimeSlotController::class, 'store']);
Route::get('/time-slots', [TimeSlotController::class, 'index']);
Route::get('/time-slots/{timeSlot}', [TimeSlotController::class, 'show']);
Route::put('/time-slots/{timeSlot}', [TimeSlotController::class, 'update']);
Route::delete('/time-slots/{timeSlot}', [TimeSlotController::class, 'destroy']);

Route::get('/pages/{slug}', [\App\Http\Controllers\PageController::class, 'show' ]);
Route::get('/galleries', [GalleryController::class, 'index']);

Route::get('/time-slot-assignments', [TimeSlotAssignmentController::class, 'index']);
Route::post('/time-slot-assignments', [TimeSlotAssignmentController::class, 'store']);
Route::put('/time-slot-assignments/{id}', [TimeSlotAssignmentController::class, 'update']);
Route::delete('/time-slot-assignments/{id}', [TimeSlotAssignmentController::class, 'destroy']);

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
