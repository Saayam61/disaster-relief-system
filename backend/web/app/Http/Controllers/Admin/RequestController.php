<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReliefCenter;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class RequestController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request)
    {
        $query = ModelsRequest::query();
        $reliefCenters = ReliefCenter::all();

        if ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->request_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.requests', compact('requests', 'reliefCenters'));
    }

    public function destroy(ModelsRequest $request)
    {
        $request->delete();

        return redirect()->route('admin.requests')->with('success', 'Request deleted successfully!');
    }
}