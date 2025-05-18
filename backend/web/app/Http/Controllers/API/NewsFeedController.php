<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NewsFeed;
use App\Models\ReliefCenter;
use App\Http\Controllers\Controller;

class NewsFeedController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userLat = $user->latitude;
        $userLng = $user->longitude;

        $allPosts = NewsFeed::with(['reliefCenter.user'])->get();

        $grouped = $allPosts->groupBy('center_id');

        // Latest 3 posts per center
        $filtered = $grouped->flatMap(function ($posts) {
            return $posts->sortByDesc('created_at')->take(3);
        });

        // Add distance
        $filtered = $filtered->map(function ($post) use ($userLat, $userLng) {
            $centerUser = $post->reliefCenter->user;
            $distance = sqrt(pow($centerUser->latitude - $userLat, 2) + pow($centerUser->longitude - $userLng, 2));
            $post->distance = $distance;
            return $post;
        });

        // Sort by distance then date
        $sorted = $filtered->sort(function ($a, $b) {
            if ($a->distance == $b->distance) {
                return $b->created_at->timestamp <=> $a->created_at->timestamp;
            }
            return $a->distance <=> $b->distance;
        })->values(); // reset index

        // Return as JSON for Flutter frontend
        return response()->json([
            $sorted
        ]);
    }
}
