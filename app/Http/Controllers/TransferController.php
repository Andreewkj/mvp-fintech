<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Services\TransferService;
use App\Domain\Enums\HttpStatusCodeEnum;
use App\Domain\Exceptions\TransferException;
use App\Http\Requests\CreateTransferRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class TransferController extends Controller
{
    public function __construct(
        private readonly CreateTransferRequest $createTransferRequest,
        private readonly TransferService $transferService
    )
    {}

    public function makeTransfer(Request $request): JsonResponse
    {
        try {
            $requestData = $request->all();
            $requestData['payer_id'] = auth()->user()->id;

            $makeTransferDto = $this->createTransferRequest->validate($requestData);
            $this->transferService->transfer($makeTransferDto);

            return response()->json([
                'message' => "transfer completed successfully"
            ], HttpStatusCodeEnum::CREATED->value);
        } catch (InvalidArgumentException | TransferException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], HttpStatusCodeEnum::UNPROCESSABLE_ENTITY->value);
        } catch (Exception $e) {
            Log::error("Error creating transfer, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Apparently something went wrong with your transfer, but don't worry, we will rollback the values for you"
            ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }
    }
}
