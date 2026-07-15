<?php

namespace Tests\Unit\Services;

use App\Services\Notification\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    use RefreshDatabase;

    private WhatsAppService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WhatsAppService::class);

        config(['wa-templates' => [
            'order_received' => 'Halo {name}, pesanan {order} sudah diterima.',
            'order_ready' => 'Halo {name}, pesanan {order} sudah siap diambil.',
        ]]);
    }

    public function test_generate_link_returns_valid_wa_link_format(): void
    {
        $link = $this->service->generateLink('081234567890', 'order_received', [
            'name' => 'Budi',
            'order' => 'ORD-001',
        ]);

        $this->assertStringStartsWith('https://wa.me/62', $link);
        $this->assertStringContainsString('?text=', $link);
    }

    public function test_generate_link_uses_template_and_fills_data(): void
    {
        $link = $this->service->generateLink('081234567890', 'order_received', [
            'name' => 'Budi',
            'order' => 'ORD-001',
        ]);

        $this->assertStringContainsString('Halo+Budi', $link);
        $this->assertStringContainsString('ORD-001', $link);
    }

    public function test_send_returns_generated_link(): void
    {
        $link = $this->service->send('081234567890', 'order_ready', [
            'name' => 'Andi',
            'order' => 'ORD-002',
        ]);

        $this->assertStringStartsWith('https://wa.me/62', $link);
        $this->assertStringContainsString('?text=', $link);
    }

    public function test_fill_template_replaces_all_placeholders(): void
    {
        $template = 'Halo {name}, pesanan {order} dengan harga {price} sudah selesai.';
        $result = $this->service->fillTemplate($template, [
            'name' => 'Sari',
            'order' => 'ORD-003',
            'price' => 'Rp50.000',
        ]);

        $this->assertEquals('Halo Sari, pesanan ORD-003 dengan harga Rp50.000 sudah selesai.', $result);
    }

    public function test_get_template_throws_for_unknown_key(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("WA template 'nonexistent' not found.");

        $this->service->getTemplate('nonexistent');
    }

    public function test_generate_link_strips_plus_prefix(): void
    {
        $link = $this->service->generateLink('+6281234567890', 'order_received', [
            'name' => 'Dewi',
            'order' => 'ORD-004',
        ]);

        $this->assertStringStartsWith('https://wa.me/6281234567890', $link);
    }

    public function test_generate_link_strips_leading_zero(): void
    {
        $link = $this->service->generateLink('081234567890', 'order_received', [
            'name' => 'Rina',
            'order' => 'ORD-005',
        ]);

        $this->assertStringStartsWith('https://wa.me/6281234567890', $link);
    }
}
