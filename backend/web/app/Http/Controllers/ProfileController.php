<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NewsFeed;
use App\Models\ReliefCenter;

class ProfileController extends Controller
{
    public function index($centerId = null)
{
    // Visiting a specific relief center profile
    if ($centerId) {
        $reliefCenter = ReliefCenter::find($centerId);

        if (!$reliefCenter) {
            abort(404, 'Relief center not found.');
        }
    } 
    // Logged-in relief center visiting their own profile
    else {
        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->first();

        if (!$reliefCenter) {
            return redirect()->route('home')->with('error', 'You are not a relief center.');
        }
    }

    $posts = NewsFeed::where('center_id', $reliefCenter->center_id)
                ->with('reliefCenter.user')
                ->orderBy('created_at', 'desc')
                ->get();

    return view('profile', compact('posts', 'reliefCenter'));
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

        $posts = NewsFeed::latest()->with('reliefCenter')->get();

        return view('profile', compact('post', 'posts'));
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
        
        return redirect()->route('profile.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(NewsFeed $post)
    {
        $post->delete();

        return redirect()->route('profile.index')
            ->with('success', 'Post deleted successfully!');
    }
}