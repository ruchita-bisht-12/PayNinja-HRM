<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    /**
     * Display a list of available payslips by year and month
     *
     * @return \Illuminate\View\View
     */
    public function listPayslips()
    {
        $user = Auth::user();
        
        if (!$user->employee) {
            abort(403, 'Employee record not found.');
        }
        
        $employee = $user->employee;
        $currentSalary = $employee->currentSalary;
        
        if (!$currentSalary) {
            return redirect()->route('employee.salary.details')
                ->with('error', 'No active salary record found.');
        }
        
        // Get all salary records for the employee
        $salaryRecords = $employee->salaries()
            ->orderBy('effective_from', 'asc')
            ->get(['effective_from', 'effective_to']);
            
        if ($salaryRecords->isEmpty()) {
            return redirect()->route('employee.salary.details')
                ->with('error', 'No salary records found.');
        }
        
        // Get the earliest start date and latest end date
        $currentYear = now()->year;
        $startYear = $employee->joining_date 
            ? \Carbon\Carbon::parse($employee->joining_date)->year 
            : $currentYear - 2;
            
        $endYear = $currentYear;
        
        // Generate a list of valid year-month combinations
        $validMonths = collect();
        
        foreach ($salaryRecords as $record) {
            $start = \Carbon\Carbon::parse($record->effective_from);
            $end = $record->effective_to ? \Carbon\Carbon::parse($record->effective_to) : now();
            
            $current = $start->copy()->startOfMonth();
            
            while ($current->lte($end)) {
                $validMonths->push($current->format('Y-m'));
                $current->addMonth();
            }
        }
        
        // Remove duplicates and sort
        $validMonths = $validMonths->unique()->sort()->values();
        
        // Group by year
        $years = [];
        foreach ($validMonths as $yearMonth) {
            [$year, $month] = explode('-', $yearMonth);
            if (!isset($years[$year])) {
                $years[$year] = [];
            }
            $years[$year][] = (int)$month;
        }
        
        // Sort years in descending order
        krsort($years);
        
        // Sort months in descending order within each year
        foreach ($years as &$months) {
            rsort($months);
        }
        
        return view('employee.salary.payslips', [
            'salary' => $currentSalary,
            'years' => $years,
            'employee' => $employee
        ]);
    }
    /**
     * Display the payslip for an employee
     *
     * @param  int  $employeeId
     * @param  string  $monthYear
     * @return \Illuminate\Http\Response
     */
    /**
     * Display the payslip for an employee
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $employee
     * @param  string|null  $monthYear
     * @return \Illuminate\Http\Response
     */
    public function showPayslip(Request $request, $employee, $monthYear = null)
    {
        $user = Auth::user();
        $monthYear = $monthYear ?? now()->format('Y-m');
        
        // For employees, they can only view their own payslips
        if ($user->hasRole('employee') && $user->employee->id != $employee) {
            abort(403, 'Unauthorized action.');
        }
        
        $salary = EmployeeSalary::with(['employee', 'employee.department', 'employee.designation'])
            ->where('employee_id', $employee)
            ->where('is_current', true)
            ->firstOrFail();
            
        $data = [
            'salary' => $salary,
            'monthYear' => $monthYear,
            'generatedDate' => now()->format('d M, Y'),
        ];
        
        $pdf = PDF::loadView('pdf.payslip', $data);
        
        return $pdf->stream('payslip-' . $salary->employee->id . '-' . $monthYear . '.pdf');
    }
    
    /**
     * Download payslip as PDF
     * 
     * @param  int  $employeeId
     * @param  string  $monthYear
     * @return \Illuminate\Http\Response
     */
    /**
     * Download the payslip as PDF
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $employee
     * @param  string|null  $monthYear
     * @return \Illuminate\Http\Response
     */
    public function downloadPayslip(Request $request, $employee, $monthYear = null)
    {
        $user = Auth::user();
        $monthYear = $monthYear ?? now()->format('Y-m');
        
        // For employees, they can only download their own payslips
        if ($user->hasRole('employee') && $user->employee->id != $employee) {
            abort(403, 'Unauthorized action.');
        }
        
        $salary = EmployeeSalary::with(['employee', 'employee.department', 'employee.designation'])
            ->where('employee_id', $employee)
            ->where('is_current', true)
            ->firstOrFail();
            
        $data = [
            'salary' => $salary,
            'monthYear' => $monthYear,
            'generatedDate' => now()->format('d M, Y'),
        ];
        
        $pdf = PDF::loadView('pdf.payslip', $data);
        
        return $pdf->download('payslip-' . $salary->employee->id . '-' . $monthYear . '.pdf');
    }
}
