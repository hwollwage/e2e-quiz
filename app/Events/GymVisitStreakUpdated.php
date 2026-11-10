<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GymVisitStreakUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int   $userId,
        public readonly array $streakData,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("gym-streak.{$this->userId}");
    }

    public function broadcastAs(): string
    {
        return 'streak.updated';
    }

    public function broadcastWith(): array
    {
        return $this->streakData;
    }
}