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
        // Reset unique generator để tránh conflict với email hardcode
        fake()->unique(true);

        // Tạo tài khoản super admin thủ công
        $adminUser = User::factory()->create([
            'student_code' => '123',
            'password' => bcrypt('123A@a321'),
            'role' => 2,
        ]);

        // Tạo branch và member tương ứng cho super admin
        $adminBranch = Branch::factory()->create([
            'branch_name' => 'D11.01.01',
            'description' => 'Chi đoàn admin quản lý',
            'secretary' => $adminUser->id,
        ]);

        Member::create([
            'user_id' => $adminUser->id,
            'branch_id' => $adminBranch->id,
            'full_name' => 'Nguyễn Văn Lợi',
            'email' => 'admin@example.com',
            'birth_date' => fake()->dateTimeBetween('-30 years', '-25 years')->format('Y-m-d'),
            'gender' => 0,
            'address' => fake()->address(),
            'phone_number' => fake()->numerify('0#########'),
            'join_date' => fake()->date('Y-m-d'),
            'status' => 1,
        ]);

        // Tạo 5 cán bộ đoàn (role 1) với các chi đoàn
        $branchOfficers = [];
        for ($i = 1; $i <= 5; $i++) {
            $officerUser = User::factory()->create([
                'student_code' => '2254801' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password' => bcrypt('123A@a321'),
                'role' => 1,
            ]);

            $branch = Branch::factory()->create([
                'branch_name' => 'D11.0' . $i . '.01',
                'description' => 'Chi đoàn được quản lý bởi cán bộ đoàn ' . $i,
                'secretary' => $officerUser->id,
            ]);

            // Tạo member cho cán bộ đoàn
            $officerMember = Member::create([
                'user_id' => $officerUser->id,
                'branch_id' => $branch->id,
                'full_name' => 'Cán bộ đoàn ' . $i,
                'email' => 'canbo' . $i . '@example.com',
                'birth_date' => fake()->dateTimeBetween('-26 years', '-20 years')->format('Y-m-d'),
                'gender' => fake()->randomElement([0, 1]),
                'address' => fake()->address(),
                'phone_number' => fake()->numerify('0#########'),
                'join_date' => fake()->date('Y-m-d'),
                'status' => 1,
            ]);

            // Tạo 10-15 thành viên ngẫu nhiên cho mỗi chi đoàn
            $memberCount = fake()->numberBetween(10, 15);
            User::factory($memberCount)->create(['role' => 0])->each(function ($user) use ($branch) {
                Member::factory()->create([
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'status' => 1,
                ]);
            });

            $branchOfficers[] = [
                'user' => $officerUser,
                'branch' => $branch,
                'member' => $officerMember,
            ];
        }

        // Tạo thêm 5 chi đoàn không có cán bộ đoàn (secretary = null)
        $branchesWithoutOfficers = Branch::factory(5)->create([
            'secretary' => null,
        ]);

        // Tạo thành viên cho các chi đoàn không có cán bộ đoàn
        User::factory(50)->create(['role' => 0])->each(function ($user) use ($branchesWithoutOfficers) {
            Member::factory()->create([
                'user_id' => $user->id,
                'branch_id' => $branchesWithoutOfficers->random()->id,
                'status' => 1,
            ]);
        });
    }
}