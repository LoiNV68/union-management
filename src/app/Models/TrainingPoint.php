<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingPoint extends Model
{
  use HasFactory;

  protected $fillable = [
    'point',
    'member_id',
    'semester_id',
    'updater_id',
  ];

  public function member(): BelongsTo
  {
    return $this->belongsTo(Member::class, 'member_id');
  }

  public function semester(): HasMany
  {
    return $this->hasMany(Semester::class, 'semester_id');
  }

  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updater_id');
  }
}