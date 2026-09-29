<?php

namespace App\Http\Requests\ClientOuterTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateClientOuterTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'clientOuterTicketId' => 'required|integer',
            'firstname' => 'required_unless:acceptStatus,2',
            'lastname' => 'required_unless:acceptStatus,2',
            'cf' => 'required_unless:acceptStatus,2',
            'pIva' => 'nullable',
            'ragioneSociale' => 'nullable',
            'delegatedFirstname' => 'nullable',
            'delegatedLastname' => 'nullable',
            'delegatedPhone' => 'nullable',
            'message' => 'nullable',
            'description' => 'nullable',
            'parameterId' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'address' => 'nullable',
            'city' => 'nullable',
            'state' => 'nullable',
            'stats' => 'nullable',
            'status' => 'nullable|integer|in:0,1,2,3',
            "clientId" => "required_unless:acceptStatus,2",
            'contractId' => 'nullable',
            'serviceId' => 'nullable',
            'esito' => 'nullable',
            'notifyDate' => 'nullable',
            'connectTypeId' => 'nullable',
            'anno' => 'nullable',
            'note' => 'nullable',
            'segnalazione' => 'nullable',
            'urgenza' => 'nullable',
            'endDate' => 'nullable',
            'istanzaParameterId' => 'nullable',
            'tipologiaIstanza' => 'nullable',
            'delegatedRoleId' => 'nullable',
            'ticketClientId' => 'nullable',
            'acceptStatus' => 'nullable|integer|in:0,1,2',
            'rejectionReason' => 'exclude_unless:acceptStatus,2|required|string|max:5000',
            'workerId' => 'nullable'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()
        ], 401));
    }
}
