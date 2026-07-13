<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ArchiveActivityLogs extends Command
{
    protected $signature = 'logs:archive';

    protected $description = 'Archive activity logs older than 30 days to file';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subDays(30);

        $logs = ActivityLog::where('created_at', '<', $cutoff)->get();

        if ($logs->isEmpty()) {
            $this->info('Tidak ada log yang perlu diarsipkan.');
            return Command::SUCCESS;
        }

        $archiveDir = storage_path('logs/activity');

        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0755, true);
        }

        $dateStr = Carbon::now()->format('Y-m-d');
        $filePath = $archiveDir . '/activity-' . $dateStr . '.log';

        $content = '';

        foreach ($logs as $log) {
            $content .= sprintf(
                "[%s] %s|%s|%s|%s|%s\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user_id,
                $log->event,
                $log->loggable_type,
                $log->loggable_id,
                $log->description ?? ''
            );
        }

        file_put_contents($filePath, $content, FILE_APPEND | LOCK_EX);

        $logs->each->forceDelete();

        $this->info($logs->count() . ' log berhasil diarsipkan ke ' . $filePath);

        return Command::SUCCESS;
    }
}
