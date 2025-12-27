<?php

namespace Tests\Feature;

use App\Constants\ContactConstants;
use App\Constants\UserConstants;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
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

    public function test_get_contact_by_id_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found.'
                    ]
                ]
            ]);
    }

    public function test_get_other_user_contact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => UserConstants::TOKEN . '2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Contact not found.'
                    ]
                ]
            ]);
    }

    public function test_update_contact_successfully()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test2@test.com',
            'phone' => '',
        ], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test2',
                    'last_name' => 'test2',
                    'email' => 'test2@test.com',
                    'phone' => '',
                ]
            ]);
    }

    public function test_update_validation_error()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'test2',
            'email' => 'test2@test.com',
            'phone' => '1111112',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ]
                ]
            ]);
    }

    public function test_delete_contact_successfully()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(200)
            ->assertJson(['data' => true]);
    }

    public function test_delete_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . ($contact->id + 1), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "Contact not found."
                    ]
                ]
            ]);
    }

    public function test_search_contacts_by_first_name()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts/search?name=first', [
            'Authorization' => UserConstants::TOKEN
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function test_search_contacts_by_last_name()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts/search?name=last', [
            'Authorization' => UserConstants::TOKEN
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function test_search_contacts_by_email()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts/search?email=test', [
            'Authorization' => UserConstants::TOKEN
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function test_search_contacts_by_phone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts/search?phone=0888', [
            'Authorization' => UserConstants::TOKEN
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function test_search_contacts_not_found()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts/search?name=stranger', [
            'Authorization' => UserConstants::TOKEN
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function test_search_contacts_with_page()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts/search?size=5&page=2', [
            'Authorization' => UserConstants::TOKEN
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
