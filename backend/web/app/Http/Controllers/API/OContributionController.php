<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\Request;
class OContributionController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index($userId)
    {
        $query = Contribution::with(['organization', 'reliefCenter.user', 'organization.user'])
            ->orderBy('created_at', 'desc');

        $contributions = $query->where('org_id', function ($subQuery) use ($userId) {
            $subQuery->select('org_id')
                ->from('organizations')
                ->where('user_id', $userId)
                ->limit(1);
        })->get();
        
        return response()->json([
            'data' => $contributions
        ]);
    }
}