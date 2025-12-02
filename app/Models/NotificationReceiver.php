<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationReceiver extends Model
{
  use HasFactory;

  protected $table = 'notification_receivers';

  protected $fillable = [
    'notification_id',
    'member_id',
    'is_read',
  ];

  public function notification(): BelongsTo
  {
    return $this->belongsTo(Notification::class, 'notification_id');
  }

  public function member(): BelongsTo
  {
    return $this->belongsTo(Member::class, 'member_id');
  }
}