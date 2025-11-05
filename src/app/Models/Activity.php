<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
  use HasFactory;

  protected $table = 'activity'; // vì bạn đặt tên bảng số ít

  protected $fillable = [
    'activity_name',
    'description',
    'start_date',
    'end_date',
    'location',
    'type',
    'max_participants',
    'creator',
  ];

  protected function casts(): array
  {
    return [
      'start_date' => 'date',
      'end_date' => 'date',
      'type' => 'integer',
      'max_participants' => 'integer',
    ];
  }

  /**
   * Người tạo hoạt động (quan hệ với bảng users)
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'creator');
  }

  public function registrations()
  {
    return $this->hasMany(ActivityRegistration::class, 'activity_id');
  }
}