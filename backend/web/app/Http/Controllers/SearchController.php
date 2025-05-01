<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $query = User::whereIn('role', ['Relief Center', 'Organization', 'Volunteer'])
                    ->whereNot('user_id', Auth::id()); // also changed 'user_id' to 'id' assuming it's the primary key

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
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Location filter
        if ($request->filled('latitude') && $request->filled('longitude') && $request->filled('radius')) {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius');

            $query->whereHas('profile', function($q) use ($latitude, $longitude, $radius) {
                $q->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) 
                        * cos(radians(latitude)) 
                        * cos(radians(longitude) - radians(?))
                    ) + sin(radians(?)) 
                    * sin(radians(latitude)))
                    < ?
                ", [$latitude, $longitude, $latitude, $radius]);
            });
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

        $results = $query->paginate(10);

        return view('search', [
            'results' => $results,
            'searchParams' => $request->all(),
            'isInitialLoad' => false
        ]);
    }
}