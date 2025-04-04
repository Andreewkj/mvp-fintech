<?php

namespace App\Http\Controllers;

use App\Domain\Entities\Cnpj;
use App\Domain\Entities\Cpf;
use App\Domain\Services\UserService;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
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

        return (new UserService())->createUser($data);
    }
}
