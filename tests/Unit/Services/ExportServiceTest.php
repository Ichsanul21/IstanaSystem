<?php

namespace Tests\Unit\Services;

use App\Services\Export\ExportService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ExportService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_excel_returns_download_response(): void
    {
        $data = collect([
            ['name' => 'Budi', 'phone' => '081234567890', 'total' => 50000],
            ['name' => 'Andi', 'phone' => '081234567891', 'total' => 75000],
        ]);
        $headers = ['Name', 'Phone', 'Total'];

        Excel::fake();

        $response = $this->service->excel($data, 'customers.xlsx', $headers);

        $this->assertNotNull($response);
    }

    public function test_pdf_returns_download_response(): void
    {
        $pdfMock = Mockery::mock(PDF::class);
        $pdfMock->shouldReceive('loadView')
            ->once()
            ->with('pdf.report', Mockery::type('array'))
            ->andReturnSelf();
        $pdfMock->shouldReceive('download')
            ->once()
            ->with('report.pdf')
            ->andReturn(new Response('fake pdf', 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="report.pdf"',
            ]));

        $this->app->bind('dompdf.wrapper', fn () => $pdfMock);

        $response = $this->service->pdf('pdf.report', [
            'title' => 'Laporan',
            'items' => [],
            'total' => 0,
        ], 'report.pdf');

        $this->assertNotNull($response);
    }

    public function test_excel_does_not_throw_with_empty_data(): void
    {
        Excel::fake();

        $response = $this->service->excel(collect(), 'empty.xlsx', ['Column']);

        $this->assertNotNull($response);
    }

    public function test_pdf_does_not_throw_with_empty_data(): void
    {
        $pdfMock = Mockery::mock(PDF::class);
        $pdfMock->shouldReceive('loadView')
            ->once()
            ->with('pdf.report', [])
            ->andReturnSelf();
        $pdfMock->shouldReceive('download')
            ->once()
            ->with('empty.pdf')
            ->andReturn(new Response('fake pdf', 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="empty.pdf"',
            ]));

        $this->app->bind('dompdf.wrapper', fn () => $pdfMock);

        $response = $this->service->pdf('pdf.report', [], 'empty.pdf');

        $this->assertNotNull($response);
    }
}
