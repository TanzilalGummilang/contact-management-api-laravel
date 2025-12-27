<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function createContact(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function getContactById(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::query()->where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found.'
                    ]
                ]
            ], 404));
        }

        return new ContactResource($contact);
    }

    public function updateContact(int $id, ContactCreateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::query()->where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found.'
                    ]
                ]
            ], 404));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return (new ContactResource($contact));
    }
}
