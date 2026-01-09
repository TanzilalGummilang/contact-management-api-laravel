<?php

namespace Tests\Feature;

use App\Constants\AddressConstants;
use App\Constants\UserConstants;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_create_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => AddressConstants::STREET,
            'city' => AddressConstants::CITY,
            'province' => AddressConstants::PROVINCE,
            'country' => AddressConstants::COUNTRY,
            'postal_code' => AddressConstants::POSTAL_CODE,
        ], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(201)->assertJson([
            'data' => [
                'street' => AddressConstants::STREET,
                'city' => AddressConstants::CITY,
                'province' => AddressConstants::PROVINCE,
                'country' => AddressConstants::COUNTRY,
                'postal_code' => AddressConstants::POSTAL_CODE,
            ]
        ]);
    }

    public function test_create_failed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => AddressConstants::STREET,
                'city' => AddressConstants::CITY,
                'province' => AddressConstants::PROVINCE,
                'country' => '',
                'postal_code' => AddressConstants::POSTAL_CODE,
            ],
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    public function test_create_but_contact_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->post(
            '/api/contacts/' . ($contact->id + 1) . '/addresses',
            [
                'street' => AddressConstants::STREET,
                'city' => AddressConstants::CITY,
                'province' => AddressConstants::PROVINCE,
                'country' => AddressConstants::COUNTRY,
                'postal_code' => AddressConstants::POSTAL_CODE,
            ],
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found.']
                ]
            ]);
    }

    public function test_get_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();
        $address = Address::query()->where('contact_id', $contact->id)->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => AddressConstants::STREET,
                    'city' => AddressConstants::CITY,
                    'province' => AddressConstants::PROVINCE,
                    'country' => AddressConstants::COUNTRY,
                    'postal_code' => AddressConstants::POSTAL_CODE,
                ]
            ]);
    }

    public function test_get_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 100), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Address not found.']
                ]
            ]);
    }

    public function test_update_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();
        $address = Address::query()->where('contact_id', $contact->id)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'Forgotten Street',
                'city' => 'Forgotten City',
                'province' => 'Forgotten Province',
                'country' => 'Forgotten Country',
                'postal_code' => '12345',
            ],
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Forgotten Street',
                    'city' => 'Forgotten City',
                    'province' => 'Forgotten Province',
                    'country' => 'Forgotten Country',
                    'postal_code' => '12345',
                ]
            ]);
    }

    public function test_update_failed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();
        $address = Address::query()->where('contact_id', $contact->id)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'Forgotten Street',
                'city' => 'Forgotten City',
                'province' => 'Forgotten Province',
                'country' => '',
                'postal_code' => '12345',
            ],
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    public function test_update_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();
        $address = Address::query()->where('contact_id', $contact->id)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 100),
            [
                'street' => 'Forgotten Street',
                'city' => 'Forgotten City',
                'province' => 'Forgotten Province',
                'country' => 'Forgotten Country',
                'postal_code' => '12345',
            ],
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Address not found.']
                ]
            ]);
    }

    public function test_delete_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();
        $address = Address::query()->where('contact_id', $contact->id)->first();

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(200)
            ->assertJson(['data' => true]);
    }

    public function test_delete_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();
        $address = Address::query()->where('contact_id', $contact->id)->first();

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 100), [], [
            'Authorization' => UserConstants::TOKEN
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Address not found.']
                ]
            ]);
    }

    public function test_list_success()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->get(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'street' => AddressConstants::STREET,
                        'city' => AddressConstants::CITY,
                        'province' => AddressConstants::PROVINCE,
                        'country' => AddressConstants::COUNTRY,
                        'postal_code' => AddressConstants::POSTAL_CODE,
                    ]
                ]
            ]);
    }

    public function test_list_not_found()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $user = User::query()->where('username', UserConstants::USERNAME)->first();
        $contact = Contact::query()->where('user_id', $user->id)->first();

        $this->get(
            '/api/contacts/' . ($contact->id + 1) . '/addresses',
            [
                'Authorization' => UserConstants::TOKEN
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['Contact not found.']
                ]
            ]);
    }
}
