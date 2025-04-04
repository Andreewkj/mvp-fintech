<?php

namespace App\Http\Controllers;

use App\Domain\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function createWallet(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        try {
            (new WalletService())->createWallet($data);
            return response()->json([
                'message' => "transfer completed successfully"
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error creating wallet for user: {$data['user_id']}, error: {$e->getMessage()}");

            return response()->json([
                'message' => "Error creating your wallet"
            ], 400);
        }

    }
}
