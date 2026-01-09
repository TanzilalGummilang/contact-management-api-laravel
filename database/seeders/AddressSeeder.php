<?php

namespace Database\Seeders;

use App\Constants\AddressConstants;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->limit(1)->first();
        Address::create([
            'contact_id' => $contact->id,
            'street' => AddressConstants::STREET,
            'city' => AddressConstants::CITY,
            'province' => AddressConstants::PROVINCE,
            'country' => AddressConstants::COUNTRY,
            'postal_code' => AddressConstants::POSTAL_CODE
        ]);

        Address::create([
            'contact_id' => $contact->id,
            'street' => AddressConstants::STREET . '2',
            'city' => AddressConstants::CITY . '2',
            'province' => AddressConstants::PROVINCE . '2',
            'country' => AddressConstants::COUNTRY . '2',
            'postal_code' => AddressConstants::POSTAL_CODE . '2'
        ]);
    }
}
