<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Sinh mã sinh viên dạng 2254800xxx - dùng fake() thay vì $this->faker
        $student_code = '2254800' . str_pad(fake()->numberBetween(0, 999), 3, '0', STR_PAD_LEFT);

        return [
            'student_code' => $student_code,
            'password' => '123A@a321',
            'role' => 0,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withoutTwoFactor(): static
    {
        return $this->state(fn(array $attributes) => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }
}