<?php

namespace App\Http\Requests\Ticket\TicketClient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class CreateTicketClientRequest extends FormRequest
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
            'ticketClientId' => 'required_without_all:firstname,lastname,companyName',
            'firstname' => 'required_without_all:ticketClientId,lastname,companyName',
            'lastname' => 'required_without_all:ticketClientId,firstname,companyName',
            'companyName' => '',
            'nationalNumber' => '',
        ];
    }

    public function failedValidation(Validator $validator) 
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()
        ], 401));
    }

}
