<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class LoginUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'O e-mail informado não é válido.',
            'password.required' => 'O campo senha é obrigatório.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::channel('database')->warning('Erro de validação no login', [
            'errors' => $validator->errors()->toArray(),
            'ip'     => $this->ip(),
        ]);

        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'message' => 'Erro de validação',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
