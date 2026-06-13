<?php

namespace App\Http\Requests\ClientOuterTicket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateClientOuterTicketRequest extends FormRequest
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
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'cf' => 'nullable|string|max:20',
            'pIva' => 'nullable|string|max:20',
            'ragioneSociale' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'delegatedFirstname' => 'nullable|string|max:255',
            'delegatedLastname' => 'nullable|string|max:255',
            'delegatedPhone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
            'anno' => 'nullable|integer',
            'emailToken' => 'nullable|string|max:255',
            'status' => 'required',
            'serviceId' => 'nullable|integer',
            'istanzaParameterId' => 'nullable|integer',
            'clientId' => 'required|integer',
            'delegatedRoleId' => 'nullable|integer',
        ];

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()
        ], 401));
    }
}
