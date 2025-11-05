<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
  use HasFactory;

  protected $fillable = [
    'school_year',
    'semester',
  ];
  public function trainingPoints() : BelongsTo
  {
    return $this->belongsTo(TrainingPoint::class, 'semester_id');
  }
}