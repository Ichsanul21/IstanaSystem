<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServicePricing;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        $pricings = ServicePricing::with('service')
            ->forCurrentBranch()
            ->where('is_active', true)
            ->get();

        return view('pos.index', compact('pricings'));
    }
}
