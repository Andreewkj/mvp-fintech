<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\TransferService;
use App\Application\Services\WalletService;
use App\Domain\Contracts\LoggerInterface;
use App\Domain\Enums\HttpStatusCodeEnum;
use App\Domain\Exceptions\WalletException;
use App\Http\Requests\CreateWalletRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function __construct(
        private readonly CreateWalletRequest $createWalletRequest,
        private readonly WalletService $walletService,
        private readonly LoggerInterface $logger
    )
    {}

    public function createWallet(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        try {
            $createWalletDto = $this->createWalletRequest->validate($data);
            $this->walletService->createWallet($createWalletDto);

            return response()->json([
                'message' => "Your wallet was created successfully"
            ], HttpStatusCodeEnum::CREATED->value);
        } catch (\InvalidArgumentException | WalletException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpStatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        } catch (\Exception $e) {
            $this->logger->error("Error creating wallet for user: {$data['user_id']}, error: {$e->getMessage()}");

            return response()->json([
                'message' => "Error creating your wallet"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }
}
