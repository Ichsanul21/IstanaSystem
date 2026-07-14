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

        // 2.1 DECIMAL Precision Fix (12,2) -> (15,2)
        $this->fixDecimalPrecision();

        // 2.2 Phantom Columns — from docs but missing in migrations
        $this->addPhantomColumns();

        // 2.3 Inventory Type Fix — INT -> DECIMAL where needed
        $this->fixInventoryTypes();

        // 2.4 ENUM Comments — add ->comment('allowed: ...') to status columns
        $this->addEnumComments();

        Schema::enableForeignKeyConstraints();
    }

    private function fixDecimalPrecision(): void
    {
        // order_items.price_per_unit: 12,2 -> 15,2
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price_per_unit', 15, 2)->change();
        });

        // order_items.subtotal: 12,2 -> 15,2
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
        });

        // service_pricings.price: 12,2 -> 15,2
        Schema::table('service_pricings', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
        });

        // promotions.value: 12,2 -> 15,2
        Schema::table('promotions', function (Blueprint $table) {
            $table->decimal('value', 15, 2)->change();
        });

        // promotions.min_order_amount: 12,2 -> 15,2
        Schema::table('promotions', function (Blueprint $table) {
            $table->decimal('min_order_amount', 15, 2)->default(0)->change();
        });

        // promotions.max_discount_amount: 12,2 -> 15,2
        Schema::table('promotions', function (Blueprint $table) {
            $table->decimal('max_discount_amount', 15, 2)->nullable()->change();
        });

        // promotions.get_value: integer -> decimal(15,2) nullable
        Schema::table('promotions', function (Blueprint $table) {
            $table->decimal('get_value', 15, 2)->nullable()->change();
        });

        // promotion_usages.discount_amount: 12,2 -> 15,2
        Schema::table('promotion_usages', function (Blueprint $table) {
            $table->decimal('discount_amount', 15, 2)->change();
        });

        // inventory_batches.unit_cost: 12,2 -> 15,2
        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->change();
        });

        // inventory_transactions.unit_cost: 12,2 -> 15,2
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->decimal('unit_cost', 15, 2)->nullable()->change();
        });
    }

    private function addPhantomColumns(): void
    {
        // gateway_payments — add Midtrans fields from docs
        Schema::table('gateway_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('gateway_payments', 'va_number')) {
                $table->string('va_number', 50)->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('gateway_payments', 'bill_key')) {
                $table->string('bill_key', 50)->nullable()->after('va_number');
            }
            if (!Schema::hasColumn('gateway_payments', 'biller_code')) {
                $table->string('biller_code', 50)->nullable()->after('bill_key');
            }
            if (!Schema::hasColumn('gateway_payments', 'qr_url')) {
                $table->text('qr_url')->nullable()->after('biller_code');
            }
            if (!Schema::hasColumn('gateway_payments', 'expiry_time')) {
                $table->dateTime('expiry_time')->nullable()->after('qr_url');
            }
        });

        // accounting_periods — add is_active
        Schema::table('accounting_periods', function (Blueprint $table) {
            if (!Schema::hasColumn('accounting_periods', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('is_closed');
            }
        });

        // branch_settings — add type
        Schema::table('branch_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_settings', 'type')) {
                $table->string('type', 20)->nullable()->after('value');
            }
        });

        // tax_configurations — add effective_date (from docs)
        Schema::table('tax_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('tax_configurations', 'effective_date')) {
                $table->date('effective_date')->nullable()->after('rate');
            }
        });

        // promotion_branches — add is_active
        Schema::table('promotion_branches', function (Blueprint $table) {
            if (!Schema::hasColumn('promotion_branches', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('branch_id');
            }
        });
    }

    private function fixInventoryTypes(): void
    {
        // inventory_transactions.before_stock: integer -> decimal(12,2)
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->decimal('before_stock', 12, 2)->default(0)->change();
        });

        // inventory_transactions.after_stock: integer -> decimal(12,2)
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->decimal('after_stock', 12, 2)->default(0)->change();
        });
    }

    private function addEnumComments(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 30)->comment('draft, pending, processing, completed, cancelled')->change();
            $table->string('payment_status', 30)->comment('unpaid, paid, refunded, partial_refund')->change();
            $table->string('payment_method', 20)->nullable()->comment('cash, transfer, qris, gateway')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('method', 30)->comment('cash, transfer, qris, gateway')->change();
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->string('status', 20)->comment('requested, approved, completed, rejected')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('unit', 10)->comment('kg, pcs, m2')->change();
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->string('type', 20)->comment('percentage, fixed, buy_x_get_y')->change();
            $table->string('get_type', 30)->nullable()->comment('free, discount_percent, discount_fixed')->change();
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->comment('packaging, chemical, stationery, other')->change();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->string('type', 20)->comment('purchase, usage, adjustment_plus, adjustment_minus, transfer_out, transfer_in')->change();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('type', 20)->nullable()->comment('string, number, boolean, json')->change();
        });

        Schema::table('branch_settings', function (Blueprint $table) {
            $table->string('type', 20)->nullable()->comment('string, number, boolean, json')->change();
        });

        Schema::table('tax_configurations', function (Blueprint $table) {
            $table->string('regime', 10)->comment('none, pp23, pkp')->change();
        });

        Schema::table('gateway_payments', function (Blueprint $table) {
            $table->string('status', 30)->comment('pending, success, failed, expired, refund')->change();
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->string('category', 30)->comment('asset, liability, equity, revenue, expense')->change();
            $table->string('normal_balance', 10)->nullable()->comment('debit, credit')->change();
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('type', 20)->nullable()->comment('auto, manual, adjustment')->change();
        });

        Schema::table('loyalty_points_transactions', function (Blueprint $table) {
            $table->string('type', 20)->comment('earn, redeem, expire, adjust')->change();
        });
    }

    public function down(): void
    {
        // Reverse changes is impractical for schema alignment migrations.
        // Use `migrate:fresh` to reset instead.
    }
};
