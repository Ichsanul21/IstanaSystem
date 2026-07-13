<?php

namespace App\Services\Customer;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerService
{
    public function createCustomer(array $data): Customer
    {
        $data['code'] = $data['code'] ?? 'CUS-' . now()->format('YmdHis');
        $data['branch_id'] = $data['branch_id'] ?? currentBranchId();
        $data['total_points'] = $data['total_points'] ?? 0;
        $data['lifetime_spending'] = $data['lifetime_spending'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        return Customer::create($data);
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->fresh();
    }

    public function searchCustomers(string $query, ?int $branchId = null): Collection
    {
        $q = Customer::query();

        if ($branchId) {
            $q->where('branch_id', $branchId);
        }

        $q->where(function ($sq) use ($query) {
            $sq->where('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%");
        });

        return $q->with('membershipTier')->limit(10)->get();
    }

    public function getByPhone(string $phone): ?Customer
    {
        return Customer::forCurrentBranch()
            ->where('phone', $phone)
            ->with('membershipTier')
            ->first();
    }
}
