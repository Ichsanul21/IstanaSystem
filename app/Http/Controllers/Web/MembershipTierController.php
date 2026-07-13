<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\Request;

class MembershipTierController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::latest()->paginate(15);

        return view('membership-tiers.index', compact('tiers'));
    }

    public function edit(MembershipTier $membershipTier)
    {
        return view('membership-tiers.edit', compact('membershipTier'));
    }

    public function update(Request $request, MembershipTier $membershipTier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'min_points' => 'required|integer|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $membershipTier->update($data);

        return redirect()->route('admin.membership-tiers.index')
            ->with('success', 'Tier member berhasil diperbarui.');
    }
}
