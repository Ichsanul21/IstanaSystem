<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        $statusMap = [
            'received' => 1, 'washed' => 3, 'dried' => 4, 'ironed' => 5,
            'packed' => 5, 'ready_for_pickup' => 7, 'picked_up' => 8, 'cancelled' => null,
        ];

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->foreignId('production_status_id')->nullable()->constrained('production_statuses');
            $table->timestamp('scan_time')->useCurrent();
        });

        $logs = DB::table('order_item_status_logs')->get();
        foreach ($logs as $log) {
            $statusId = null;
            $status = $log->to_status ?? $log->from_status;
            if ($status && isset($statusMap[$status])) {
                $statusId = $statusMap[$status];
            }
            if ($statusId === null && $status === 'cancelled') {
                continue;
            }
            DB::table('order_item_status_logs')
                ->where('id', $log->id)
                ->update([
                    'production_status_id' => $statusId,
                    'scan_time' => $log->created_at ?? now(),
                ]);
        }

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->dropColumn(['from_status', 'to_status', 'updated_at']);
        });

        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->renameColumn('changed_by', 'scanned_by');
            $table->renameColumn('notes', 'note');
        });

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->foreign('scanned_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->dropForeign(['scanned_by']);
            $table->renameColumn('scanned_by', 'changed_by');
            $table->renameColumn('note', 'notes');
        });

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->timestamps();
        });

        Schema::table('order_item_status_logs', function (Blueprint $table) {
            $table->dropForeign(['production_status_id']);
            $table->dropColumn(['production_status_id', 'scan_time']);
        });

        Schema::enableForeignKeyConstraints();
    }
};
