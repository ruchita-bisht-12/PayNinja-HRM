<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AcademicHolidayTemplate implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return new Collection([
            [
                'name' => 'Summer Break',
                'from_date' => '2025-06-01',
                'to_date' => '2025-06-30',
                'description' => 'Summer vacation period'
            ],
            [
                'name' => 'Winter Break',
                'from_date' => '2025-12-20',
                'to_date' => '2026-01-05',
                'description' => 'Winter vacation period'
            ],
            [
                'name' => 'Foundation Day',
                'from_date' => '2025-08-15',
                'to_date' => '2025-08-15',
                'description' => 'School Foundation Day'
            ]
        ]);
    }
    public function headings(): array
    {
        return [
            'name',
            'from_date',
            'to_date',
            'description'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30);  // Name
        $sheet->getColumnDimension('B')->setWidth(15);  // From Date
        $sheet->getColumnDimension('C')->setWidth(15);  // To Date
        $sheet->getColumnDimension('D')->setWidth(40);  // Description

        return [
            1 => ['font' => ['bold' => true]],
            'A1:E1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ]
        ];
    }
}
