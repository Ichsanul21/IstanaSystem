<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/webhook/midtrans')) {
            $signature = $request->input('signature_key');
            $orderId = $request->input('order_id');
            $grossAmount = $request->input('gross_amount');
            $serverKey = config('services.midtrans.server_key');

            if (!$serverKey) {
                return response()->json(['message' => 'Server key not configured'], 500);
            }

            $expected = hash('sha512', $orderId . $grossAmount . $serverKey);

            if ($signature !== $expected) {
                return response()->json(['message' => 'Invalid signature'], 401);
            }
        }

        return $next($request);
    }
}
