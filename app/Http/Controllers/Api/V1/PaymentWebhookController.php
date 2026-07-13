<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Payment\MidtransService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(protected MidtransService $midtransService) {}

    public function midtrans(Request $request)
    {
        $payload = $request->all();

        if (!$this->midtransService->verifyWebhookSignature($payload)) {
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $this->midtransService->handleWebhook($payload);

        return response()->json(['message' => 'OK']);
    }
}
