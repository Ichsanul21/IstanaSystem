<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Promotion;
use App\Models\PromotionBranch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('branches')->latest()->paginate(15);

        return view('promotions.index', compact('promotions'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();

        return view('promotions.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:percentage,fixed,buy_get',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_value' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $promotion = Promotion::create($data);

        if ($request->has('branches')) {
            $promotion->branches()->attach($request->branches);
        }

        return redirect()->route('admin.promotions.show', $promotion)->with('success', 'Promo berhasil ditambahkan.');
    }

    public function show(Promotion $promotion)
    {
        $promotion->load('branches', 'usages');

        return view('promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion)
    {
        $branches = Branch::where('is_active', true)->get();

        return view('promotions.edit', compact('promotion', 'branches'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code,' . $promotion->id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:percentage,fixed,buy_get',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_value' => 'nullable|integer|min:1',
            'total_usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $promotion->update($data);

        if ($request->has('branches')) {
            $promotion->branches()->sync($request->branches);
        } else {
            $promotion->branches()->detach();
        }

        return redirect()->route('admin.promotions.show', $promotion)->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promotion $promotion)
    {
        try {
            $promotion->delete();
            return redirect()->route('admin.promotions.index')->with('success', 'Promo berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.promotions.index')->with('error', 'Promo gagal dihapus.');
        }
    }

    public function toggleBranch(Promotion $promotion, Branch $branch)
    {
        $promotionBranch = PromotionBranch::firstOrNew([
            'promotion_id' => $promotion->id,
            'branch_id' => $branch->id,
        ]);

        $promotionBranch->is_active = !$promotionBranch->is_active;
        $promotionBranch->save();

        return back();
    }

    public function check(string $code): JsonResponse
    {
        $promotion = Promotion::where('code', $code)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promotion) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kadaluarsa.']);
        }

        return response()->json([
            'valid' => true,
            'promotion' => [
                'id' => $promotion->id,
                'code' => $promotion->code,
                'name' => $promotion->name,
                'type' => $promotion->type->value,
                'value' => $promotion->value,
                'min_order_amount' => $promotion->min_order_amount,
                'max_discount' => $promotion->max_discount,
            ],
        ]);
    }
}
