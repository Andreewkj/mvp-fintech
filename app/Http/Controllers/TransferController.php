<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    // TODO: implementar Request
    public function makeTransfer(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            (new TransferService())->transfer($data);
            return response()->json([
                'message' => "transfer completed successfully"
            ], 201);
        }catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
        catch (\Exception $e) {
            Log::error("Error creating transfer for user: {$data['payee_id']}, error: {$e->getMessage()}");
            return response()->json([
                'message' => "Apparently something went wrong with your transfer, but don't worry, we will rollback the values for you"
            ], 500);
        }
    }
}
