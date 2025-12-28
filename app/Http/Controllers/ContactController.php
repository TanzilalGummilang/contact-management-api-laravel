<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\Interface\ContactServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private ContactServiceInterface $contactService;

    public function __construct(ContactServiceInterface $contactService)
    {
        $this->contactService = $contactService;
    }
    
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($id, $user);

        return new ContactResource($contact);
    }

    public function update(int $id, ContactCreateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($id, $user);
        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return (new ContactResource($contact));
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->contactService->getContactById($id, $user);
        $contact->delete();

        return response()->json(['data' => true], 200);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $contacts = Contact::query()->where('user_id', $user->id);

        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('first_name', 'like', '%' . $name . '%');
                    $builder->orWhere('last_name', 'like', '%' . $name . '%');
                });
            }

            $email = $request->input('email');
            if ($email) {
                $builder->where('email', 'like', '%' . $email . '%');
            }

            $phone = $request->input('phone');
            if ($phone) {
                $builder->where('phone', 'like', '%' . $phone . '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return (new ContactCollection($contacts));
    }
}
