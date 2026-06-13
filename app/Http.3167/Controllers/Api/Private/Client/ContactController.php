<?php

namespace App\Http\Controllers\Api\Private\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Contact\CreateSingleContactRequest;
use App\Http\Requests\Client\Contact\UpdateSingleContactRequest;
use App\Services\Client\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->middleware('auth:api');
        $this->contactService = $contactService;
    }

    public function create(CreateSingleContactRequest $contactRequest){

        return $this->contactService->createContact($contactRequest->validated());

    }

    public function edit(Request $request){

        return $this->contactService->editContact($request->contactId);

    }

    public function update(UpdateSingleContactRequest $contactRequest){

        return $this->contactService->updateContact($contactRequest->validated());
    }

    /**
    * Remove the specified resource from storage.
    */
    public function delete(Request $request, ContactService $contactService)
    {   
        return $contactService->deleteContact($request->contactId);
    }
}
