<?php

namespace App\Imports;

use App\Models\AcademicHoliday;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class AcademicHolidayImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $company_id;
    protected $rowCount = 0;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function model(array $row)
    {
        // Validate date formats
        $fromDate = $this->transformDate($row['from_date']);
        $toDate = $this->transformDate($row['to_date']);

        // Check for overlapping dates
        $this->checkOverlappingDates($fromDate, $toDate);

        $this->rowCount++;

        return new AcademicHoliday([
            'company_id' => $this->company_id,
            'name' => $row['name'],
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'description' => $row['description'] ?? null,
            'created_by' => Auth::id()
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'from_date' => 'required',
            'to_date' => 'required',
            'description' => 'nullable|string',
            'is_recurring' => 'nullable|boolean'
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    protected function transformDate($value)
    {
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }

        try {
            return \Carbon\Carbon::parse($value)->startOfDay();
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: {$value}. Please use YYYY-MM-DD format.");
        }
    }

    protected function checkOverlappingDates($fromDate, $toDate)
    {
        $overlapping = AcademicHoliday::where('company_id', $this->company_id)
            ->where(function($query) use ($fromDate, $toDate) {
                $query->whereBetween('from_date', [$fromDate, $toDate])
                    ->orWhereBetween('to_date', [$fromDate, $toDate])
                    ->orWhere(function($q) use ($fromDate, $toDate) {
                        $q->where('from_date', '<=', $fromDate)
                            ->where('to_date', '>=', $toDate);
                    });
            })
            ->first();

        if ($overlapping) {
            throw new \Exception("Date range overlaps with existing holiday: {$overlapping->name}");
        }
    }
}
