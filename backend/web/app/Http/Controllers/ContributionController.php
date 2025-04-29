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
    public function index(Request $request)
    {
        $query = Contribution::with(['reliefCenter', 'organization', 'user', 'volunteer'])
            ->orderBy('created_at', 'desc');
    
        // Filter by contribution type if provided
        if ($request->has('type') && in_array($request->type, ['donated', 'received'])) {
            $query->where('type', $request->type);
        }
    
        // Filter by contributor type — but smartly, based on 'type'
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
    
        // Paginate it like a pro
        $contributions = $query->paginate(15);
    
        // Preload models for forms or dropdowns
        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();
        $users = User::all();
        $volunteers = Volunteer::all();
    
        $generalUsers = $users->where('role', 'General User');
    
        return view('supplies', compact(
            'contributions',
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
        // Log::info('Store method reached', ['user_id' => Auth::id(), 'input' => $request->all()]);
    // dd('Store method reached', $request->all());
        $validated = $request->validate([
            // 'center_id' => 'nullable|exists:relief_centers,center_id',
            // 'org_id' => 'nullable|exists:organizations,org_id',
            'user_id' => 'nullable|exists:users,user_id',
            // 'volunteer_id' => 'nullable|exists:volunteers,volunteer_id',
            'name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:10',
            'type' => 'required|in:donated,received',
            'description' => 'nullable|string',
        ]);

        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();

        $contribution = new Contribution([
            'center_id' => $reliefCenter->center_id,
            // 'org_id' => $validated['org_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null, // fallback to current user
            // 'volunteer_id' => $validated['volunteer_id'] ?? null,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);
        // try {
        //     $contribution->save();
        // } catch (\Exception $e) {
        //     dd('Save failed:', $e->getMessage());
        // }

        $contribution->save();
        return redirect()->route('contribution.index')
            ->with('success', 'Contribution logged successfully!');
    }

    /**
     * Show the form for editing the specified contribution.
     */
    public function edit(Request $request, Contribution $contribution)
    {
        $query = Contribution::with(['reliefCenter', 'organization', 'user', 'volunteer'])
            ->orderBy('created_at', 'desc');
    
        // Filter by contribution type if provided
        if ($request->has('type') && in_array($request->type, ['donated', 'received'])) {
            $query->where('type', $request->type);
        }
    
        // Filter by contributor type — but smartly, based on 'type'
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
    
        // Paginate it like a pro
        $contributions = $query->paginate(15);


        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();
        $users = User::all();
        $volunteers = Volunteer::all();

        $generalUsers = $users->where('role', 'General User');

        return view('supplies', compact(
            'contribution',
            'contributions',
            'reliefCenters',
            'organizations',
            'generalUsers',
            'volunteers'
        ));
    }

    /**
     * Update the specified contribution in storage.
     */
    public function update(Request $request, Contribution $contribution)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:relief_centers,center_id',
            // 'org_id' => 'nullable|exists:organizations,org_id',
            'user_id' => 'nullable|exists:users,user_id',
            // 'volunteer_id' => 'nullable|exists:volunteers,volunteer_id',
            'name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:10',
            'type' => 'required|in:donated,received',
            'description' => 'nullable|string',
        ]);

        $contribution->update([
            'center_id' => $validated['center_id'],
            // 'org_id' => $validated['org_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            // 'volunteer_id' => $validated['volunteer_id'] ?? null,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('supplies')
            ->with('success', 'Contribution updated successfully!');
    }

    /**
     * Remove the specified contribution from storage.
     */
    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->route('contribution.index')
            ->with('success', 'Contribution deleted successfully!');
    }
}