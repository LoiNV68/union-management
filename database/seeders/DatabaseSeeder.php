<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo 10 chi đoàn
        $branches = Branch::factory(10)->create();

        // Tạo user + member có quan hệ 1-1, phân bổ vào các chi đoàn
        User::factory(100)->create()->each(function ($user) use ($branches) {
            Member::factory()->create([
                'user_id' => $user->id,
                'branch_id' => $branches->random()->id,
            ]);
        });

        // Tạo tài khoản admin thủ công
        $adminUser = User::factory()->create([
            'student_code' => '123',
            'password' => bcrypt('123'),
            'role' => 2,
        ]);

        // Tạo branch và member tương ứng cho admin
        $adminBranch = Branch::factory()->create([
            'branch_name' => 'D11.01.01',
            'description' => 'Chi đoàn admin quản lý',
            'secretary' => $adminUser->id,
        ]);

        Member::factory()->create([
            'user_id' => $adminUser->id,
            'branch_id' => $adminBranch->id,
            'full_name' => 'Nguyễn Văn Lợi',
            'email' => 'admin@example.com',
            'status' => 1,
        ]);
    }
}