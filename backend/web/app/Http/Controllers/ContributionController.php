<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ReliefCenter;
use App\Models\Organization;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContributionController extends Controller
{
    /**
     * Display a listing of the contributions.
     */
    public function index(Request $request, $userId)
{
    $query = Contribution::with(['reliefCenter', 'organization', 'user', 'volunteer'])
        ->orderBy('created_at', 'desc');

    // Filter by contribution type if provided
    if ($request->has('type') && in_array($request->type, ['donated', 'received'])) {
        $query->where('type', $request->type);
    }

    // Filter by contributor type
    if ($request->has('user_type')) {
        $query->where(function ($q) use ($request) {
            switch ($request->user_type) {
                case 'user':
                    $q->where('type', 'received')->whereNotNull('user_id');
                    break;
                case 'volunteer':
                    $q->where('type', 'received')->whereNotNull('volunteer_id');
                    break;
                case 'organization':
                    $q->where('type', 'received')->whereNotNull('org_id');
                    break;
                case 'relief_center':
                    $q->where('type', 'donated')->whereNotNull('center_id');
                    break;
            }
        });
    }

    $contributions = $query->where(function ($q) use ($userId) {
        $q->where('user_id', $userId)
          ->orWhere('volunteer_id', $userId)
          ->orWhere('org_id', $userId)
          ->orWhere('center_id', function ($subQuery) use ($userId) {
              $subQuery->select('center_id')
                  ->from('relief_centers')
                  ->where('user_id', $userId)
                  ->limit(1);
          });
    })->paginate(10);

    $reliefCenters = ReliefCenter::all();
    $organizations = Organization::all();
    $users = User::all();
    $volunteers = Volunteer::all();
    $user = User::findOrFail($userId);
    $reliefCenter = ReliefCenter::where('user_id', $user->id)->first();
    $generalUsers = $users->where('role', 'General User');

    return view('contribution', compact(
        'contributions',
        'reliefCenters',
        'organizations',
        'generalUsers',
        'volunteers',
        'reliefCenter',
        'user'
    ));
}


    public function newDonation()
    {
        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();
        $users = User::all();
        $volunteers = Volunteer::all();
        $generalUsers = $users->where('role', 'General User');

        return view('contributionD', compact(
            'reliefCenters',
            'organizations',
            'generalUsers',
            'volunteers'
        ));
    }

    public function newReceive()
    {
        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();
        $users = User::all();
        $volunteers = Volunteer::all();

        $generalUsers = $users->where('role', 'General User');

        return view('contributionR', compact(
            'reliefCenters',
            'organizations',
            'generalUsers',
            'volunteers'
        ));
    }

    /**
     * Store a newly created contribution in storage.
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

        $contribution = new Contribution([
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

        $userId = Auth::id();

        $contribution->save();

        return redirect()->route('contribution.index', ['userId' => $userId])
            ->with('success', 'Contribution logged successfully!');
    }

    /**
     * Show the form for editing the specified contribution.
     */
    public function editDonation(Request $request, Contribution $contribution)
    {

        $contributions = Contribution::with(['reliefCenter', 'organization', 'user', 'volunteer'])
        ->where('type', 'donated')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();
        $users = User::all();
        $volunteers = Volunteer::all();

        $generalUsers = $users->where('role', 'General User');

        return view('ContributionD', compact(
            'contribution',
            'contributions',
            'reliefCenters',
            'organizations',
            'generalUsers',
            'volunteers',
        ));
    }

    public function editReceive(Request $request, Contribution $contribution)
    {

        $contributions = Contribution::with(['reliefCenter', 'organization', 'user', 'volunteer'])
        ->where('type', 'received')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();
        $users = User::all();
        $volunteers = Volunteer::all();

        $generalUsers = $users->where('role', 'General User');

        return view('ContributionR', compact(
            'contribution',
            'contributions',
            'reliefCenters',
            'organizations',
            'generalUsers',
            'volunteers',
        ));
    }

    /**
     * Update the specified contribution in storage.
     */
    public function update(Request $request, Contribution $contribution)
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
        $contribution->update([
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
        $userId = Auth::id();

        
        return redirect()->route('contribution.index', ['userId' => $userId])
            ->with('success', 'Contribution updated successfully!');
    }

    /**
     * Remove the specified contribution from storage.
     */
    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->route('contribution.index', ['userId' => Auth::id()])
            ->with('success', 'Contribution deleted successfully!');
    }
}