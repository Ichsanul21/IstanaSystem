<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.service_pricing_id' => 'required|exists:service_pricings,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'payment_method' => 'required_with:paid_amount|string|in:cash,transfer,qris,gateway',
            'paid_amount' => 'nullable|numeric|min:0',
            'promotion_code' => 'nullable|string|exists:promotions,code',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
