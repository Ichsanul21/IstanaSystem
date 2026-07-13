<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Tracking\TrackingService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function show($token)
    {
        $trackingService = app(TrackingService::class);
        $order = $trackingService->findByToken($token);

        if (!$order) {
            abort(404);
        }

        if (session('tracking_verified_'.$token)) {
            $data = $trackingService->getOrderStatus($order);
            return view('tracking.show', array_merge($data, ['order' => $order]));
        }

        return view('tracking.show', compact('order'));
    }

    public function verify(Request $request, $token)
    {
        $request->validate(['pin' => 'required|string|size:2']);

        $trackingService = app(TrackingService::class);
        $order = $trackingService->findByToken($token);

        if (!$order) {
            return response()->json(['error' => 'Token tidak valid.'], 404);
        }

        if (!$trackingService->verifyPin($order, $request->pin)) {
            return response()->json(['error' => 'PIN salah.'], 422);
        }

        session(['tracking_verified_'.$token => true]);

        return response()->json(['success' => true]);
    }
}
