<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(int $contactId, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContactById($contactId, $user);
        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $contactId, int $addressId): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContactById($contactId, $user);
        $address = $this->getAddressById($addressId, $contact);

        return (new AddressResource($address));
    }

    private function getContactById(int $contactId, User $user): Contact
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

    private function getAddressById(int $addressId, Contact $contact): Address
    {
        $address = Address::query()->where('contact_id', $contact->id)->where('id', $addressId)->first();

        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Address not found.'
                    ]
                ]
            ], 404));
        }

        return $address;
    }
}
