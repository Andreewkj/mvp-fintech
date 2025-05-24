<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\DTO\User\ResponseUserDTO;
use App\Application\Services\UserService;
use App\Domain\Enums\HttpStatusCodeEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class UserController extends Controller
{
    public function __construct(
        private readonly CreateUserRequest $createUserRequest,
        private readonly UserService $userService,
        private readonly LoginUserRequest $createLoginRequest
    ) {}
    public function login(Request $request): JsonResponse
    {
        try {
            $loginUserDto = $this->createLoginRequest->validate($request->only('email', 'password'));

            if (Auth::attempt($loginUserDto->toArray())) {
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

    public function store(Request $request): JsonResponse
    {
        try {
            $dto = $this->createUserRequest->validate($request->all());
            $user = $this->userService->createUser($dto);

            return response()->json(
                ResponseUserDTO::fromEntity($user)->toArray(),
                HttpStatusCodeEnum::CREATED->value
            );
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpStatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        } catch (Exception $e) {
            Log::error("Error creating user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error creating user"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }
}
