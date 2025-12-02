<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'type',
        'due_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => 'integer',
            'status' => 'integer',
            'due_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function memberTransactions(): HasMany
    {
        return $this->hasMany(MemberTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 1);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 0);
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 1);
    }
}
