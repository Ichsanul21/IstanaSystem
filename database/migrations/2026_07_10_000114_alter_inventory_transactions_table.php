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

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_batch_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['order_item_id']);
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->bigInteger('inventory_item_id')->unsigned()->nullable()->after('id');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->after('inventory_item_id');
            $table->integer('before_stock')->default(0)->after('unit_cost');
            $table->integer('after_stock')->default(0)->after('before_stock');
            $table->string('note', 255)->nullable()->after('reference');
            $table->string('reference_type', 50)->nullable()->after('note');
            $table->bigInteger('reference_id')->unsigned()->nullable()->after('reference_type');
        });

        DB::statement('UPDATE inventory_transactions SET inventory_item_id = (SELECT inventory_item_id FROM inventory_batches WHERE inventory_batches.id = inventory_transactions.inventory_batch_id)');

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['order_item_id']);
            $table->renameColumn('user_id', 'created_by');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items');
            $table->foreign('inventory_batch_id')->references('id')->on('inventory_batches')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });

        DB::statement("UPDATE inventory_transactions SET type = 'purchase' WHERE type = 'in'");
        DB::statement("UPDATE inventory_transactions SET type = 'usage' WHERE type = 'out'");
        DB::statement("UPDATE inventory_transactions SET type = 'transfer_out' WHERE type = 'transfer'");

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropForeign(['inventory_batch_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['branch_id']);
        });

        DB::statement("UPDATE inventory_transactions SET type = 'in' WHERE type = 'purchase'");
        DB::statement("UPDATE inventory_transactions SET type = 'out' WHERE type = 'usage'");
        DB::statement("UPDATE inventory_transactions SET type = 'transfer' WHERE type = 'transfer_out'");

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->renameColumn('created_by', 'user_id');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->after('inventory_batch_id');
            $table->dropColumn(['inventory_item_id', 'branch_id', 'before_stock', 'after_stock', 'note', 'reference_type', 'reference_id']);
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->foreign('inventory_batch_id')->references('id')->on('inventory_batches')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }
};
