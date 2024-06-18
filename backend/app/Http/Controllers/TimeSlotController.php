<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeSlot;
use App\Models\Stage;

class TimeSlotController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $timeSlot = TimeSlot::create($validatedData);

        return response()->json($timeSlot, 201);
    }

    public function index()
    {
        $timeSlots = TimeSlot::with('stage')->get();
        return response()->json($timeSlots);
    }

    public function show(TimeSlot $timeSlot)
    {
        return response()->json($timeSlot->load('stage'));
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        $validatedData = $request->validate([
            'stage_id' => 'required|exists:stages,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $timeSlot->update($validatedData);

        return response()->json($timeSlot);
    }

    public function destroy(TimeSlot $timeSlot)
    {
        $timeSlot->delete();

        return response()->json(['message' => 'Time slot deleted successfully']);
    }
}

