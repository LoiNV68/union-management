<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Transaction $transaction)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('transactions');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->transaction->id,
            'title' => $this->transaction->title,
        ];
    }

    public function broadcastAs(): string
    {
        return 'transaction.updated';
    }
}
