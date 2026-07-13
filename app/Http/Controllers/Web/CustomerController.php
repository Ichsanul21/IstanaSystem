<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\LoyaltyPointsTransaction;
use App\Models\MembershipTier;
use App\Services\Customer\MembershipService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::forCurrentBranch()
            ->when(request('search'), function ($q, $v) {
                $q->where(function ($sq) use ($v) {
                    $sq->where('name', 'like', "%{$v}%")
                        ->orWhere('phone', 'like', "%{$v}%")
                        ->orWhere('code', 'like', "%{$v}%");
                });
            })
            ->with('membershipTier')
            ->withCount('orders')
            ->withSum('orders', 'grand_total')
            ->latest()
            ->paginate(15);

        $membershipTiers = MembershipTier::where('is_active', true)->pluck('name', 'id');

        return view('customers.index', compact('customers', 'membershipTiers'));
    }

    public function create()
    {
        $tiers = MembershipTier::where('is_active', true)->get();

        return view('customers.create', compact('tiers'));
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create([
            'code' => 'CUS-' . now()->format('YmdHis'),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'membership_tier_id' => $request->membership_tier_id,
            'branch_id' => currentBranchId(),
            'total_points' => 0,
            'lifetime_spending' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function show(Customer $customer)
    {
        $customer->load(['membershipTier', 'loyaltyPointsTransactions']);
        $recentOrders = $customer->orders()->latest()->take(5)->get();
        $recentPoints = $customer->loyaltyPointsTransactions()->latest()->take(10)->get();
        $ordersCount = $customer->orders()->count();

        return view('customers.show', compact('customer', 'recentOrders', 'recentPoints', 'ordersCount'));
    }

    public function edit(Customer $customer)
    {
        $tiers = MembershipTier::where('is_active', true)->get();

        return view('customers.edit', compact('customer', 'tiers'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Pelanggan berhasil diperbarui');
    }

    public function destroy(Customer $customer)
    {
        $customer->update(['is_active' => false]);
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus');
    }

    public function addPoints(Customer $customer, Request $request)
    {
        $request->validate([
            'points' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        LoyaltyPointsTransaction::create([
            'customer_id' => $customer->id,
            'points' => $request->points,
            'type' => $request->points > 0 ? 'earn' : 'redeem',
            'reference' => $request->reason,
        ]);

        $customer->increment('total_points', $request->points);

        app(MembershipService::class)->checkUpgrade($customer);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Poin berhasil ditambahkan.');
    }

    public function addNote(Customer $customer, Request $request)
    {
        $request->validate([
            'notes' => 'required|string|max:2000',
        ]);

        $customer->update(['notes' => $request->notes]);

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Catatan berhasil disimpan.');
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $customers = Customer::forCurrentBranch()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
        ]);

        $customer = Customer::create([
            'code' => 'CUS-' . now()->format('YmdHis'),
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'branch_id' => currentBranchId(),
            'total_points' => 0,
            'lifetime_spending' => 0,
            'is_active' => true,
        ]);

        return response()->json($customer->only(['id', 'name', 'phone']), 201);
    }

    public function getByPhone(Request $request)
    {
        $customer = Customer::forCurrentBranch()
            ->where('phone', $request->phone)
            ->with('membershipTier')
            ->first();

        return response()->json($customer);
    }
}
