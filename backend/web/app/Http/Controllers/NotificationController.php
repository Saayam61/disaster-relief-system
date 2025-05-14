<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10);
        return view('notification', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->unreadNotifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }
}
