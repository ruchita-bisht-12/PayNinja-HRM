<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Auth::user()->employee; 

        if (!$employee) {
            return redirect()->route('home')->with('error', 'Unable to retrieve your employee information.');
        }
        $employeeId = $employee->id;

        $payrolls = Payroll::where('employee_id', $employeeId)
            ->latest()
            ->paginate(10);

        return view('employee.payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll $payroll)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('employee.payroll.index')
                ->with('error', 'Unable to retrieve your employee information.');
        }

        // Authorization: Ensure the payroll belongs to the authenticated employee
        if ($payroll->employee_id != $employee->id) {
            return redirect()->route('employee.payroll.index')
                ->with('error', 'You are not authorized to view this payslip.');
        }

        // Eager load all necessary relationships
        $payroll->load([
            'items' => function($query) {
                $query->orderBy('type')->orderBy('id');
            },
            'company',
            'employee.department',
            'employee.designation',
            'employee.beneficiaryBadges'
        ]);

        // Get active beneficiary badges for the employee
        $beneficiaryBadges = $employee->beneficiaryBadges()
            ->wherePivot('is_applicable', true)
            ->where(function($query) use ($payroll) {
                $query->whereNull('employee_beneficiary_badges.start_date')
                      ->orWhere('employee_beneficiary_badges.start_date', '<=', $payroll->pay_period_end);
            })
            ->where(function($query) use ($payroll) {
                $query->whereNull('employee_beneficiary_badges.end_date')
                      ->orWhere('employee_beneficiary_badges.end_date', '>=', $payroll->pay_period_start);
            })
            ->get()
            ->map(function($badge) {
                return [
                    'name' => $badge->name,
                    'type' => $badge->type === 'allowance' ? 'earning' : 'deduction',
                    'value' => $badge->pivot->custom_value ?? $badge->value,
                    'calculation_type' => $badge->pivot->custom_calculation_type ?? $badge->calculation_type,
                    'based_on' => $badge->pivot->custom_based_on ?? $badge->based_on,
                    'is_taxable' => $badge->is_taxable ?? false
                ];
            });

        // Debug: Log the items to check what's being loaded
        \Log::info('Payroll Items:', $payroll->items->toArray());
        \Log::info('Beneficiary Badges:', $beneficiaryBadges->toArray());

        return view('employee.payroll.show', compact('payroll', 'beneficiaryBadges'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        //
    }

    /**
     * Download the payslip as PDF
     *
     * @param  \App\Models\Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function downloadPayslip(Payroll $payroll)
    {
        $employee = Auth::user()->employee;

        if (!$employee) {
            return redirect()->route('employee.payroll.index')
                ->with('error', 'Unable to retrieve your employee information.');
        }

        // Authorization: Ensure the payroll belongs to the authenticated employee
        if ($payroll->employee_id != $employee->id) {
            return redirect()->route('employee.payroll.index')
                ->with('error', 'You are not authorized to download this payslip.');
        }

        // Eager load all necessary relationships
        $payroll->load([
            'items' => function($query) {
                $query->orderBy('type')->orderBy('id');
            },
            'company',
            'employee.department',
            'employee.designation'
        ]);

        // Prepare data for the PDF - include everything the view needs
        $data = [
            'payroll' => $payroll,
            'employee' => $employee,
            'monthYear' => $payroll->pay_period_start->format('Y-m'),
            'generatedDate' => now()->format('M d, Y')
        ];

        // Generate PDF using the payslip.blade.php template
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payslip', $data);
        
        // Set the PDF filename
        $filename = 'payslip-' . $payroll->employee->employee_id . '-' . 
                   $payroll->pay_period_start->format('M-Y') . '.pdf';
        
        // Return the PDF as a download
        return $pdf->download($filename);
    }
}
