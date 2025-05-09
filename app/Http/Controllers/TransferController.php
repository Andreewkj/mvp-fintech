<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\TransferService;
use App\Domain\Enums\HttpStatusCodeEnum;
use App\Domain\Exceptions\TransferException;
use App\Http\Requests\CreateTransferRequest;
use Exception;
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
            $this->transferService->transfer($data, auth()->user()->id);

            return response()->json([
                'message' => "transfer completed successfully"
            ], HttpStatusCodeEnum::CREATED->value);
        } catch (InvalidArgumentException | TransferException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpStatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        } catch (Exception $e) {
            throw $e;
            Log::error("Error creating transfer, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Apparently something went wrong with your transfer, but don't worry, we will rollback the values for you"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }
}
