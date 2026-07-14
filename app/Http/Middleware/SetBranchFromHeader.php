<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBranchFromHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $branchId = $request->header('X-Branch-Id');

        if ($branchId && Branch::where('id', $branchId)->where('is_active', true)->exists()) {
            session(['current_branch_id' => (int) $branchId]);
        }

        return $next($request);
    }
}
