<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_code',
        'password',
        'role',
        'is_locked'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_locked' => 'boolean',
            'role' => 'integer',
        ];
    }
    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    public function updatedTrainingPoints(): HasMany
    {
        return $this->hasMany(TrainingPoint::class, 'updater_id');
    }

    public function sender(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function receiver(): HasMany
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }


    public function income_expenditure(): HasMany
    {
        return $this->hasMany(IncomeExpenditure::class, 'performer_id');
    }

    public function branch(): HasMany
    {
        return $this->hasMany(Branch::class, 'secretary');
    }

    public function trainingPoints()
    {
        return $this->hasMany(TrainingPoint::class, 'member_id');
    }

    public function initials(): ?string
    {
        return $this->member?->initials();
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->member?->full_name;
    }

    public function getEmailAttribute(): ?string
    {
        return $this->member?->email;
    }

    public function getPhoneNumberAttribute(): ?string
    {
        return $this->member?->phone_number;
    }

    public function getBirthDateAttribute(): ?string
    {
        return $this->member?->birth_date;
    }

    public function getGenderAttribute(): ?string
    {
        return $this->member?->gender;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->member?->address;
    }

    public function getJoinDateAttribute(): ?string
    {
        return $this->member?->join_date;
    }
}