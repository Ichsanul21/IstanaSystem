<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->branch_id) {
                session()->put('current_branch_id', $user->branch_id);
            } elseif ($user->hasRole('Developer') || $user->hasRole('Super Admin')) {
                $branchId = session('current_branch_id', $request->input('branch_id'));
                session()->put('current_branch_id', $branchId);
            }
        }

        return $next($request);
    }
}
