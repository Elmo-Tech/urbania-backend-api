<?php

namespace App\Http\Requests\Client\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
//use Illuminate\Support\Facades\Validator; use code below it to activate failedValidation method
use Illuminate\Contracts\Validation\Validator;

class CreateSingleAddressRequest extends FormRequest
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
            'clientId'=> 'required',
            'address'=> '',
            'city'=> '',
            'state'=> '',
            'postalCode'=> '',
            'addressTypeId'=> '',
        ];
    }

    public function failedValidation(Validator $validator) 
    {
        throw new HttpResponseException(response()->json([
            'message'      => $validator->errors()
        ], 401));
    }

}
