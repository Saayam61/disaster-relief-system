<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $query = User::whereIn('role', ['Relief Center', 'Organization', 'Volunteer'])
                    ->whereNot('user_id', Auth::id());

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

        // Now paginate no matter what
        $results = $query->with('contributions')
                        ->orderBy('name')
                        ->paginate(10);

        return view('search', [
            'results' => $results,
            'searchParams' => $request->all(),
            'queryLoad' => $request->filled('query')
        ]);
    }


    public function search(Request $request)
    {
        $request->validate([
            'query' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:relief_center,organization,volunteer',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'radius' => 'sometimes|numeric|min:1|max:100',
        ]);

        $query = User::whereIn('role', ['relief_center', 'organization', 'volunteer'])
                    ->with('profile');

        // Text search if query is provided
        if ($request->filled('query')) {
            $searchTerm = $request->input('query');
            $searchTerms = explode(' ', $searchTerm);

            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    if(strlen(trim($term))) {
                        $q->orWhere('name', 'like', '%'.trim($term).'%');
                    }
                }
            });
        }

        // Role filter
        // if ($request->filled('role')) {
        //     $query->where('role', $request->input('role'));
        // }

        if ($request->has('role') && in_array($request->role, ['Relief Center', 'Organization', 'Volunteer'])) {
            $query->where('role', $request->role);
        }

        

        // Order results
        if ($request->filled('query')) {
            $query->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 0
                    WHEN name LIKE ? THEN 1
                    ELSE 2
                END
            ", [$request->input('query').'%', '%'.$request->input('query').'%']);
        } else {
            $query->orderBy('name');
        }

        $results = $query->get();

        return view('search', [
            'results' => $results,
            'searchParams' => $request->all(),
            'isInitialLoad' => false
        ]);
    }
}