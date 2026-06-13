<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateContractParamServiceRequest extends FormRequest
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
            "services.*.serviceId" => "",
            "services.*.startDate" => "",
            "services.*.endDate" => "",
            "services.*.paymentIds" => "",
            "services.*.accountNumber" => "",
            "services.*.caricoId" => "",
            "services.*.note" => ""

        ];
    }

    public function failedValidation(Validator $validator) 
    {
        throw new HttpResponseException(response()->json([
            'message'      => $validator->errors()
        ], 401));
    }

}
