<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeSalary;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Show the employee's salary details with monthly breakdown.
     *
     * @return \Illuminate\View\View
     */
    public function details()
    {
        $employee = Auth::user()->employee;
        $currentSalary = $employee->currentSalary;
        
        // Get all salary records with start_date and end_date
        $salaryHistory = $employee->salaries()
            ->select([
                'id',
                'start_date',
                'end_date',
                'basic_salary',
                'hra',
                'da',
                'other_allowances',
                'gross_salary',
                'created_at',
                'updated_at'
            ])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($salary) {
                return (object)[
                    'id' => $salary->id,
                    'start_date' => $salary->start_date,
                    'end_date' => $salary->end_date,
                    'basic_salary' => $salary->basic_salary,
                    'hra' => $salary->hra,
                    'da' => $salary->da,
                    'other_allowances' => $salary->other_allowances,
                    'gross_salary' => $salary->gross_salary,
                    'created_at' => $salary->created_at,
                    'updated_at' => $salary->updated_at
                ];
            });

        // Generate monthly salary data for the last 12 months
        $monthlySalaries = collect();
        $currentDate = now();
        
        // Group salary history by month
        $monthlySalaries = $salaryHistory->map(function($salary) use ($employee) {
            $startDate = Carbon::parse($salary->start_date);
            $endDate = $salary->end_date ? Carbon::parse($salary->end_date) : now();
            
            $months = [];
            $currentMonth = $startDate->copy()->startOfMonth();
            
            while ($currentMonth <= $endDate) {
                $monthStart = $currentMonth->copy()->startOfMonth();
                $monthEnd = $currentMonth->copy()->endOfMonth();
                
                // Adjust start/end dates if they fall within the salary period
                $periodStart = $startDate->greaterThan($monthStart) ? $startDate : $monthStart;
                $periodEnd = $endDate->lessThan($monthEnd) ? $endDate : $monthEnd;
                
                // Skip if the period is invalid
                if ($periodStart <= $periodEnd) {
                    $months[] = (object)[
                        'month' => $currentMonth->format('M Y'),
                        'date' => $currentMonth->copy(),
                        'salary' => $salary,
                        'working_days' => $periodEnd->diffInDays($periodStart) + 1,
                        'present_days' => $this->calculatePresentDays($employee->id, $periodStart, $periodEnd),
                        'leaves' => $this->calculateLeaves($employee->id, $periodStart, $periodEnd),
                        'start_date' => $periodStart->format('Y-m-d'),
                        'end_date' => $periodEnd->format('Y-m-d')
                    ];
                }
                
                $currentMonth->addMonth();
            }
            
            return $months;
        })->flatten()->sortByDesc('date')->take(12);

        return view('employee.salary.details', [
            'employee' => $employee,
            'currentSalary' => $currentSalary,
            'salaryHistory' => $salaryHistory,
            'monthlySalaries' => $monthlySalaries
        ]);
    }

    /**
     * Show detailed view of a specific month's salary
     * 
     * @param string $year
     * @param string $month
     * @return \Illuminate\View\View
     */
    public function monthlyDetails(Request $request, $year, $month)
    {
        $employee = Auth::user()->employee;
        $date = Carbon::createFromDate($year, $month, 1);
        
        // Get date range from request or use month boundaries
        $startDate = $request->has('start_date') 
            ? Carbon::parse($request->start_date)
            : $date->copy()->startOfMonth();
            
        $endDate = $request->has('end_date')
            ? Carbon::parse($request->end_date)
            : $date->copy()->endOfMonth();

        // Find the applicable salary for this period
        $salary = $employee->salaries()
            ->where('effective_from', '<=', $endDate)
            ->where(function($query) use ($startDate) {
                $query->where('effective_to', '>=', $startDate)
                      ->orWhereNull('effective_to');
            })
            ->orderBy('effective_from', 'desc')
            ->firstOrFail();

        $workingDays = $startDate->diffInDays($endDate) + 1; // +1 to include both start and end dates
        $presentDays = $this->calculatePresentDays($employee->id, $startDate, $endDate);
        $leaves = $this->calculateLeaves($employee->id, $startDate, $endDate);
        $absentDays = $workingDays - $presentDays - $leaves;

        // Calculate salary breakdown
        $perDayBasic = $salary->basic_salary / $workingDays;
        $perDayHra = $salary->hra / $workingDays;
        $perDayDa = $salary->da / $workingDays;
        $perDayAllowances = $salary->other_allowances / $workingDays;

        $deductions = $salary->deductions ?? 0;
        $totalEarnings = ($perDayBasic + $perDayHra + $perDayDa + $perDayAllowances) * $presentDays;
        $netSalary = $totalEarnings - $deductions;

        return view('employee.salary.monthly_details', [
            'employee' => $employee,
            'salary' => $salary,
            'month' => $date->format('F Y'),
            'monthDate' => $date,
            'workingDays' => $workingDays,
            'presentDays' => $presentDays,
            'leaveDays' => $leaves,
            'absentDays' => $absentDays,
            'perDayBasic' => $perDayBasic,
            'perDayHra' => $perDayHra,
            'perDayDa' => $perDayDa,
            'perDayAllowances' => $perDayAllowances,
            'totalEarnings' => $totalEarnings,
            'deductions' => $deductions,
            'netSalary' => $netSalary
        ]);
    }

    /**
     * Calculate present days for the employee in the given date range
     */
    private function calculatePresentDays($employeeId, $startDate, $endDate)
    {
        // Implement your attendance logic here
        // This is a placeholder - replace with your actual attendance calculation
        return $endDate->diffInDays($startDate) + 1; // Default to all days present
    }

    /**
     * Calculate leave days for the employee in the given date range
     */
    private function calculateLeaves($employeeId, $startDate, $endDate)
    {
        // Implement your leave calculation logic here
        // This is a placeholder - replace with your actual leave calculation
        return 0; // Default to no leaves
    }
}
