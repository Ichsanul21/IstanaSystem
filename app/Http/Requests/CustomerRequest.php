<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->getKey();

        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone,' . ($customerId ?? 'NULL'),
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'membership_tier_id' => 'nullable|exists:membership_tiers,id',
        ];
    }
}
