<?php

namespace App\Http\Controllers;

use App\Models\ReliefCenter;
use App\Models\User;
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
        if ($request->has('approval_status') && in_array($request->approval_status, ['prnding', 'approved', 'rejected'])) {
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

    /**
     * Store a newly created volunteer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'org_id' => 'nullable|exists:organizations,org_id',
            'user_id' => 'nullable|exists:users,user_id',
            'volunteer_id' => 'nullable|exists:volunteers,volunteer_id',
            'name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:10',
            'type' => 'required|in:donated,received',
            'description' => 'nullable|string',
        ]);

        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();

        $contribution = new Volunteer([
            'center_id' => $reliefCenter->center_id,
            'org_id' => $validated['org_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'volunteer_id' => $validated['volunteer_id'] ?? null,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);

        $contribution->save();

        return redirect()->route('contribution.index')
            ->with('success', 'Contribution logged successfully!');
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