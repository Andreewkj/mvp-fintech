<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\TransferService;
use App\Application\Services\WalletService;
use App\Domain\Requests\CreateWalletRequest;
use App\Exceptions\WalletException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function __construct(
        protected CreateWalletRequest $createWalletRequest,
        protected WalletService $walletService,
        protected TransferService $transferService
    )
    {}

    public function createWallet(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        try {
            $data = $this->createWalletRequest->validate($data);
            $this->walletService->createWallet($data);

            return response()->json([
                'message' => "WalletModel was created successfully"
            ], 201);
        } catch (\InvalidArgumentException | WalletException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error("Error creating wallet for user: {$data['user_id']}, error: {$e->getMessage()}");

            return response()->json([
                'message' => "Error creating your wallet"
            ], 500);
        }
    }
}
