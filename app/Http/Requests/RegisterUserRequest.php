<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'O campo nome é obrigatório.',
            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'O e-mail informado não é válido.',
            'email.unique'      => 'Este e-mail já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min'      => 'A senha deve ter pelo menos 8 caracteres.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::channel('database')->warning('Erro de validação no registro', [
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
