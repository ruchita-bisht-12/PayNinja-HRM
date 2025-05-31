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
     * @param  int|null  $salaryId
     * @return \Illuminate\Http\Response
     */
    public function downloadPayslip(Request $request, $employee, $monthYear = null, $salaryId = null)
    {
        $user = Auth::user();
        $monthYear = $monthYear ?? now()->format('Y-m');
        
        // For employees, they can only download their own payslips
        if ($user->hasRole('employee') && $user->employee->id != $employee) {
            abort(403, 'Unauthorized action.');
        }

        // Build the base query with all necessary relationships
        $query = EmployeeSalary::with([
            'employee.user',
            'employee.department',
            'employee.designation'
        ])->where('employee_id', $employee);

        // If salary ID is provided, use that specific salary record
        if ($salaryId) {
            $salary = $query->findOrFail($salaryId);
        } else {
            // For backward compatibility, get the current salary
            $salary = $query->where('is_current', true)->firstOrFail();
        }

        // Make sure we have the employee relationship loaded
        if (!$salary->relationLoaded('employee')) {
            $salary->load(['employee.user', 'employee.department', 'employee.designation']);
        }
        
        $data = [
            'salary' => $salary,
            'monthYear' => $monthYear,
            'generatedDate' => now()->format('d M, Y'),
        ];
        
        $pdf = PDF::loadView('pdf.payslip', $data);
        
        return $pdf->download('payslip-' . $salary->employee->id . '-' . $monthYear . '.pdf');
    }
    
    /**
     * Get all payslips for employees
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getAllPayslips(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is authorized (admin or HR)
        if (!$user->hasAnyRole(['admin', 'hr', 'company_admin'])) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get departments for filter dropdown
        $departments = \App\Models\Department::orderBy('name')->get();
        
        $query = EmployeeSalary::with([
                'employee.user',
                'employee.department',
                'employee.designation'
            ])
            ->where('is_current', true);
            
        // Apply department filter if provided
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        
        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('employee.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        // Apply date range filter if provided
        if ($request->has('date_range') && $request->date_range) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();
                
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('effective_from', [$startDate, $endDate])
                      ->orWhereBetween('effective_to', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('effective_from', '<=', $startDate)
                            ->where(function($q) use ($endDate) {
                                $q->where('effective_to', '>=', $endDate)
                                  ->orWhereNull('effective_to');
                            });
                      });
                });
            }
        }
        
        // Get paginated results
        $perPage = $request->per_page ?? 15;
        $salaries = $query->paginate($perPage)->withQueryString();
        
        // Transform the data
        $payslips = $salaries->map(function ($salary) {
            return [
                'id' => $salary->id,
                'employee_id' => $salary->employee_id,
                'employee_name' => $salary->employee->user->name,
                'employee_number' => $salary->employee->employee_id,
                'email' => $salary->employee->user->email,
                'department' => $salary->employee->department->name ?? 'N/A',
                'department_id' => $salary->employee->department_id,
                'designation' => $salary->employee->designation->name ?? 'N/A',
                'basic_salary' => $salary->basic_salary,
                'gross_salary' => $salary->gross_salary,
                'net_salary' => $salary->net_salary,
                'formatted_basic_salary' => number_format($salary->basic_salary, 2),
                'formatted_gross_salary' => number_format($salary->gross_salary, 2),
                'formatted_net_salary' => number_format($salary->net_salary, 2),
                'effective_from' => $salary->effective_from,
                'effective_to' => $salary->effective_to,
                'formatted_effective_from' => $salary->effective_from ? \Carbon\Carbon::parse($salary->effective_from)->format('d M, Y') : 'N/A',
                'formatted_effective_to' => $salary->effective_to ? \Carbon\Carbon::parse($salary->effective_to)->format('d M, Y') : 'Present',
                'payslip_url' => route('payslip.show', [$salary->employee_id, now()->format('Y-m')]),
                'download_url' => route('payslip.download', [$salary->employee_id, now()->format('Y-m')])
            ];
        });
        
        // Return JSON response for API requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'data' => $payslips,
                'pagination' => [
                    'total' => $salaries->total(),
                    'per_page' => $salaries->perPage(),
                    'current_page' => $salaries->currentPage(),
                    'last_page' => $salaries->lastPage(),
                    'from' => $salaries->firstItem(),
                    'to' => $salaries->lastItem()
                ],
                'message' => 'Payslips retrieved successfully'
            ]);
        }
        
        // Return view for web requests
        return view('company_admin.payslips.index', [
            'payslips' => $payslips,
            'departments' => $departments,
            'salaries' => $salaries,
            'filters' => [
                'search' => $request->search,
                'department_id' => $request->department_id,
                'date_range' => $request->date_range,
            ]
        ]);
    }

    /**
     * Export payslips in the specified format
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportPayslips(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is authorized (admin or HR or company admin)
        if (!$user->hasAnyRole(['admin', 'hr', 'company_admin'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $format = $request->query('format', 'excel');
        $search = $request->query('search');
        $departmentId = $request->query('department_id');
        $dateRange = $request->query('date_range');
        
        $query = EmployeeSalary::with([
                'employee.user',
                'employee.department',
                'employee.designation'
            ])
            ->where('is_current', true);
            
        // Apply filters
        if ($search) {
            $query->whereHas('employee.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }
        
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                $endDate = \Carbon\Carbon::parse($dates[1])->endOfDay();
                
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('effective_from', [$startDate, $endDate])
                      ->orWhereBetween('effective_to', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('effective_from', '<=', $startDate)
                            ->where(function($q) use ($endDate) {
                                $q->where('effective_to', '>=', $endDate)
                                  ->orWhereNull('effective_to');
                            });
                      });
                });
            }
        }
        
        $salaries = $query->get();
        
        // Transform the data
        $data = $salaries->map(function ($salary) {
            return [
                'Employee ID' => $salary->employee->employee_id,
                'Employee Name' => $salary->employee->user->name,
                'Email' => $salary->employee->user->email,
                'Department' => $salary->employee->department->name ?? 'N/A',
                'Designation' => $salary->employee->designation->name ?? 'N/A',
                'Basic Salary' => number_format($salary->basic_salary, 2),
                'Gross Salary' => number_format($salary->gross_salary, 2),
                'Net Salary' => number_format($salary->net_salary, 2),
                'Effective From' => $salary->effective_from ? \Carbon\Carbon::parse($salary->effective_from)->format('d M, Y') : 'N/A',
                'Effective To' => $salary->effective_to ? \Carbon\Carbon::parse($salary->effective_to)->format('d M, Y') : 'Present',
            ];
        });
        
        if ($format === 'csv') {
            return $this->exportToCsv($data->toArray(), 'payslips');
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($data->toArray(), 'payslips');
        } else {
            return $this->exportToExcel($data->toArray(), 'payslips');
        }
    }
    
    /**
     * Export data to CSV
     * 
     * @param array $data
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if (count($data) > 0) {
                fputcsv($file, array_keys($data[0]));
            }
            
            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export data to Excel
     * 
     * @param array $data
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    private function exportToExcel($data, $filename)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PayslipsExport($data),
            $filename . '.xlsx'
        );
    }
    
    /**
     * Export data to PDF
     * 
     * @param array $data
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($data, $filename)
    {
        $pdf = PDF::loadView('pdf.payslips-export', ['data' => $data]);
        return $pdf->download($filename . '.pdf');
    }
}
