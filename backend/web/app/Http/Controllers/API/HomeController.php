<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10|unique:users,phone,' . $user->user_id . ',user_id',
            'email' => 'required|email|string|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'address' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ];

        // $user->save();
        User::where('user_id', $user->user_id)->update($data);

        return response()->json(['success' => 'User info updated successfully!']);
    }

    public function search(Request $request)
    {
        $query = User::whereIn('role', ['Relief Center', 'Organization', 'Volunteer'])
                    ->whereNot('user_id', Auth::id()); // also changed 'user_id' to 'id' assuming it's the primary key

        // Location filter
        $user = Auth::user();
        if ($request->filled('radius')) {
            $latitude = $user->latitude;
            $longitude = $user->longitude;
            $radius = $request->input('radius');
        
            $query->whereRaw("
                (6371 * acos(
                cos(radians(?)) 
                * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?))
                + sin(radians(?)) 
                * sin(radians(latitude))
                )) < ?
            ", [$latitude, $longitude, $latitude, $radius]);
        }

        // Text search if query is provided
        if ($request->filled('query')) {
            $searchTerm = $request->input('query');
            $searchTerms = explode(' ', $searchTerm);

            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    if (strlen(trim($term))) {
                        $q->orWhere('name', 'like', '%' . trim($term) . '%');
                    }
                }
            });
        }

        // Role filter
        if ($request->has('role') && in_array($request->role, ['Relief Center', 'Organization', 'Volunteer'])) {
            $query->where('role', $request->role);
        }

        $results = $query->with('contributions')
                        ->orderBy('name')
                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Search results fetched successfully',
            'data' => $results
        ]);
    }
}