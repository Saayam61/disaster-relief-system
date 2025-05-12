<?php

namespace App\Http\Controllers\Admin;

use App\Models\NewsFeed;
use App\Models\ReliefCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsFeed::query();
        $reliefCenters = ReliefCenter::all();

        if ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.posts', compact('posts', 'reliefCenters'));
    }

    public function destroy(NewsFeed $post)
    {
        $post->delete();

        return redirect()->route('admin.posts')->with('success', 'Post deleted successfully!');
    }
}