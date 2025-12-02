<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'branches';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'branch_name',
        'description',
        'secretary',
        'birth_date',
        'gender',
        'phone_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'gender' => 'int',
        ];
    }

    /**
     * Members that belong to this branch.
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'branch_id');
    }

    /**
     * The secretary (a member) of this branch.
     */
    public function secretaryMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary');
    }

    /**
     * Backwards-compatible alias for the secretary relationship used in Livewire components.
     */
    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary');
    }
}