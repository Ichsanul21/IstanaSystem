<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01|max:' . ($this->order?->grand_total - $this->order?->refunds()->where('status', 'approved')->sum('amount')),
            'reason' => 'required|string|max:1000',
        ];
    }
}
