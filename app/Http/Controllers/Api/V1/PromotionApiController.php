<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Services\Promotion\PromotionService;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class PromotionApiController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService,
        protected DiscountService $discountService
    ) {}

    public function index(Request $request)
    {
        $query = Promotion::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('branches', fn($q) => $q->where('branch_id', $request->branch_id));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function show($id)
    {
        return response()->json(['data' => Promotion::with('branches')->findOrFail($id)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:percentage,fixed,buy_get',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_subtotal' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $promotion = Promotion::create($data);

        if (!empty($data['branch_ids'])) {
            $promotion->branches()->sync($data['branch_ids']);
        }

        return response()->json(['success' => true, 'data' => $promotion], 201);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $data = $request->validate([
            'code' => "nullable|string|max:50|unique:promotions,code,{$id}",
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:percentage,fixed,buy_get',
            'value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_subtotal' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $promotion->update($data);

        if (array_key_exists('branch_ids', $data)) {
            $promotion->branches()->sync($data['branch_ids']);
        }

        return response()->json(['success' => true, 'message' => 'Promotion updated']);
    }

    public function eligible($orderId)
    {
        $promotions = $this->promotionService->getEligiblePromotions(currentBranchId());

        return response()->json(['data' => $promotions->map(fn($p) => [
            'id' => $p->id,
            'code' => $p->code,
            'name' => $p->name,
            'type' => $p->type,
            'value' => (float) $p->value,
            'max_discount' => (float) ($p->max_discount ?? 0),
        ])]);
    }

    public function calculate(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        $order = \App\Models\Order::findOrFail($request->order_id);
        $subtotal = (float) $order->total_amount;
        $discount = $this->discountService->calculatePromotionDiscount($promotion, $subtotal);

        $pointDiscount = 0;
        if ($request->filled('redeem_points') && $request->redeem_points > 0) {
            $pointDiscount = $request->redeem_points / 100;
        }

        $grandTotal = max(0, $subtotal - $discount - $pointDiscount);

        return response()->json(['data' => [
            'subtotal' => $subtotal,
            'promotion_discount' => $discount,
            'point_discount' => $pointDiscount,
            'grand_total' => $grandTotal,
        ]]);
    }
}
