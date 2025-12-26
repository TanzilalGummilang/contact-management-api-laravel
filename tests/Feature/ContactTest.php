<?php

namespace Tests\Feature;

use App\Constants\ContactConstants;
use App\Constants\UserConstants;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_create_successfully()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => ContactConstants::FIRST_NAME,
            'last_name' => ContactConstants::LAST_NAME,
            'email' => ContactConstants::EMAIL,
            'phone' => ContactConstants::PHONE,
        ], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'first_name' => ContactConstants::FIRST_NAME,
                    'last_name' => ContactConstants::LAST_NAME,
                    'email' => ContactConstants::EMAIL,
                    'phone' => ContactConstants::PHONE,
                    'user_id' => 1
                ]
            ]);
    }

    public function test_create_failed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => ContactConstants::LAST_NAME,
            'email' => 'johndoe',
            'phone' => ContactConstants::PHONE,
        ], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function test_create_unauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => ContactConstants::FIRST_NAME,
            'last_name' => ContactConstants::LAST_NAME,
            'email' => ContactConstants::EMAIL,
            'phone' => ContactConstants::PHONE,
        ], [
            'Authorization' => 'invalid-token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized.'
                    ]
                ]
            ]);
    }

    public function test_get_contact_by_id_successfully()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => ContactConstants::FIRST_NAME,
                    'last_name' => ContactConstants::LAST_NAME,
                    'email' => ContactConstants::EMAIL,
                    'phone' => ContactConstants::PHONE,
                ]
            ]);
    }
}
