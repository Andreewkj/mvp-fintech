<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Requests\CreateTransferRequest;
use App\Domain\Services\TransferService;
use App\Exceptions\TransferException;
use App\Exceptions\WalletException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    private TransferService $transferService;

    public function __construct()
    {
        $this->transferService = new TransferService();
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
