<?php

namespace Database\Seeders;

use App\Constants\UserConstants;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
           'name' => UserConstants::NAME,
           'username' => UserConstants::USERNAME,
           'password' => Hash::make(UserConstants::PASSWORD),
           'token' => UserConstants::TOKEN
        ]);

        User::query()->create([
           'name' => UserConstants::NAME . '2',
           'username' => UserConstants::USERNAME . '2',
           'password' => Hash::make(UserConstants::PASSWORD) . '2',
           'token' => UserConstants::TOKEN . '2'
        ]);
    }
}
