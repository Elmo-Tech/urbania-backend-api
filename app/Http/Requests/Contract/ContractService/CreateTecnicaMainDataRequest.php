<?php

namespace App\Http\Requests\Contract\ContractService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateTecnicaMainDataRequest extends FormRequest
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
            "contractId"=>"",
            'contractServiceId' => "",
            'noteSecOne'=> "",
            'tipologia'=> "",
            'dataApozione'=> "",
            'dataApprovazione'=> "",
            'pubblicazioneBurl'=> "",
            'note'=> "",
        ];
    }

    public function failedValidation(Validator $validator) 
    {
        throw new HttpResponseException(response()->json([
            'message'      => $validator->errors()
        ], 401));
    }

}
