<?php

namespace App\Http\Controllers\Api;

use App\Models\Contribution;
use App\Models\ReliefCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContributionController extends Controller
{
    // GET /api/contributions/user/{userId}
    public function indexUser()
    {
        $userId = Auth::user()->user_id;
        $contributions = Contribution::with(['user', 'reliefCenter.user'])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();
// Log::info('Contribution Query Result:', [
//     'userId' => $userId,
//     'contributions' => $contributions
// ]);
        return response()->json([
            'data' => $contributions
        ]);
    }

    // DELETE /api/contributions/{id}
    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return response()->json(['message' => 'Contribution deleted successfully.']);
    }
}
