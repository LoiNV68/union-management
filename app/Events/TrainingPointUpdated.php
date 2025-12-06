<?php

namespace App\Events;

use App\Models\TrainingPoint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingPointUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public TrainingPoint $trainingPoint)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('training-points');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->trainingPoint->id,
            'point' => $this->trainingPoint->point,
        ];
    }

    public function broadcastAs(): string
    {
        return 'training-point.updated';
    }
}
