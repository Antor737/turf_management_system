<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;

class SlotController extends Controller
{
    
    // All slots দেখাবে
    public function index(Request $request)
{
    if ($request->date) {
        return Slot::where('date', $request->date)->get();
    }

    return Slot::all();
}

    // Single slot create করবে
    public function store(Request $request)
{
    $request->validate([
        'time' => 'required|string',
        'date' => 'required|date',
    ]);

    $slot = Slot::create([
        'time' => $request->time,
        'date' => $request->date, // 🔥 এখানে add
        'is_booked' => false
    ]);

    return response()->json($slot, 201);
}

    // Slot book/unbook করা
    public function update(Request $request, $id)
{
    $slot = Slot::findOrFail($id);
    $userId = $request->user_id;

    if ($request->is_booked) {

        if ($slot->is_booked) {
            return response()->json(['message' => 'Already booked'], 400);
        }

        $slot->is_booked = true;
        $slot->user_id = $userId;
    } 
    else {

        if ($slot->user_id != $userId) {
            return response()->json(['message' => 'Not your slot'], 403);
        }

        $slot->is_booked = false;
        $slot->user_id = null;
    }

    $slot->save();

    return response()->json($slot);
}

    // Slot delete করা
    public function destroy($id)
    {
        $slot = Slot::findOrFail($id);
        $slot->delete();

        return response()->json(['message' => 'Slot deleted']);
    }

    
}