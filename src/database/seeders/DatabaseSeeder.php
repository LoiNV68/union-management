<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();
        Branch::factory(100)->create();
        Member::factory(100)->create();

        // User::factory()->create([
        //     'full_name' => 'admin',
        //     'password' => '123',
        //     'role' => 2
        // ]);
    }
}