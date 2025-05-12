<?php

namespace App\Http\Controllers\Admin;

use App\Models\Volunteer;
use App\Models\ReliefCenter;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class VolunteerController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request)
    {
        $query = Volunteer::query();
        $reliefCenters = ReliefCenter::all();
        $organizations = Organization::all();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }        

        $volunteers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.volunteers', compact('volunteers', 'reliefCenters', 'organizations'));
    }

    public function updateRC(Request $request, $vol)
    {
        $volunteer = Volunteer::findOrFail($vol);
        $volunteer->center_id = $request->center_id;
        $volunteer->save();

        return back()->with('success', 'Volunteer assignment updated successfully!');
    }

    public function updateOrg(Request $request, $vol)
    {
        $volunteer = Volunteer::findOrFail($vol);
        $volunteer->org_id = $request->org_id;
        $volunteer->save();

        return back()->with('success', 'Volunteer assignment updated successfully!');
    }

    public function destroy(Volunteer $volunteer)
    {
        $volunteer->delete();

        return redirect()->route('admin.volunteers')->with('success', 'Volunteer info deleted successfully!');
    }
}