<?php

namespace App\Http\Controllers;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $receptionist = Auth::user();
        $contacts = User::where('role', 'member')
            ->whereHas('sentMessages', function ($q) use ($receptionist) {
                $q->where('receiver_id', $receptionist->id);
            })
            ->orWhereHas('receivedMessages', function ($q) use ($receptionist) {
                $q->where('sender_id', $receptionist->id);
            })
            ->get()
            ->map(function (User $member) use ($receptionist) {
                $lastMsg = ChatMessage::where(function ($q) use ($receptionist, $member) {
                    $q->where('sender_id', $receptionist->id)
                      ->where('receiver_id', $member->id);
                })->orWhere(function ($q) use ($receptionist, $member) {
                    $q->where('sender_id', $member->id)
                      ->where('receiver_id', $receptionist->id);
                })
                ->latest()
                ->first();
                $unread = ChatMessage::where('sender_id', $member->id)
                    ->where('receiver_id', $receptionist->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'id'       => $member->id,
                    'name'     => $member->name,
                    'email'    => $member->email,
                    'initials' => strtoupper(substr($member->name, 0, 2)),
                    'lastMsg'  => $lastMsg?->message,
                    'lastTime' => $lastMsg?->created_at?->diffForHumans(),
                    'unread'   => $unread,
                ];
            });

        return view('components.reservation.admin-chat', compact('contacts', 'receptionist'));
    }

    public function history(Request $request)
    {
        $request->validate([
            'with' => ['required', 'integer', 'exists:users,id'],
        ]);

        $me     = Auth::id();
        $other  = (int) $request->with;

        $messages = ChatMessage::with('sender:id,name,role')
            ->where(function ($q) use ($me, $other) {
                $q->where('sender_id', $me)->where('receiver_id', $other);
            })
            ->orWhere(function ($q) use ($me, $other) {
                $q->where('sender_id', $other)->where('receiver_id', $me);
            })
            ->orderBy('created_at')
            ->get()
            ->map(fn($msg) => [
                'id'          => $msg->id,
                'sender_id'   => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message'     => $msg->message,
                'sender_name' => $msg->sender->name,
                'created_at'  => $msg->created_at->toISOString(),
                'time'        => $msg->created_at->format('H:i'),
            ]);

        ChatMessage::where('sender_id', $other)
            ->where('receiver_id', $me)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'message'     => ['required', 'string', 'max:2000'],
        ]);

        $msg = ChatMessage::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        broadcast(new MessageSent($msg));

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $msg->id,
                'sender_id'   => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message'     => $msg->message,
                'sender_name' => Auth::user()->name,
                'created_at'  => $msg->created_at->toISOString(),
                'time'        => $msg->created_at->format('H:i'),
            ],
        ]);
    }
    public function unreadCount()
    {
        $count = ChatMessage::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}