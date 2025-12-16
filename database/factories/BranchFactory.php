<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tạo phần đầu D11 / D12 / D13 ngẫu nhiên - dùng fake() thay vì $this->faker
        $prefix = fake()->randomElement(['D11', 'D12', 'D13']);
        // Sinh hai phần số ngẫu nhiên từ 01–99
        $part1 = str_pad(fake()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT);
        $part2 = str_pad(fake()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT);
        return [
            'branch_name' => "{$prefix}.{$part1}.{$part2}",
            'description' => "Chi đoàn của đoàn viên",
            'secretary' => null,
        ];
    }
}