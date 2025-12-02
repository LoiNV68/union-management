<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchDues extends Model
{
  use HasFactory;

  protected $table = 'branch_dues';

  protected $fillable = [
    'member_id',
    'amount_money',
    'type',
    'payment_date',
    'qr_code',
  ];

  protected function casts(): array
  {
    return [
      'amount_money' => 'decimal:2',
      'type' => 'integer',
      'payment_date' => 'date',
    ];
  }

  /**
   * Quan hệ: Mỗi khoản thu/chi thuộc về 1 đoàn viên.
   */
  public function member(): BelongsTo
  {
    return $this->belongsTo(Member::class, 'member_id');
  }
}