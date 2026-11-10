<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message) {}
    public function broadcastOn(): array
    {
        $a = $this->message->sender_id;
        $b = $this->message->receiver_id;

        return [
            new Channel('chat.' . min($a, $b) . '.' . max($a, $b)),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $msg = $this->message->load('sender');

        return [
            'id'          => $msg->id,
            'sender_id'   => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message'     => $msg->message,
            'sender_name' => $msg->sender->name,
            'created_at'  => $msg->created_at->toISOString(),
        ];
    }
}