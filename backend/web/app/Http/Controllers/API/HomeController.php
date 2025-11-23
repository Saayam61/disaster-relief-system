<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function fetchCurrentVolunteer()
    {
        $user = Auth::user();
        $vol = User::where('user_id', $user->user_id)->first();
        $query = Volunteer::with(['reliefCenter.user', 'organization.user'])
            ->orderBy('created_at', 'desc');
        $volunteer = $query->where('user_id', $user->user_id)->get();
        Log::info('Fetched current volunteer', ['user_id' => $user->user_id, 'data'=> $volunteer]);

        return response()->json([$volunteer]);
    }
    public function updateVolunteer(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'skills' => 'required',
            'availability' => 'required',
            'status' => 'required',
        ]);

        $data = [
            'skills' => $request->skills,
            'availability' => $request->availability,
            'status' => $request->status,
        ];

        // $user->save();
        Volunteer::where('user_id', $user->user_id)->update($data);

        return response()->json(['success' => 'Volunteer info updated successfully!']);
    }

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
    public function searchChat()
    {
        $currentUserId = Auth::id();

        $users = User::join('communications as c', function ($join) use ($currentUserId) {
            $join->on('users.user_id', '=', 'c.receiver_id')
                ->where('c.sender_id', '=', $currentUserId)
                ->orOn(function ($query) use ($currentUserId) {
                    $query->on('users.user_id', '=', 'c.sender_id')
                        ->where('c.receiver_id', '=', $currentUserId);
                });
        })
        ->select('users.user_id', 'users.name', \DB::raw('MAX(c.timestamp) as last_message_time'))
        ->groupBy('users.user_id', 'users.name')
        ->orderByDesc('last_message_time')
        ->get();
        // dd($users);
        return response()->json($users);
    }
}