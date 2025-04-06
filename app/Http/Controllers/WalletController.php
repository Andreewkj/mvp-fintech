<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Repositories\WalletRepository;
use App\Domain\Requests\CreateWalletRequest;
use App\Domain\Services\TransferService;
use App\Domain\Services\UserService;
use App\Domain\Services\WalletService;
use App\Exceptions\WalletException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService(
            new WalletRepository(),
            new UserService(),
            null,
            null
        );
    }

    public function createWallet(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        try {
            $data = (new CreateWalletRequest($data))->validate();
            $this->walletService->createWallet($data);

            return response()->json([
                'message' => "Wallet was created successfully"
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
