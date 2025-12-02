<?php

namespace App\Events;

use App\Models\Activity;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Activity $activity)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('activities');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->activity->id,
            'activity_name' => $this->activity->activity_name,
            'approved_count' => $this->activity->registrations()
                ->where('registration_status', 1)
                ->count(),
        ];
    }
}
