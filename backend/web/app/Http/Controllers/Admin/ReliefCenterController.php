<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReliefCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ReliefCenterController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request)
    {
        $query = ReliefCenter::query();

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $reliefCenters = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.reliefcenters', compact('reliefCenters'));
    }

    public function destroy(ReliefCenter $reliefCenter)
    {
        $reliefCenter->delete();

        return redirect()->route('admin.reliefcenters')->with('success', 'Relief Center info deleted successfully!');
    }
}