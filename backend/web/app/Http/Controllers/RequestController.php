<?php

namespace App\Http\Controllers;

use App\Models\ReliefCenter;
use App\Models\Request as ModelsRequest;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{
    /**
     * Display a listing of the contributions.
     */
    public function index(Request $request)
    {
        $reliefCenter = ReliefCenter::where('user_id', Auth::id())->firstOrFail();
        
        // Build the query only for this relief center
        $query = ModelsRequest::with(['reliefCenter', 'user'])
        ->where('center_id', $reliefCenter->center_id)
        ->orderBy('created_at', 'desc');

    
        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'processing', 'fulfilled', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Filter by urgency if provided
        if ($request->has('urgency') && in_array($request->urgency, ['low', 'medium', 'high'])) {
            $query->where('urgency', $request->urgency);
        }

        // Filter by request type if provided
        if ($request->has('request_type') && in_array($request->request_type, ['supply', 'evacuation', 'medical', 'other'])) {
            $query->where('request_type', $request->request_type);
        }
    
        // Paginate it like a pro
        $requests = $query->paginate(10);
        
        return view('request', compact('requests'));    
    }

    public function update(Request $request, ModelsRequest $req)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,fulfilled,rejected',
        ]);

        $req->update([
            'status' => $validated['status'],
        ]);


        return redirect()->route('request.index')
        ->with('success', 'Status updated successfully!');
    }

    /**
     * Remove the specified volunteer from storage.
     */
    public function destroy(ModelsRequest $req)
    {
        $req->delete();

        return redirect()->route('request.index')
            ->with('success', 'Request deleted successfully!');
    }
}