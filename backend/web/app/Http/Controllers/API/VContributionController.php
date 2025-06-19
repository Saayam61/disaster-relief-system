<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\Request;
class VContributionController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index($userId)
    {
        $query = Contribution::with(['volunteer'])
            ->orderBy('created_at', 'desc');

        $contributions = $query->where('volunteer_id', function ($subQuery) use ($userId) {
            $subQuery->select('volunteer_id')
                ->from('volunteers')
                ->where('user_id', $userId)
                ->limit(1);
        })->get();
        
        return response()->json($contributions);
    }

    // public function destroy(Contribution $contribution)
    // {
    //     $contribution->delete();

    //     return redirect()->route('contributionv.index')->with('success', 'Contribution deleted successfully!');
    // }
}