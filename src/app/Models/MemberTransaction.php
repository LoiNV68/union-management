<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'member_id',
        'payment_status',
        'payment_date',
        'payment_proof',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'payment_status' => 'integer',
            'payment_date' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 1);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 0);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('payment_status', 2);
    }
}
