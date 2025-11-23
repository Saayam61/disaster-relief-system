<?php

namespace App\Http\Controllers\Admin;

use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class OrganizationController extends Controller
{
    /**
     * Display a listing of the users except Administrator.
     */
    public function index(Request $request)
    {
        $query = Organization::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }        

        $organizations = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.organizations', compact('organizations'));
    }

    public function updateType(Request $request, $org)
    {
        $organization = Organization::findOrFail($org);
        $organization->type = $request->type;
        $organization->save();

        return back()->with('success', 'Organization type updated successfully!');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('admin.organizations')->with('success', 'Organization info deleted successfully!');
    } 
}