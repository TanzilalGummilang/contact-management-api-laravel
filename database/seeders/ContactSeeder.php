<?php

namespace Database\Seeders;

use App\Constants\ContactConstants;
use App\Constants\UserConstants;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        Contact::query()->create([
            'first_name' => ContactConstants::FIRST_NAME,
            'last_name' => ContactConstants::LAST_NAME,
            'email' => ContactConstants::EMAIL,
            'phone' => ContactConstants::PHONE,
            'user_id' => $user->id
        ]);
    }
}
