<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Models\Entities\Cnpj;
use App\Models\Entities\Cpf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $userData = $request->all();
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->all();
        $cpf = isset($data['cpf']) ? (new Cpf($data['cpf']))->getValue() : null;
        $cnpj = isset($data['cnpj']) ? (new Cnpj($data['cpf']))->getValue() : null;

        //TODO: Revisar
        $user = (new User())->create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cpf' => $cpf,
            'cnpj' => $cnpj
        ]);

        dd($user);

        return response()->json($request->all());
    }
}
