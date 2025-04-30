<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ReliefCenter;
use App\Models\User;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10|unique:users,phone,' . $user->user_id . ',user_id',
            'email' => 'required|email|string|max:255|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ];

        // $user->save();
        User::where('user_id', $user->user_id)->update($data);

        return redirect()->back()->with('success', 'User info updated successfully!');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $reliefCenter = $user->reliefCenter ?? new ReliefCenter(['user_id' => $user->user_id]);

        $request-> validate([
            'address' => 'required|string',
            'capacity' => 'required|integer|min:0',
            'current_occupancy' => 'required|integer|min:0',
            'total_volunteers' => 'required|integer|min:0',
            'total_supplies' => 'nullable|string',
            'contact_numbers' => 'required|string|max:255',
            'is_active' => 'required',
        ]);

        $reliefCenter->fill($request->only([
            'address',
            'capacity',
            'current_occupancy',
            'total_volunteers',
            'total_supplies',
            'contact_numbers',
        ]));
        $reliefCenter->is_active = $request->input('is_active', 0);
        $reliefCenter->save();

        return redirect()->back()->with('success', 'Relief center profile updated successfully!');
    }
}