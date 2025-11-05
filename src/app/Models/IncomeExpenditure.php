<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeExpenditure extends Model
{
  use HasFactory;

  protected $table = 'income_expenditures';

  protected $fillable = [
    'type',
    'amount_money',
    'transaction_date',
    'description',
    'performer_id',
  ];

  protected function casts(): array
  {
    return [
      'type' => 'integer',
      'amount_money' => 'decimal:2',
      'transaction_date' => 'date',
    ];
  }

  public function performer(): BelongsTo
  {
    return $this->belongsTo(User::class, 'performer_id');
  }
}