<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ReliefCenter;
use Illuminate\Http\Request;
class VContributionController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request, $userId)
    {
        $query = Contribution::with(['volunteer'])
            ->orderBy('created_at', 'desc');

        $contributions = $query->where('volunteer_id', function ($subQuery) use ($userId) {
            $subQuery->select('volunteer_id')
                ->from('volunteers')
                ->where('user_id', $userId)
                ->limit(1);
        })->paginate(10);
        
        return view('contributionV', compact('contributions'));
    }

    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->route('contributionv.index')->with('success', 'Contribution deleted successfully!');
    }
}