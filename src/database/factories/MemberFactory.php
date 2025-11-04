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
        $fullName = $this->faker->name();

        return [
            'full_name' => $fullName,
            'birth_date' => $this->faker->dateTimeBetween('-26 years', '-18 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement([0, 1]),
            'address' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->numerify('0#########'),
            'join_date' => $this->faker->date('Y-m-d'),
            'status' => 1,
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
        ];
    }
}