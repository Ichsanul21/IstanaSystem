<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index()
    {
        $customers = Customer::forCurrentBranch()
            ->with('membershipTier')
            ->latest()
            ->paginate(15);

        return ApiResponse::paginate($customers);
    }

    public function show(Customer $customer)
    {
        $customer->load('membershipTier', 'orders');

        return ApiResponse::success($customer);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $customer = Customer::create(array_merge($data, [
            'code' => 'CUS-' . now()->format('YmdHis'),
            'branch_id' => currentBranchId(),
            'total_points' => 0,
            'lifetime_spending' => 0,
            'is_active' => true,
        ]));

        return ApiResponse::success($customer, null, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
        ]);

        $customer->update($data);

        return ApiResponse::success($customer);
    }

    public function search(Request $request)
    {
        $q = $request->get('q');

        $customers = Customer::forCurrentBranch()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->with('membershipTier')
            ->limit(10)
            ->get();

        return ApiResponse::success($customers);
    }

    public function orders(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $orders = $customer->orders()->with('items.servicePricing.service')
            ->latest()
            ->paginate($request->per_page ?? 15);

        return ApiResponse::paginate($orders);
    }

    public function points($id)
    {
        $customer = Customer::findOrFail($id);
        $transactions = $customer->loyaltyPointsTransactions()
            ->latest()
            ->paginate(15);

        return ApiResponse::success([
            'balance' => $customer->loyaltyPointsTransactions()->sum('points'),
            'transactions' => $transactions->items(),
        ]);
    }

    public function adjustPoints(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'points' => 'required|integer',
            'description' => 'nullable|string|max:255',
        ]);

        $customer->loyaltyPointsTransactions()->create([
            'points' => $data['points'],
            'description' => $data['description'] ?? 'Penyesuaian manual',
            'branch_id' => currentBranchId(),
        ]);

        return ApiResponse::success(['balance' => $customer->loyaltyPointsTransactions()->sum('points')]);
    }

    public function membershipTiers()
    {
        $tiers = \App\Models\MembershipTier::orderBy('level')->get();

        return ApiResponse::success($tiers);
    }

    public function lookup(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $customers = Customer::where(function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->q}%")
              ->orWhere('phone', 'like', "%{$request->q}%");
        })->limit(10)->get();

        return ApiResponse::success($customers->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'phone' => $c->phone ?? '',
            'tier_name' => $c->membershipTier?->name ?? 'Reguler',
            'available_points' => $c->loyaltyPointsTransactions()->sum('points'),
        ]));
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return ApiResponse::success(null, 'Customer deleted');
    }
}
