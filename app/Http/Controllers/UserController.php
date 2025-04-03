<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $userData = $request->all();
    }

    public function store(Request $request)
    {
        return $request->all();
    }
}
