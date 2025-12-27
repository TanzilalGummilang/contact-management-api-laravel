<?php

namespace Database\Seeders;

use App\Constants\UserConstants;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::query()->create([
                'first_name' => 'first ' . $i,
                'last_name' => 'last ' . $i,
                'email' => 'test' . $i . '@test.com',
                'phone' => '08880000' . $i,
                'user_id' => $user->id
            ]);
        }
    }
}
