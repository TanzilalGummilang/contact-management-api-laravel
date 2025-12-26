<?php

namespace Tests\Feature;

use App\Constants\UserConstants;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_register_successfully()
    {
        $this->post('/api/users/register', [
            'name' => UserConstants::NAME,
            'username' => UserConstants::USERNAME,
            'password' => UserConstants::PASSWORD,
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => UserConstants::NAME,
                    'username' => UserConstants::USERNAME,
                ]
            ]);
    }

    public function test_register_required()
    {
        $this->post('/api/users/register', [
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
        $this->post('/api/users/register', [
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
        $this->post('/api/users/register', [
            'name' => UserConstants::NAME,
            'username' => UserConstants::USERNAME,
            'password' => UserConstants::PASSWORD,
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
            'username' => UserConstants::USERNAME,
            'password' => UserConstants::PASSWORD,
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => UserConstants::NAME,
                    'username' => UserConstants::USERNAME,
                ]
            ]);

        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        self::assertNotNull($user->token);
    }

    public function test_login_username_not_found()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'johnwick',
            'password' => UserConstants::PASSWORD,
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
            'username' => UserConstants::USERNAME,
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

    public function test_get_current_user_successfully()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => UserConstants::USERNAME,
                    'name' => UserConstants::NAME,
                ]
            ]);
    }
    public function test_get_current_user_unauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized.'],
                ]
            ]);
    }
    public function test_get_current_user_invalid_token()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => 'invalid-token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized.'],
                ]
            ]);
    }

    public function test_update_current_user_name_successfully()
    {

        $this->seed(UserSeeder::class);
        $oldName = User::query()->where('username', UserConstants::USERNAME)->first()->name;

        $this->patch('/api/users/current', [
            'name' => 'John Wick',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => UserConstants::USERNAME,
                    'name' => 'John Wick',
                ]
            ]);

        $newName = User::query()->where('username', UserConstants::USERNAME)->first()->name;
        self::assertNotEquals($oldName, $newName);
    }

    public function test_update_current_user_password_successfully()
    {

        $this->seed(UserSeeder::class);
        $oldPassword = User::query()->where('username', UserConstants::USERNAME)->first()->password;

        $this->patch('/api/users/current', [
            'password' => 'newtest',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => UserConstants::USERNAME,
                    'name' => UserConstants::NAME,
                ]
            ]);

        $newPassword = User::query()->where('username', UserConstants::USERNAME)->first()->password;
        self::assertNotEquals($oldPassword, $newPassword);
    }

    public function test_update_current_user_min_length()
    {
        $this->seed(UserSeeder::class);

        $this->patch('/api/users/current', [
            'name' => 'John',
            'password' => 'wi',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field must be at least 6 characters.'],
                    'password' => ['The password field must be at least 3 characters.'],
                ]
            ]);
    }

    public function test_logout_successfully()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/logout', [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);

        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        self::assertNull($user->token);
    }

    public function test_logout_failed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/logout', [], [
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
