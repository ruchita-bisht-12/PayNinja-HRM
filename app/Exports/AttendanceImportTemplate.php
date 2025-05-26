<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AttendanceImportTemplate implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            // This is an empty template with just the headers
        ];
    }

    public function headings(): array
    {
        return [
            'employee_id',
            'date',
            'check_in',
            'check_out',
            'status',
            'remarks'
        ];
    }

    public function title(): string
    {
        return 'Import Template';
    }
}
