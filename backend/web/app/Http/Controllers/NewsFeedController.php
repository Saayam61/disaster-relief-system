<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NewsFeed;
use App\Models\ReliefCenter;

class NewsFeedController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userLat = $user->latitude;
        $userLng = $user->longitude;

        $allPosts = NewsFeed::with(['reliefCenter.user'])->get();

        $grouped = $allPosts->groupBy('center_id');

        // Take latest 3 posts per center
        $filtered = $grouped->flatMap(function ($posts) {
            return $posts->sortByDesc('created_at')->take(3);
        });

        $filtered = $filtered->map(function ($post) use ($userLat, $userLng) {
            $centerUser = $post->reliefCenter->user;
            $distance = sqrt(pow($centerUser->latitude - $userLat, 2) + pow($centerUser->longitude - $userLng, 2));
            $post->distance = $distance;
            return $post;
        });

        // Sort posts by distance asc, then by created_at desc
        $sorted = $filtered->sort(function ($a, $b) {
            if ($a->distance == $b->distance) {
                return $b->created_at->timestamp <=> $a->created_at->timestamp;
            }
            return $a->distance <=> $b->distance;
        });

        return view('newsFeed', ['posts' => $sorted]);
    }
}