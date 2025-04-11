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
use InvalidArgumentException;

class TransferController extends Controller
{
    public function __construct(
        protected CreateTransferRequest $createTransferRequest,
        protected TransferService $transferService
    )
    {}

    public function makeTransfer(Request $request): JsonResponse
    {
        try {
            $data = $this->createTransferRequest->validate($request->all());
            $this->transferService->transfer($data);

            return response()->json([
                'message' => "transfer completed successfully"
            ], 201);
        }catch (InvalidArgumentException | WalletException | TransferException $e) {
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
