<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Services\Interface\ContactServiceInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private ContactServiceInterface $contactService;

    public function __construct(ContactServiceInterface $contactService)
    {
        $this->contactService = $contactService;
    }

    public function create(int $contactId, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($contactId, $user);
        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $contactId, int $addressId): AddressResource
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($contactId, $user);
        $address = $this->getAddressById($addressId, $contact);

        return (new AddressResource($address));
    }

    public function update(int $contactId, int $addressId, AddressCreateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($contactId, $user);
        $address = $this->getAddressById($addressId, $contact);
        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return (new AddressResource($address));
    }

    public function delete(int $contactId, int $addressId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($contactId, $user);
        $address = $this->getAddressById($addressId, $contact);
        $address->delete();

        return response()->json(['data' => true], 200);
    }

    public function list(int $contactId): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($contactId, $user);
        $addresses = Address::query()->where('contact_id', $contact->id)->get();

        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
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
