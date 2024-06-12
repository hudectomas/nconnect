<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{

    /**
     * Create a new session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Log::info('Create session endpoint hit');
        try {
            $request->validate([
                'session_name' => 'required|string',
                'capacity' => 'required|integer',
                'start_time' => 'required|date', // Pridanie validácie pre start_time
                'end_time' => 'required|date|after:start_time', // Pridanie validácie pre end_time
            ]);

            Log::info('Validation passed.', ['request' => $request->all()]);

            $session = new Session([
                'session_name' => $request->session_name,
                'capacity' => $request->capacity,
                'start_time' => $request->start_time, // Použitie start_time z requestu
                'end_time' => $request->end_time, // Použitie end_time z requestu
                'user_agent' => $request->header('User-Agent', 'unknown'),
                'payload' => json_encode($request->all()),
                'last_activity' => now()->timestamp,
            ]);

            Log::info('Session model created.', ['session' => $session]);

            $session->save();

            Log::info('Session saved successfully.');

            return response()->json(['message' => 'Session created successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create session.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json(['error' => 'Failed to create session', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of the sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Získaj všetky sessions
        $sessions = Session::all();

        // Vráť sessions ako JSON response
        return response()->json($sessions);
    }
}
