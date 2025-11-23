<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\ReliefCenter;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Log;

class ApplyController extends Controller
{
    public function indexCenter($userId)
    {
        $user_id = Auth::user()->user_id;
        $center = ReliefCenter::where('user_id', $userId)->first();

        $existingVolunteer = Volunteer::where('user_id', $user_id)
        ->where('center_id', $center->center_id)
        ->first();

        if ($existingVolunteer) {
            return response()->json(['message' => 'You have already applied or are volunteering here.'], 400);
        }

        $alreadyAccepted = Volunteer::where('user_id', $user_id)
            ->where('approval_status', 'accepted') 
            ->first();

        if ($alreadyAccepted) {
            return response()->json(['message' => 'You are already accepted as a volunteer at another center.'], 400);
        }

        Log::info('Applying volunteer', ['user_id' => $user_id, 'center_id' => $center->center_id]);
        $volunteer = new Volunteer();
        $volunteer->user_id = $user_id;
        $volunteer->center_id = $center->center_id;
        $volunteer->approval_status = 'pending';
        $volunteer->status = 'inactive';
        $volunteer->save();

        return response()->json(['success' => 'Applied successfully!']);
    }
    public function indexOrg($userId)
    {
        $user_id = Auth::user()->user_id;
        $org = Organization::where('user_id', $userId)->first();

        $existingVolunteer = Volunteer::where('user_id', $user_id)
        ->where('org_id', $org->org_id)
        ->first();

        if ($existingVolunteer) {
            return response()->json(['message' => 'You have already applied or are volunteering here.'], 400);
        }

        $alreadyAccepted = Volunteer::where('user_id', $user_id)
            ->where('approval_status', 'accepted') 
            ->first();

        if ($alreadyAccepted) {
            return response()->json(['message' => 'You are already accepted as a volunteer at another organization.'], 400);
        }

        Log::info('Applying volunteer', ['user_id' => $user_id, 'org_id' => $org->org_id]);
        $volunteer = new Volunteer();
        $volunteer->user_id = $user_id;
        $volunteer->org_id = $org->org_id;
        $volunteer->approval_status = 'pending';
        $volunteer->status = 'inactive';
        $volunteer->save();

        return response()->json(['success' => 'Applied successfully!']);
    }
}