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
    }
}
