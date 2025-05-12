<?php

namespace App\Http\Controllers\Organization;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Organization;
use App\Models\User;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('organization.home');
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

        return redirect()->back()->with('success', 'User info updated successfully!');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $organizations = $user->organizations ?? new Organization(['user_id' => $user->user_id]);

        $request-> validate([
            'type' => 'required|in:i/ngo, private',
            'is_verified' => 'required|integer|min:',
        ]);

        $organizations->fill($request->only([
            'address',
            'capacity',
            'current_occupancy',
            'total_volunteers',
            'total_supplies',
            'contact_numbers',
        ]));
        $organizations->is_active = $request->input('is_active', 0);
        $organizations->save();

        return redirect()->back()->with('success', 'Organization profile updated successfully!');
    }
}