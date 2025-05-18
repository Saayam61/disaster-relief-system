<?php

namespace App\Http\Controllers\Api;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReliefCenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{
    // GET /api/contributions/user/{userId}
    public function index()
    {
        $userId = Auth::user()->user_id;
        $relief_centers = ReliefCenter::with(['user'])->get();
        $requests = ModelsRequest::with(['reliefCenter.user'])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'data' => $requests,
            'center_data' => $relief_centers
        ]);
    }
    public function store(Request $request)
    {
        // Log::info($request->all());

        $validated = $request->validate([
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|regex:/^[a-zA-Z]+$/',
            'request_type' => 'required|in:supply,evacuation,medical,other',
            'urgency' => 'required|in:low,medium,high',
            'center_id' => 'required|exists:relief_centers,center_id',
        ]);

        $validated['user_id'] = Auth::user()->user_id;
        $validated['status'] = 'pending';

        $newRequest = ModelsRequest::create($validated);

        return response()->json($newRequest->load(['user', 'reliefCenter.user']), 201);
    }

    // PUT /api/requests/{id}
    public function update(Request $request, $id)
    {
        // Log::info($request->all());

        $existingRequest = ModelsRequest::findOrFail($id);

        // Optional: Check if the logged-in user is allowed to update this request
        if ($existingRequest->user_id !== Auth::user()->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|regex:/^[a-zA-Z]+$/',
            'request_type' => 'required|in:supply,evacuation,medical,other',
            'urgency' => 'required|in:low,medium,high',
        ]);

        $existingRequest->update($validated);
        

        return response()->json($existingRequest->load(['user', 'reliefCenter.user']));
    }

    // DELETE /api/contributions/{id}
    public function destroy(ModelsRequest $request)
    {
        $request->delete();

        return response()->json(['message' => 'Request deleted successfully.']);
    }
}
