<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->session(['current_branch_id' => $this->branch->id]);
    }

    public function test_reports_revenue_requires_report_read(): void
    {
        $this->user->givePermissionTo('report.read');
        $response = $this->actingAs($this->user)->get(route('admin.reports.revenue'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('report.read');
        $this->actingAs($this->user)->get(route('admin.reports.revenue'))->assertForbidden();
    }

    public function test_reports_orders_requires_view_financial_reports(): void
    {
        $this->user->givePermissionTo('view_financial_reports');
        $response = $this->actingAs($this->user)->get(route('admin.reports.orders'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_financial_reports');
        $this->actingAs($this->user)->get(route('admin.reports.orders'))->assertForbidden();
    }

    public function test_reports_customers_requires_report_read(): void
    {
        $this->user->givePermissionTo('report.read');
        $response = $this->actingAs($this->user)->get(route('admin.reports.customers'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('report.read');
        $this->actingAs($this->user)->get(route('admin.reports.customers'))->assertForbidden();
    }

    public function test_reports_inventory_requires_view_financial_reports(): void
    {
        $this->user->givePermissionTo('view_financial_reports');
        $response = $this->actingAs($this->user)->get(route('admin.reports.inventory'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_financial_reports');
        $this->actingAs($this->user)->get(route('admin.reports.inventory'))->assertForbidden();
    }

    public function test_reports_tax_requires_report_read(): void
    {
        $this->user->givePermissionTo('report.read');
        $response = $this->actingAs($this->user)->get(route('admin.reports.tax'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('report.read');
        $this->actingAs($this->user)->get(route('admin.reports.tax'))->assertForbidden();
    }

    public function test_reports_production_requires_view_financial_reports(): void
    {
        $this->user->givePermissionTo('view_financial_reports');
        $response = $this->actingAs($this->user)->get(route('admin.reports.production'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_financial_reports');
        $this->actingAs($this->user)->get(route('admin.reports.production'))->assertForbidden();
    }

    public function test_reports_forbidden_when_no_permission(): void
    {
        $this->actingAs($this->user)->get(route('admin.reports.revenue'))->assertForbidden();
        $this->actingAs($this->user)->get(route('admin.reports.orders'))->assertForbidden();
    }
}
