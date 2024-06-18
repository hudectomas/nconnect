<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SessionUser;
use Illuminate\Support\Facades\Log;

class SessionUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $sessionUsers = SessionUser::all();
            return response()->json($sessionUsers, 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve session users.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to retrieve session users', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Request data:', $request->all());

        try {
            $request->validate([
                'session_id' => 'required|integer|exists:sessions,id',
                'user_id' => 'required|integer|exists:users,id',
                'session_name' => 'required|string|max:255',
                'user_name' => 'required|string|max:255',
                'capacity' => 'required|integer',
            ]);

            $existingSessionUser = SessionUser::where('session_id', $request->input('session_id'))
                ->where('user_id', $request->input('user_id'))
                ->first();

            if ($existingSessionUser) {
                $existingSessionUser->delete();
                return response()->json(['message' => 'User removed from session successfully'], 200);
            }

            $sessionUser = new SessionUser([
                'session_id' => $request->input('session_id'),
                'user_id' => $request->input('user_id'),
                'session_name' => $request->input('session_name'),
                'user_name' => $request->input('user_name'),
                'capacity' => $request->input('capacity'),
            ]);

            $sessionUser->save();

            return response()->json(['message' => 'User added to session successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Failed to add user to session.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to add user to session', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($session_id, $user_id)
    {
        try {
            $sessionUser = SessionUser::where('session_id', $session_id)
                                      ->where('user_id', $user_id)
                                      ->first();

            if (!$sessionUser) {
                return response()->json(['error' => 'Session user not found.'], 404);
            }

            $sessionUser->delete();

            return response()->json(['message' => 'Session user successfully unregistered.']);
        } catch (\Exception $e) {
            Log::error('Error deleting session user: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error. Please try again later.'], 500);
        }
    }
}
