<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('inventory_item_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expired_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
