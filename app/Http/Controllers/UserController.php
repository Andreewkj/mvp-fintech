<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Repositories\UserRepository;
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
    public function __construct(
        protected UserRepository $userRepository,
        protected CreateUserRequest $createUserRequest,
        protected UserService $userService,
        protected CreateLoginRequest $createLoginRequest
    ) {}
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $this->createLoginRequest->validate($request->only('email', 'password'));

            if (Auth::attempt($credentials)) {
                $token = $request->user()->createToken('apiToken')->plainTextToken;
                return response()->json(['token' => $token], 200);
            }

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error("Error logging in user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error logging in your user"
            ], 500);
        }
    }

    public function store(Request $request): User | JsonResponse
    {
        try {
            $data = $this->createUserRequest->validate($request->all());
            return $this->userService->createUser($data);
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
