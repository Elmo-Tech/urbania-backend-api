<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            "userId" => "",
            //"username"=> "required|unique:users,username" . $this->userId,
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($this->userId),
            ],
            "firstname"=> "",
            "lastname"=> "",
            "newPassword"=> "",
            "oldPassword"=> "",
            "email"=> "",
            "status"=>"",
            'roleType' => ""
        ];
    }

    public function failedValidation(Validator $validator) 

    {

        throw new HttpResponseException(response()->json([

            'message'      => $validator->errors()

        ], 401));

    }

}
