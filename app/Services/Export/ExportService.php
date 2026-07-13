<?php

namespace App\Services\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    public function excel($data, string $filename, array $headers)
    {
        $export = new GenericExport($data, $headers);

        return Excel::download($export, $filename);
    }

    public function pdf(string $view, array $data, string $filename)
    {
        $pdf = Pdf::loadView($view, $data);

        return $pdf->download($filename);
    }
}
