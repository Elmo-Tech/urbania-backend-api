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
            'clientOuterTicketId' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'cf' => 'required',
            'pIva' => 'nullable',
            'ragioneSociale' => 'nullable',
            'delegatedFirstname' => 'nullable',
            'delegatedLastname' => 'nullable',
            'delegatedPhone' => 'nullable',
            'message' => 'nullable',
            'parameterId' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'address' => 'nullable',
            "status" => "required",
            "clientId" => "required",
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
            'delegatedRoleId' => 'nullable',
            'ticketClientId' => 'nullable',
            'acceptStatus' => 'nullable',
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
