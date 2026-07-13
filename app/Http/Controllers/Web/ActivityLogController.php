<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\Export\ExportService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected ExportService $exportService) {}

    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->forCurrentBranch()
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->event, fn($q, $v) => $q->where('event', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(15);

        $users = User::where('branch_id', currentBranchId())->pluck('name', 'id');

        if ($request->has('export')) {
            try {
                $data = $logs->items();
                $headers = ['User', 'Event', 'Deskripsi', 'IP Address', 'Waktu'];

                return $this->exportService->excel($data, 'activity-logs.xlsx', $headers);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengekspor log aktivitas.');
            }
        }

        return view('audit.index', compact('logs', 'users'));
    }
}
