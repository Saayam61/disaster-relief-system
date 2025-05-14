<?php

namespace App\Http\Controllers\Organization;

use App\Models\Contribution;
use App\Models\ReliefCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ContributionController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request, $userId)
    {
        $query = Contribution::with(['organization'])
            ->orderBy('created_at', 'desc');

        $contributions = $query->where('org_id', function ($subQuery) use ($userId) {
            $subQuery->select('org_id')
                ->from('organizations')
                ->where('user_id', $userId)
                ->limit(1);
        })->paginate(10);
        
        return view('organization.contributions', compact('contributions'));
    }

    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->route('contributions.index')->with('success', 'Contribution deleted successfully!');
    }
}