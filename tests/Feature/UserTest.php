<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_register_successfully()
    {
        $this->post('/api/users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'password' => 'password',
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'username' => 'johndoe',
                ]
            ]);
    }

    public function test_register_required()
    {
        $this->post('/api/users', [
            'name' => '',
            'username' => '',
            'password' => '',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'username' => ['The username field is required.'],
                    'password' => ['The password field is required.'],  
                ]
            ]);
    }

    public function test_register_min_length()
    {
        $this->post('/api/users', [
            'name' => 'John',
            'username' => 'john',
            'password' => 'pa',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field must be at least 6 characters.'],
                    'username' => ['The username field must be at least 6 characters.'],
                    'password' => ['The password field must be at least 3 characters.'],
                ]
            ]);
    }

    public function test_register_username_exist()
    {
        $this->test_register_successfully();
        $this->post('/api/users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'password' => 'password',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [                    
                    'username' => ['The username has already been taken.'],
                ]
            ]);
    }
}
