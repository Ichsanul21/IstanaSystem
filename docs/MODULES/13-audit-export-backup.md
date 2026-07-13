# Module 13: Audit Trail, Export & Backup

## Overview

Activity logging (DB + File), data export (Excel/PDF), and manual backup via Spatie Backup (latest).

## Audit Trail

### Database Logging

All CRUD operations on all models are logged to `activity_logs` table:

```php
// Via trait:
class Order extends Model {
    use LogsActivity;
}

// Via observer (preferred for automatic logging):
class ActivityLogObserver
{
    public function created(Model $model): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'branch_id' => currentBranchId(),
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'event' => 'created',
            'new_values' => $model->toJson(),
            'description' => "Created {$model->getLogName()} #{$model->id}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    // + updated, deleted events
}
```

### File Archiving

```bash
# Daily cron: archive logs older than 30 days to file
php artisan logs:archive

# Writes to: storage/logs/activity/activity-2026-07-09.log
# Format: [timestamp] user|action|model|id|description|ip
```

### Log Viewer UI

```
SETTINGS → Activity Logs (Dev, Owner, SA only)
┌──────────────────────────────────────────────┐
│  Activity Logs          [Export Excel] [PDF]  │
├──────────────────────────────────────────────┤
│ [Today ▼] [User ▼] [Action ▼] [Cari...]     │
├──────┬────────┬──────────┬──────────┬────────┤
│ Tgl  │ User   │ Aksi     │ Target   │ Detail │
├──────┼────────┼──────────┼──────────┼────────┤
│09 Jul│ Admin  │ Created  │ Order #B001│ By...│
│09 Jul│ Budi   │ Updated  │ Cust #C001│ Phone │
└──────┴────────┴──────────┴──────────┴────────┘
```

## Export (Excel + PDF)

### Libraries

| Format | Library | Composer |
|--------|---------|----------|
| Excel | Laravel Excel (`maatwebsite/laravel-excel`) | `maatwebsite/excel` (latest) |
| PDF | DomPDF (`barryvdh/laravel-dompdf`) | `barryvdh/laravel-dompdf` v3.1.5 |

### Exportable Reports

| Report | Format | Description |
|--------|--------|-------------|
| Revenue Report | Excel, PDF | Daily/monthly revenue breakdown |
| Orders List | Excel, PDF | All orders with filters |
| Journal Entries | Excel, PDF | General ledger |
| Tax Summary | Excel, PDF | PP 23 or PPN summary |
| Inventory Stock | Excel | Current stock per branch |
| Customer List | Excel | All customers with points |
| Activity Logs | Excel, PDF | Audit trail export |
| Production Log | Excel, PDF | Per-item status history |

### Export Button

```blade
{{-- In every table/index page --}}
<div class="flex items-center gap-2 mb-4">
    <x-ui.button variant="outline" icon="lucide:download">
        Export Excel
    </x-ui.button>
    <x-ui.button variant="outline" icon="lucide:file-text">
        Export PDF
    </x-ui.button>
</div>
```

### Export Service Pattern

```php
// app/Services/Export/ExportService.php
class ExportService
{
    public function excel($data, string $filename, array $headers): void
    {
        return Excel::download(new GenericExport($data, $headers), "{$filename}.xlsx");
    }
    
    public function pdf($view, array $data, string $filename): void
    {
        $pdf = PDF::loadView($view, $data);
        return $pdf->download("{$filename}.pdf");
    }
}
```

## Backup (Spatie)

### Installation

```bash
composer require spatie/laravel-backup
```

### Manual Trigger

```bash
# Manual — no scheduler
php artisan backup:run

# Backups stored in: storage/app/backups/
# Includes: database dump + uploaded files
```

### Admin UI

```
SETTINGS → Backup
┌──────────────────────────────────────────────┐
│  Backup Management        [Run Backup Now]    │
│                                              │
│  Last Backup: 09 Jul 2026 14:30              │
│  Size: 45 MB                                 │
│  Location: storage/app/backups/              │
│                                              │
│  Available Backups:                          │
│  ├─ 2026-07-09-14-30-00.zip  | 45 MB | [DL] │
│  ├─ 2026-07-08-14-30-00.zip  | 44 MB | [DL] │
│  └─ 2026-07-07-14-30-00.zip  | 44 MB | [DL] │
└──────────────────────────────────────────────┘
```

### Config (`config/backup.php`)

```php
// Key settings:
'backup' => [
    'source' => [
        'databases' => ['mysql'],
        'include' => ['public/uploads'],
    ],
    'destination' => [
        'disks' => ['local'],
    ],
],
```

## Files

```
app/Models/ActivityLog.php
app/Traits/LogsActivity.php
app/Observers/ActivityLogObserver.php
app/Console/Commands/ArchiveActivityLogs.php
app/Services/Export/ExportService.php
app/Services/Export/GenericExport.php (Maatwebsite concern)
app/Http/Controllers/Web/ActivityLogController.php
app/Http/Controllers/Web/BackupController.php
app/Http/Controllers/Web/ExportController.php
app/Providers/AppServiceProvider.php (observer registration)
database/migrations/create_activity_logs_table.php
resources/views/settings/activity-logs.blade.php
resources/views/settings/backup.blade.php
resources/views/exports/revenue.blade.php (PDF template)
```
