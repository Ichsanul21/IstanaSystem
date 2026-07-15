<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatusLog;
use App\Models\ProductionStatus;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopApiTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->assignRole('Developer');
        session(['current_branch_id' => $this->branch->id]);
    }

    private function createOrderItemWithStatus(string $statusCode): OrderItem
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'created_by' => $this->user->id,
        ]);
        $pricing = ServicePricing::factory()->create(['branch_id' => $this->branch->id]);
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $pricing->service_id,
            'quantity' => 2,
            'price_per_unit' => $pricing->price,
            'subtotal' => 2 * $pricing->price,
        ]);
        $status = ProductionStatus::firstOrCreate(
            ['code' => $statusCode],
            ['name' => $statusCode, 'sequence' => array_search($statusCode, ['TERIMA', 'PILAH', 'CUCI', 'KERING', 'LIPAT', 'CEK', 'SIAP', 'DIAMBIL']) + 1, 'color' => '#808080']
        );
        OrderItemStatusLog::create([
            'order_item_id' => $orderItem->id,
            'production_status_id' => $status->id,
        ]);

        return $orderItem;
    }

    public function test_queue_returns_workshop_queue(): void
    {
        $this->createOrderItemWithStatus('CUCI');

        $this->actingAs($this->user)
            ->getJson('/api/v1/workshop/queue')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_queue_filters_by_status(): void
    {
        $this->createOrderItemWithStatus('CUCI');

        $this->actingAs($this->user)
            ->getJson('/api/v1/workshop/queue?status=CUCI')
            ->assertOk()
            ->assertJsonStructure(['success', 'data']);
    }

    public function test_stats_returns_workshop_statistics(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/workshop/stats')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_in_production',
                    'completed_today',
                    'average_time',
                    'by_status',
                ],
            ]);
    }

    public function test_stats_includes_all_status_codes(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/workshop/stats')
            ->assertOk();

        $statusCodes = ['TERIMA', 'PILAH', 'CUCI', 'KERING', 'LIPAT', 'CEK', 'SIAP'];
        foreach ($statusCodes as $code) {
            $response->assertJsonPath("data.by_status.{$code}", 0);
        }
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/workshop/queue')
            ->assertUnauthorized();
    }
}
