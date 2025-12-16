<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Branch;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fullName = fake()->name();

        return [
            'full_name' => $fullName,
            'birth_date' => fake()->dateTimeBetween('-26 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement([0, 1]),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->numerify('0#########'),
            'join_date' => fake()->date('Y-m-d'),
            'status' => 1,
            'user_id' => null,
            'branch_id' => null,
        ];
    }
}