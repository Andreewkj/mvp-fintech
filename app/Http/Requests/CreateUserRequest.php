<?php

namespace App\Http\Requests;

use App\Rules\CpfRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'full_name' => 'required|string',
            'email' => ['required', 'email'],
            'cpf' => ['required_without:cnpj' , new CpfRule()],
            'cnpj' => ['required_without:cpf'],
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        //TODO: trocar mensagens para o inglês
        return [
            'email.required' => 'É necessário informar o email ou o CPF para efetuar o login.',
            'email.email' => 'O email é inválido.',
            'cpf.required_without' => 'O CPF/CNPJ é obrigatório.',
            'cnpj.required_without' => 'O CPF/CNPJ é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
        ];
    }
}
