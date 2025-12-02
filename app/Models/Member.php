<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'members';

    protected $fillable = [
        'full_name',
        'birth_date',
        'gender',
        'address',
        'email',
        'phone_number',
        'join_date',
        'status',
        'user_id',
        'branch_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
            'gender' => 'int',
            'status' => 'int',
            'user_id' => 'int',
            'branch_id' => 'int',
        ];
    }

    

    /**
     * The user account associated with the member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branchDues()
    {
        return $this->hasMany(BranchDues::class, 'member_id');
    }


    public function registrations()
    {
        return $this->hasMany(ActivityRegistration::class, 'member_id');
    }

    /**
     * The branch this member belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branch_dues(): BelongsTo
    {
        return $this->belongsTo(BranchDues::class, 'member_id');
    }

    public function notify_receiver(): BelongsTo
    {
        return $this->belongsTo(NotificationReceiver::class, 'member_id');
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_receivers')
            ->withPivot('is_read')
            ->withTimestamps();
    }

    public function trainingPoints()
    {
        return $this->hasMany(TrainingPoint::class, 'member_id');
    }

    public function activity_registration()
    {
        return $this->hasMany(TrainingPoint::class, 'member_id');
    }
    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->full_name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}