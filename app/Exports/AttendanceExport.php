<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    protected $attendances;
    protected $month;

    public function __construct($attendances, $month)
    {
        $this->attendances = $attendances;
        $this->month = $month;
    }

    public function collection()
    {
        return $this->attendances;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Day',
            'Status',
            'Check In',
            'Check Out',
            'Working Hours',
            'Remarks'
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date->format('d-m-Y'),
            $attendance->date->format('l'),
            $attendance->status,
            $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : 'N/A',
            $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : 'N/A',
            $this->calculateWorkingHours($attendance->check_in, $attendance->check_out),
            $attendance->remarks ?? 'N/A'
        ];
    }

    public function title(): string
    {
        return 'Attendance ' . $this->month;
    }

    private function calculateWorkingHours($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            return 'N/A';
        }

        $start = \Carbon\Carbon::parse($checkIn);
        $end = \Carbon\Carbon::parse($checkOut);
        $hours = $end->diffInMinutes($start) / 60;
        
        return number_format($hours, 2) . ' hrs';
    }
}
