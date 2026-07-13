<?php

namespace App\Services\Export;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GenericExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $headings;

    public function __construct($data, array $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function collection(): Collection
    {
        if ($this->data instanceof Collection) {
            return $this->data;
        }

        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        if (is_array($row)) {
            return $row;
        }

        if (is_object($row)) {
            return array_map(function ($heading) use ($row) {
                $key = strtolower(str_replace(' ', '_', $heading));

                return $row->$key ?? $row->{camel_case($heading)} ?? null;
            }, $this->headings);
        }

        return [];
    }
}
