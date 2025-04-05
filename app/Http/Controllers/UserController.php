<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Requests\CreateLoginRequest;
use App\Domain\Services\UserService;
use App\Domain\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = (new CreateLoginRequest(
            $request->only('email', 'password')
        ))->validate();

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('apiToken')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'The provided credentials do not match our records.'], 401);
    }

    public function store(Request $request): User | JsonResponse
    {
        try {
            $data = (new CreateUserRequest($request->all()))->validate();
            return (new UserService())->createUser($data);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error("Error creating user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error creating your user"
            ], 500);
        }
    }
}
