<?php

namespace App\Http\Requests\OuterLetter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class UpdateOuterLetterMobileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'outerLetterId' => '',
            'letterId' => '',
            'name' => '',
            'address' => '',
            'cap' => '',
            'city' => '',
            'province' => '',
            'internalCode' => '',
            'clientId' => '',
            'serviceId' => '',
            'year' => '',
            'cf' => '',
            'note'=> '',
            'isOpened' => '',
            'receivedDate' => '',
            'files' => '',
            'position' => '',
            'numero' => ['nullable']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => $validator->errors()
        ], 401));
    }

}
