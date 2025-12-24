<?php

namespace Database\Seeders;

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
           'name' => 'John Doe',
           'username' => 'johndoe',
           'password' => Hash::make('test'),
           'token' => 'test'
        ]);
    }
}
