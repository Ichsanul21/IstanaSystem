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

        $existingRows = DB::table('production_statuses')->get();

        $maxId = DB::table('order_item_status_logs')->max('id') ?? 0;

        foreach ($existingRows as $row) {
            $maxId++;
            DB::table('order_item_status_logs')->insert([
                'id' => $maxId,
                'order_item_id' => $row->order_item_id,
                'from_status' => $row->from_status,
                'to_status' => $row->to_status,
                'changed_by' => $row->user_id,
                'notes' => $row->notes,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        Schema::dropIfExists('production_statuses');

        Schema::create('production_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 50);
            $table->integer('sequence');
            $table->string('color', 7)->default('#FF6B00');
            $table->text('description')->nullable();
        });

        DB::table('production_statuses')->insert([
            ['code' => 'TERIMA', 'name' => 'Terima', 'sequence' => 1, 'color' => '#FF6B00', 'description' => 'Order diterima dari customer'],
            ['code' => 'PILAH', 'name' => 'Pilah', 'sequence' => 2, 'color' => '#FF8C38', 'description' => 'Pakaian dipilah berdasarkan jenis dan warna'],
            ['code' => 'CUCI', 'name' => 'Cuci', 'sequence' => 3, 'color' => '#FFA863', 'description' => 'Proses pencucian'],
            ['code' => 'KERING', 'name' => 'Kering', 'sequence' => 4, 'color' => '#FFC592', 'description' => 'Proses pengeringan'],
            ['code' => 'LIPAT', 'name' => 'Lipat', 'sequence' => 5, 'color' => '#FFC107', 'description' => 'Pakaian dilipat dan di-packing'],
            ['code' => 'CEK', 'name' => 'Cek', 'sequence' => 6, 'color' => '#4CAF50', 'description' => 'Pengecekan kualitas akhir'],
            ['code' => 'SIAP', 'name' => 'Siap', 'sequence' => 7, 'color' => '#2196F3', 'description' => 'Siap diambil customer'],
            ['code' => 'DIAMBIL', 'name' => 'Diambil', 'sequence' => 8, 'color' => '#9E9E9E', 'description' => 'Sudah diambil customer'],
        ]);

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('production_statuses');

        Schema::create('production_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->foreignId('user_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        $logs = DB::table('order_item_status_logs')->whereNotNull('to_status')->get();
        foreach ($logs as $log) {
            DB::table('production_statuses')->insert([
                'order_item_id' => $log->order_item_id,
                'from_status' => $log->from_status,
                'to_status' => $log->to_status,
                'user_id' => $log->changed_by ?? 1,
                'notes' => $log->notes,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
};
