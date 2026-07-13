<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;

class AuditController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->forCurrentBranch()
            ->when(request('user_id'), fn($q, $v) => $q->where('user_id', $v))
            ->when(request('event'), fn($q, $v) => $q->where('event', $v))
            ->when(request('date_from'), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(15);

        $users = User::where('branch_id', currentBranchId())->pluck('name', 'id');

        return view('audit.index', compact('logs', 'users'));
    }
}
