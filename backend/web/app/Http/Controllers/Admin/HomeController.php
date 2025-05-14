<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\FloodAlert;
use App\Models\Volunteer;
use App\Models\ReliefCenter;
use App\Models\Organization;
use App\Models\Request as ModelsRequest;
use App\Models\Contribution;
use App\Models\NewsFeed;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $activeAlertsCount = FloodAlert::where('is_active', 1)->count();
        $activeReliefCentersCount = ReliefCenter::where('is_active', 1)->count();
        $activeVolunteersCount = Volunteer::where('status', 'active')->count(); 
        $activeOrganizationsCount = Organization::where('is_active', 1)->count(); 
        $donatedContributionsCount = Contribution::where('type', 'donated')->count(); 
        $receivedContributionsCount = Contribution::where('type', 'received')->count(); 
        $fulfilledRequestsCount = ModelsRequest::where('request_type', 'fulfilled')->count();
        $postCount = NewsFeed::all()->count();
        $userRolesCount = DB::table('users')
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        return view('admin.dashboard', compact(
            'activeAlertsCount',
            'activeReliefCentersCount',
            'activeVolunteersCount',
            'activeOrganizationsCount',
            'donatedContributionsCount',
            'receivedContributionsCount',
            'fulfilledRequestsCount',
            'postCount',
            'userRolesCount'
        ));
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10|unique:users,phone,' . $user->user_id . ',user_id',
            'email' => 'required|email|string|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'address' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ];

        // $user->save();
        User::where('user_id', $user->user_id)->update($data);

        return redirect()->back()->with('success', 'Admin info updated successfully!');
    }
}