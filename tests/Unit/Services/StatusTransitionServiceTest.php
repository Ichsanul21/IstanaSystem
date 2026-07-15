<?php

namespace Tests\Unit\Services;

use App\Enums\ProductionStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatusLog;
use App\Models\ProductionStatus as ProductionStatusModel;
use App\Models\User;
use App\Services\Workshop\StatusTransitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTransitionServiceTest extends TestCase
{
    use RefreshDatabase;

    private StatusTransitionService $service;
    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StatusTransitionService::class);
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->actingAs($this->user);

        $this->seedProductionStatuses();
    }

    private function seedProductionStatuses(): void
    {
        $statuses = [
            ['code' => 'TERIMA', 'name' => 'Terima', 'sequence' => 1],
            ['code' => 'PILAH', 'name' => 'Pilah', 'sequence' => 2],
            ['code' => 'CUCI', 'name' => 'Cuci', 'sequence' => 3],
            ['code' => 'KERING', 'name' => 'Kering', 'sequence' => 4],
            ['code' => 'LIPAT', 'name' => 'Lipat', 'sequence' => 5],
            ['code' => 'CEK', 'name' => 'Cek', 'sequence' => 6],
            ['code' => 'SIAP', 'name' => 'Siap', 'sequence' => 7],
            ['code' => 'DIAMBIL', 'name' => 'Diambil', 'sequence' => 8],
        ];

        foreach ($statuses as $status) {
            ProductionStatusModel::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }

    private function createItemWithStatus(string $statusCode): OrderItem
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $item = OrderItem::create([
            'order_id' => $order->id,
            'service_id' => 1,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $productionStatus = ProductionStatusModel::where('code', $statusCode)->first();
        OrderItemStatusLog::create([
            'order_item_id' => $item->id,
            'production_status_id' => $productionStatus->id,
            'note' => null,
            'scanned_by' => $this->user->id,
            'scan_time' => now(),
        ]);

        return $item;
    }

    public function test_validate_transition_returns_true_for_valid_transition(): void
    {
        $item = $this->createItemWithStatus('TERIMA');

        $result = $this->service->validateTransition($item, 'PILAH');

        $this->assertTrue($result);
    }

    public function test_validate_transition_returns_true_for_sequential_transitions(): void
    {
        $item = $this->createItemWithStatus('CUCI');

        $this->assertTrue($this->service->validateTransition($item, 'KERING'));
    }

    public function test_validate_transition_returns_false_for_backwards_transition(): void
    {
        $item = $this->createItemWithStatus('PILAH');

        $result = $this->service->validateTransition($item, 'TERIMA');

        $this->assertFalse($result);
    }

    public function test_validate_transition_returns_false_for_skip_transition(): void
    {
        $item = $this->createItemWithStatus('TERIMA');

        $result = $this->service->validateTransition($item, 'CUCI');

        $this->assertFalse($result);
    }

    public function test_validate_transition_returns_false_for_unknown_status(): void
    {
        $item = $this->createItemWithStatus('TERIMA');

        $result = $this->service->validateTransition($item, 'INVALID');

        $this->assertFalse($result);
    }

    public function test_transition_creates_status_log_and_returns_production_status(): void
    {
        $item = $this->createItemWithStatus('TERIMA');

        $result = $this->service->transition($item, 'PILAH', $this->user->id, 'Mulai pilah');

        $this->assertInstanceOf(ProductionStatus::class, $result);
        $this->assertSame(ProductionStatus::Pilah, $result);

        $this->assertDatabaseHas('order_item_status_logs', [
            'order_item_id' => $item->id,
            'scanned_by' => $this->user->id,
            'note' => 'Mulai pilah',
        ]);

        $log = OrderItemStatusLog::where('order_item_id', $item->id)->where('note', 'Mulai pilah')->first();
        $productionStatus = ProductionStatusModel::where('code', 'PILAH')->first();
        $this->assertSame($productionStatus->id, $log->production_status_id);
    }

    public function test_transition_throws_exception_for_invalid_transition(): void
    {
        $item = $this->createItemWithStatus('PILAH');

        $this->expectException(InvalidStatusTransitionException::class);

        $this->service->transition($item, 'TERIMA', $this->user->id);
    }

    public function test_transition_throws_exception_for_unknown_status(): void
    {
        $item = $this->createItemWithStatus('TERIMA');

        $this->expectException(InvalidStatusTransitionException::class);

        $this->service->transition($item, 'INVALID', $this->user->id);
    }

    public function test_update_order_status_sets_ready_for_pickup_when_all_items_terminal(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'processing',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'service_id' => 1,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $diambil = ProductionStatusModel::where('code', 'DIAMBIL')->first();
        OrderItemStatusLog::create([
            'order_item_id' => $item->id,
            'production_status_id' => $diambil->id,
            'note' => null,
            'scanned_by' => $this->user->id,
            'scan_time' => now(),
        ]);

        $this->service->updateOrderStatus($order);

        $order->refresh();
        $this->assertSame('ready_for_pickup', $order->status);
        $this->assertNotNull($order->finished_at);
    }

    public function test_update_order_status_sets_received_when_any_item_processing(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        $item1 = OrderItem::create([
            'order_id' => $order->id,
            'service_id' => 1,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'service_id' => 1,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $terima = ProductionStatusModel::where('code', 'TERIMA')->first();
        OrderItemStatusLog::create([
            'order_item_id' => $item1->id,
            'production_status_id' => $terima->id,
            'note' => null,
            'scanned_by' => $this->user->id,
            'scan_time' => now(),
        ]);

        $cuci = ProductionStatusModel::where('code', 'CUCI')->first();
        OrderItemStatusLog::create([
            'order_item_id' => $item2->id,
            'production_status_id' => $cuci->id,
            'note' => null,
            'scanned_by' => $this->user->id,
            'scan_time' => now(),
        ]);

        $this->service->updateOrderStatus($order);

        $order->refresh();
        $this->assertSame('received', $order->status);
    }

    public function test_update_order_status_does_not_change_when_all_null_status(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => 1,
            'quantity' => 1,
            'price_per_unit' => 10000,
            'subtotal' => 10000,
        ]);

        $this->service->updateOrderStatus($order);

        $order->refresh();
        $this->assertSame('pending', $order->status);
    }
}
