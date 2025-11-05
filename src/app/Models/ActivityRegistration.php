<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRegistration extends Model
{
  use HasFactory;

  protected $table = 'activity_registration'; // vì bạn đặt tên bảng số ít

  protected $fillable = [
    'member_id',
    'activity_id',
    'registration_time',
    'registration_status',
    'note',
  ];

  protected function casts(): array
  {
    return [
      'registration_time' => 'date',
      'registration_status' => 'integer',
    ];
  }

  /**
   * Quan hệ tới đoàn viên (member)
   */
  public function member(): BelongsTo
  {
    return $this->belongsTo(Member::class, 'member_id');
  }

  /**
   * Quan hệ tới hoạt động (activity)
   */
  public function activity(): BelongsTo
  {
    return $this->belongsTo(Activity::class, 'activity_id');
  }
}