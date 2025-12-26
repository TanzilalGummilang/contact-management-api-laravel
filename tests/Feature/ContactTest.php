<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Tests\TestCase;

class ContactTest extends TestCase
{
    private string $token = 'test';
    private string $firstName = 'John';
    private string $lastName = 'Doe';
    private string $email = 'johndoe@test.com';
    private string $phone = '088800000001';

    public function test_create_successfully()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
        ], [
            'Authorization' => $this->token
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'user_id' => 1
                ]
            ]);
    }

    public function test_create_failed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => $this->lastName,
            'email' => 'johndoe',
            'phone' => $this->phone,
        ], [
            'Authorization' => $this->token
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
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
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
}
