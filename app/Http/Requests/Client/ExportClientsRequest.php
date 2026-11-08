<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ExportClientsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exportType' => ['required', 'string', Rule::in(['excel', 'csv', 'pdf'])],
            'clientId' => ['nullable', 'integer', 'min:0'],
            'tradeRegister' => ['nullable', 'string'],
            'cf' => ['nullable', 'string'],
            // The list also accepts a single digit as its legacy >= 0 fallback.
            'peopleNumber' => ['nullable', 'string', 'regex:/^(?:\d|(?:>=|<=|!=|<>|=|>|<)\|\d+)$/D'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
