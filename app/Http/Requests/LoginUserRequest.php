<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //TODO: testar regra
        
        return [
            'email' => 'required|email',
            'cpf' => ['required_unless:email', new CpfRule()],
            'cnpj' => ['required_unless:email,cpf'],
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'É necessário informar o email ou o CPF para efetuar o login.',
            'email.email' => 'O email é inválido.',
            'cpf.required_unless' => 'O CPF é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }
}
