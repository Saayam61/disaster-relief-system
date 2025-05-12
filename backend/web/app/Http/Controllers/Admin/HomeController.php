<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        return view('admin.dashboard');
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