<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
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
            return ApiResponse::error('Invalid signature.', null, 403);
        }

        $this->midtransService->handleWebhook($payload);

        return response()->json(['ok' => true]);
    }
}
