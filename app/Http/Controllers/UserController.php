<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\UserService;
use App\Domain\Entities\User;
use App\Domain\Enums\HttpStatusCodeEnum;
use App\Http\Requests\CreateLoginRequest;
use App\Http\Requests\CreateUserRequest;
use App\Presenters\UserPresenter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UserController extends Controller
{
    public function __construct(
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
            ], HttpStatusCodeEnum::UNAUTHORIZED->value);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpStatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        } catch (Exception $e) {
            Log::error("Error logging in user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error logging in your user"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }

    public function store(Request $request): User | JsonResponse
    {
        try {
            $data = $this->createUserRequest->validate($request->all());
            $user = $this->userService->createUser($data);

            return response()->json(UserPresenter::fromEntity($user));
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpStatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        } catch (Exception $e) {
            Log::error("Error creating user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error creating your user"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }
}
