<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\User;
use App\Services\Interface\ContactServiceInterface;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactService implements ContactServiceInterface
{
    public function getContactById(int $contactId, User $user): Contact
    {
        $contact = Contact::query()->where('id', $contactId)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found.'
                    ]
                ]
            ], 404));
        }

        return $contact;
    }
}