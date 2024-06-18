<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeSlotAssignment;


class TimeSlotAssignmentController extends Controller
{
    public function index()
    {
        $assignments = TimeSlotAssignment::with('seminar', 'timeSlot')->get();
        return response()->json($assignments);
    }

    public function store(Request $request)
    {
        $assignment = TimeSlotAssignment::create($request->all());
        return response()->json($assignment, 201);
    }

    public function update(Request $request, $id)
    {
        $assignment = TimeSlotAssignment::findOrFail($id);
        $assignment->update($request->all());
        return response()->json($assignment);
    }

    public function destroy($id)
    {
        TimeSlotAssignment::destroy($id);
        return response()->json(null, 204);
    }
}
