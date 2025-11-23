<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Communication;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function chat(Request $request, $receiverId)
    {
        $userId = Auth::id();
                
        // // Mark 'sent' messages sent to current user by $receiverId as 'delivered'
        // Communication::where('sender_id', $receiverId)
        //     ->where('receiver_id', $userId)
        //     ->where('read_status', 'sent')
        //     ->update(['read_status' => 'delivered']);

        // If chat is open, mark delivered as read
        if ($request->has('mark_read') && $request->mark_read == true) {
            Communication::where('sender_id', $receiverId)
                ->where('receiver_id', $userId)
                ->update(['read_status' => 'read']);
        }

        // Get all messages between these two users
        $messages = Communication::where(function ($q) use ($userId, $receiverId) {
            $q->where('sender_id', $userId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($userId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $userId);
        })->orderBy('timestamp')->get();
        \Illuminate\Support\Facades\Log::info("Marking messages as read for user $userId and receiver $receiverId, $messages");
        // Return JSON array of messages
        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,user_id',
            'message' => 'required|string|max:1000',
        ]);

        Communication::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'timestamp' => now(),
            'read_status' => 'sent',
        ]);

        return response()->json(['success' => true]);
    }

    public function ui($receiverId) {
        $userId = Auth::id();
        Communication::where('receiver_id', $userId)
        ->where('read_status', 'sent')
        ->update(['read_status' => 'delivered']);
        // dd($receiverId);
        // dd($userId);
        $receiver = User::findOrFail($receiverId);
        $receiverName = $receiver->name;
        $receiverId = $receiver->user_id;
        if($receiverId == Auth::id()) {
            $receiverId = null;
            $receiverName = null;
            return view('chat', compact('receiverId', 'receiverName'));
        } else{
            return view('chat', compact('receiverId', 'receiverName'));
        }
    }
}
