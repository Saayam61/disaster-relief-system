<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        // Ensure the user is authenticated
        $user = Auth::user();
        
        if ($user) {
            // Validate the input data
            $validated = $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // Update the user's location in the database
            $user->latitude = $validated['latitude'];
            $user->longitude = $validated['longitude'];
            $user->save();

            // Return a response indicating success
            return response()->json(['success' => true]);
        }

        // If the user is not authenticated
        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }
}