<?php

namespace App\Events;

use App\Models\ActivityRegistration;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistrationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ActivityRegistration $registration)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('activities');
    }

    public function broadcastWith(): array
    {
        return [
            'registration_id' => $this->registration->id,
            'activity_id' => $this->registration->activity_id,
            'status' => $this->registration->registration_status,
        ];
    }
}
