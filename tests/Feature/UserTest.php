<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserTest extends TestCase
{
    private string $name = 'John Doe';
    private string $username = 'johndoe';
    private string $password = 'test';
    
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

    public function test_login_successfully()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => $this->username,
            'password' => $this->password,
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => $this->name,
                    'username' => $this->username,
                ]
            ]);

        $user = User::query()->where('username', $this->username)->first();
        self::assertNotNull($user->token);
    }

    public function test_login_username_not_found()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'johnwick',
            'password' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['username or password is wrong.'],
                ]
            ]);
    }

    public function test_login_password_wrong()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'johndoe',
            'password' => 'testing',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['username or password is wrong.'],
                ]
            ]);
    }

    public function test_login_min_length()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'name' => 'John',
            'username' => 'john',
            'password' => 'pa',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => ['The username field must be at least 6 characters.'],
                    'password' => ['The password field must be at least 3 characters.'],
                ]
            ]);
    }
}
