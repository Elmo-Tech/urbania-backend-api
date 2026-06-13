<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AiCreateReservationRequest extends FormRequest
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
            'firstname' => 'nullable',
            'lastname' => 'nullable',
            'cf' => 'nullable',
            'pIva' => 'nullable',
            'ragioneSociale' => 'nullable',
            'delegatedFirstname' => 'nullable',
            'delegatedLastname' => 'nullable',
            'message' => 'nullable',
            'parameterName' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'date' => 'required',
            'duration' => 'nullable',
            "status" => "required",
            "clientName" => "required",
            'refuseReason' => 'nullable'
        ];

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()
        ], 401));
    }
}
