<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CContributionController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index($userId)
    {
        $query = Contribution::with(['reliefCenter', 'reliefCenter.user', 'user', 'volunteer.user', 'organization.user'])
            ->orderBy('created_at', 'desc');

        $contributions = $query->where('center_id', function ($subQuery) use ($userId) {
            $subQuery->select('center_id')
                ->from('relief_centers')
                ->where('user_id', $userId)
                ->limit(1);
        })->get();
        Log::info('CContributionController index called', [
            'userId' => $userId,
            'contributions' => $contributions
        ]);
        
        return response()->json([
            'data' => $contributions
        ]);
    }
}