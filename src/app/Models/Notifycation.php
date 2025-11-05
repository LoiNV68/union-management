<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
  use HasFactory;

  protected $fillable = [
    'title',
    'content',
    'date_sent',
    'sender_id',
    'receiver_id',
    'notify_type',
  ];

  protected function casts(): array
  {
    return [
      'date_sent' => 'datetime',
      'notify_type' => 'integer',
    ];
  }

  public function sender(): BelongsTo
  {
    return $this->belongsTo(User::class, 'sender_id');
  }

  public function receiver(): BelongsTo
  {
    return $this->belongsTo(User::class, 'receiver_id');
  }

  public function notify_receiver(): HasMany
  {
    return $this->hasMany(NotificationReceiver::class, 'notification_id');
  }
}