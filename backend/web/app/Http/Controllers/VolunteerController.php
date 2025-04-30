<?php

namespace App\Http\Controllers;

use App\Models\ReliefCenter;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VolunteerController extends Controller
{
    /**
     * Display a listing of the contributions.
     */
    public function index(Request $request)
    {
        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();
        
        // Build the query only for this relief center
        $query = Volunteer::with(['reliefCenter', 'user'])
        ->where('center_id', $reliefCenter->center_id)
        ->orderBy('created_at', 'desc');

    
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
        
        return view('volunteer', compact('volunteers'));    
    }

    public function approve(Volunteer $volunteer)
    {
        if ($volunteer->approval_status === 'pending') {
            $volunteer->approval_status = 'approved';
            $volunteer->save();
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

        return redirect()->route('volunteer.index')
            ->with('success', 'Volunteer deleted successfully!');
    }
}