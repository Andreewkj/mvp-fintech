<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Adapters\PicPayAdapter;
use App\Domain\Repositories\WalletRepository;
use App\Domain\Requests\CreateTransferRequest;
use App\Domain\Services\TransferService;
use App\Domain\Services\UserService;
use App\Domain\Services\WalletService;
use App\Exceptions\TransferException;
use App\Exceptions\WalletException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    private TransferService $transferService;
    private WalletService $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService(
            new WalletRepository(),
            new UserService(),
            null,
            new PicPayAdapter()
        );

        $this->transferService = new TransferService($this->walletService);
    }

    public function makeTransfer(Request $request): JsonResponse
    {
        try {
            $data = (new CreateTransferRequest($request->all()))->validate();
            $this->transferService->transfer($data);

            return response()->json([
                'message' => "transfer completed successfully"
            ], 201);
        }catch (\InvalidArgumentException | WalletException | TransferException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }catch (\Exception $e) {
            Log::error("Error creating transfer, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Apparently something went wrong with your transfer, but don't worry, we will rollback the values for you"
            ], 500);
        }
    }
}
