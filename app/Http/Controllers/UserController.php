<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\DTO\User\ResponseUserDTO;
use App\Application\Services\UserService;
use App\Domain\Contracts\LoggerInterface;
use App\Domain\Enums\HttpStatusCodeEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Exception;

class UserController extends Controller
{
    public function __construct(
        private readonly CreateUserRequest $createUserRequest,
        private readonly UserService $userService,
        private readonly LoginUserRequest $createLoginRequest,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Login User
     */
    #[BodyParameter(name: 'email', description: 'User email address', required: true, type: 'string', example: 'user@example.com')]
    #[BodyParameter(name: 'password', description: 'User password', required: true, type: 'string', example: '123456')]
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
            $this->logger->error("Error logging in user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error logging in your user"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }

    /**
     * Create User
     */
    #[BodyParameter(name: 'full_name', description: 'User full name', required: true, type: 'string', example: 'Andreew Januário')]
    #[BodyParameter(name: 'email', description: 'User email address', required: true, type: 'string', example: 'andreew@example.com')]
    #[BodyParameter(name: 'phone', description: 'User phone number', required: true, type: 'string', example: '31993920011')]
    #[BodyParameter(name: 'password', description: 'User password', required: true, type: 'string', example: '123456')]
    #[BodyParameter(name: 'cpf', description: 'Brazilian CPF document (optional if CNPJ is provided)', required: false, type: 'string', example: '123.456.789-09')]
    #[BodyParameter(name: 'cnpj', description: 'Brazilian CNPJ document (optional if CPF is provided)', required: false, type: 'string', example: '12.345.678/0001-99')]
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
            $this->logger->error("Error creating user, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Error creating user"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }
}
