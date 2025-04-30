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
    // Get all posts with their relief centers, ordered by newest first initially
    $posts = NewsFeed::with('reliefCenter')->latest()->get();

    // Get user's location
    $userLat = Auth::user()->latitude;
    $userLng = Auth::user()->longitude;

    // Calculate distances and prepare for sorting
    $postsWithDistance = $posts->map(function ($post) use ($userLat, $userLng) {
        $center = $post->reliefCenter;
        
        // Simple distance calculation (faster than Haversine)
        // This works fine for relative sorting
        $distance = sqrt(
            pow($center->latitude - $userLat, 2) + 
            pow($center->longitude - $userLng, 2)
        );

        return [
            'post' => $post,
            'distance' => $distance,
            'created_at' => $post->created_at->timestamp
        ];
    });

    // Custom sorting
    $sorted = $postsWithDistance->sort(function ($a, $b) {
        // First sort by distance (closer first)
        if ($a['distance'] != $b['distance']) {
            return $a['distance'] <=> $b['distance'];
        }
        
        // If same distance, sort by newest first
        return $b['created_at'] <=> $a['created_at'];
    });

    // Extract just the posts in the new order
    $sortedPosts = $sorted->pluck('post');

    return view('newsFeed', ['posts' => $sortedPosts]);
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('news_images', 'public');
        }

        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();

        NewsFeed::create([
            'center_id' => $reliefCenter->center_id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_url' => $path,
        ]);

        return back()->with('success', 'Post created!');
    }

    public function edit($id)
    {
        $post = NewsFeed::findOrFail($id);

        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();

        // Check if the post belongs to this relief center
        if ($reliefCenter->center_id !== $post->center_id) {
            abort(403, 'Unauthorized action.');
        }

        $posts = NewsFeed::latest()->with('reliefCenter')->get();

        return view('newsFeed', compact('post', 'posts'));
    }

    public function update(Request $request, NewsFeed $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('news_images', 'public');
        }

        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();
        $post->update([
            'center_id' => $reliefCenter->center_id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_url' => $path,
        ]);
        
        return redirect()->route('news-feed.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(NewsFeed $post)
    {
        $post->delete();

        return redirect()->route('news-feed.index')
            ->with('success', 'Post deleted successfully!');
    }
}