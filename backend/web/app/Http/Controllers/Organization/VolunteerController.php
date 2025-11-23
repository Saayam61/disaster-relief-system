<?php

namespace App\Http\Controllers\Organization;

use App\Models\Organization;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class VolunteerController extends Controller
{
    /**
     * Display a listing of the contributions.
     */
    public function index(Request $request)
    {
        $org = Organization::where('user_id', Auth::id())->firstOrFail();
        
        // Build the query only for this relief center
        $query = Volunteer::with(['organization', 'user'])
        ->where('org_id', $org->org_id)
        ->orderBy('created_at', 'desc')
        ->join('users', 'volunteers.user_id', '=', 'users.user_id')
        ->select('volunteers.*');

        $user = Auth::user();
        if ($request->filled('radius')) {
            $latitude = $user->latitude;
            $longitude = $user->longitude;
            $radius = $request->input('radius');
        
            $query->whereRaw("
                (6371 * acos(
                cos(radians(?)) 
                * cos(radians(users.latitude)) 
                * cos(radians(users.longitude) - radians(?))
                + sin(radians(?)) 
                * sin(radians(latitude))
                )) < ?
            ", [$latitude, $longitude, $latitude, $radius]);
        }

        // Filter by approval status if provided
        if ($request->has('approval_status') && in_array($request->approval_status, ['pending', 'approved', 'rejected'])) {
            $query->where('approval_status', $request->approval_status);
        }

        // Filter by active status if provided
        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }
    
        // Paginate it like a pro
        $volunteers = $query->paginate(10);
        
        return view('organization.volunteers', compact('volunteers'));    
    }

    public function approve(Volunteer $volunteer)
    {
        if ($volunteer->approval_status === 'pending') {
            $volunteer->approval_status = 'approved';
            $volunteer->save();

            $user = $volunteer->user; 
            if ($user) {
                $user->role = 'Volunteer';
                $user->save();
            }
        }
        return redirect()->back()->with('success', 'Volunteer approved successfully.');
    }

    public function reject(Volunteer $volunteer)
    {
        if ($volunteer->approval_status === 'pending') {
            $volunteer->approval_status = 'rejected';
            $volunteer->save();
        }
        return redirect()->back()->with('success', 'Volunteer rejected successfully.');
    }

    /**
     * Remove the specified volunteer from storage.
     */
    public function destroy(Volunteer $volunteer)
    {
        $volunteer->delete();

        return redirect()->route('volunteers.index')
            ->with('success', 'Volunteer deleted successfully!');
    }
}