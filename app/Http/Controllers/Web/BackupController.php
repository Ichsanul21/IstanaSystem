<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $files = collect(Storage::disk('local')->files('backups'))
            ->filter(fn ($f) => str_ends_with($f, '.zip'))
            ->map(fn ($f) => [
                'filename' => basename($f),
                'path' => $f,
                'size' => $this->formatSize(Storage::size($f)),
                'created_at' => date('Y-m-d H:i:s', Storage::lastModified($f)),
            ])
            ->sortByDesc('created_at')
            ->values();

        $lastBackup = $files->first();

        return view('settings.backup', compact('files', 'lastBackup'));
    }

    public function create()
    {
        Artisan::call('backup:run');

        return redirect()->route('admin.backup.index')->with('success', 'Backup completed successfully.');
    }

    public function download(string $filename)
    {
        $path = 'backups/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('local')->download($path);
    }

    public function destroy(string $filename)
    {
        $path = 'backups/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        return back()->with('success', 'Backup deleted.');
    }

    private function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
