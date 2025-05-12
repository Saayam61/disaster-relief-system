<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contribution;
use App\Models\ReliefCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ContributionController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request)
    {
        $query = Contribution::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $contributions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.contributions', compact('contributions'));
    }

    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->route('admin.contributions')->with('success', 'Contribution deleted successfully!');
    }
}