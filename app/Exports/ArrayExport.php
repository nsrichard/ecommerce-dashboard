<?php
namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayExport implements FromArray, WithHeadings, Responsable
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    public function __construct(
        private array $headings,
        private array $rows,
        private string $fileName = 'export.xlsx'
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
