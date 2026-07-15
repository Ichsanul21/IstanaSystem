<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_inventory_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->timestamps();
            $table->unique(['service_id', 'inventory_item_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('inventory_consumed_at')->nullable()->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_inventory_item');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('inventory_consumed_at');
        });
    }
};
