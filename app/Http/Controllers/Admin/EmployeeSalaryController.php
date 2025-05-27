<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of employee salaries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('currentSalary')
            ->orderBy('name')
            ->paginate(10);
            
        return view('admin.salary.index', compact('employees'));
    }

    /**
     * Show the form for creating a new salary record.
     *
     * @param int $employeeId
     * @return \Illuminate\Http\Response
     */
    public function create($employeeId)
    {
        
        $employee = Employee::findOrFail($employeeId);
        $currentSalary = $employee->currentSalary;
        
        return view('admin.salary.create', compact('employee', 'currentSalary'));
    }

    /**
     * Store a newly created salary record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $employeeId)
    {
        // Log the start of the process
        Log::channel('single')->info('Starting salary creation', [
            'employee_id' => $employeeId,
            'user_id' => auth()->id()
        ]);
    
        try {
            // Validate the request
            $validated = $request->validate([
                'basic_salary' => 'required|numeric|min:0',
                'hra' => 'required|numeric|min:0',
                'da' => 'required|numeric|min:0',
                'other_allowances' => 'required|numeric|min:0',
                'pf_deduction' => 'required|numeric|min:0',
                'esi_deduction' => 'required|numeric|min:0',
                'tds_deduction' => 'required|numeric|min:0',
                'professional_tax' => 'nullable|numeric|min:0',
                'loan_deductions' => 'nullable|numeric|min:0',
                'leaves_deduction' => 'nullable|numeric|min:0',
                'effective_from' => 'required|date|after_or_equal:today',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'paid_at' => 'nullable|date',
                'bank_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'is_current' => 'sometimes|boolean',
            ]);
    
            DB::beginTransaction();
    
            // Find employee with user relationship
            $employee = Employee::with('user')->findOrFail($employeeId);
            
            // Calculate salary components
            $grossSalary = $request->basic_salary + 
                          $request->hra + 
                          $request->da + 
                          $request->other_allowances;
    
            $totalDeductions = $request->pf_deduction + 
                              $request->esi_deduction + 
                              $request->tds_deduction + 
                              ($request->professional_tax ?? 0) + 
                              ($request->loan_deductions ?? 0) +
                              ($request->leaves_deduction ?? 0);
    
            $netSalary = $grossSalary - $totalDeductions;
    
            // If this is set as current, update other records
            if ($request->boolean('is_current')) {
                EmployeeSalary::where('employee_id', $employeeId)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }
    
            // Create new salary record
            $salary = new EmployeeSalary([
                'employee_id' => $employeeId,
                'basic_salary' => $request->basic_salary,
                'hra' => $request->hra,
                'da' => $request->da,
                'other_allowances' => $request->other_allowances,
                'gross_salary' => $grossSalary,
                'pf_deduction' => $request->pf_deduction,
                'esi_deduction' => $request->esi_deduction,
                'tds_deduction' => $request->tds_deduction,
                'professional_tax' => $request->professional_tax ?? 0,
                'loan_deductions' => $request->loan_deductions ?? 0,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'currency' => 'INR',
                'payment_method' => 'bank_transfer',
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'status' => 'active',
                'effective_from' => $request->effective_from,
                'is_current' => $request->boolean('is_current', false),
                'notes' => $request->notes,
            ]);
    
            $salary->save();
            
            DB::commit();
    
            // Log successful creation
            Log::info('Salary record created successfully', [
                'salary_id' => $salary->id,
                'employee_id' => $employeeId,
                'net_salary' => $netSalary
            ]);
    
            return redirect()
                ->route('admin.salary.show', $employeeId)
                ->with('success', 'Salary record created successfully.');
    
        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
            
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Employee not found', ['employee_id' => $employeeId]);
            return back()->with('error', 'Employee not found.')->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salary creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // dd([
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            // ]);
            return back()
                ->with('error', 'Failed to create salary record. Please try again.')
                ->withInput();
        }
    }


    /**
     * Display the specified employee's salary history.
     *
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function show($employeeId, Request $request)
    {
        $employee = Employee::findOrFail($employeeId);
        
        // Start building the query
        $query = $employee->salaries()->orderBy('effective_from', 'desc');
        
        // Apply date filters if provided
        if ($request->filled('start_date')) {
            $query->where('effective_from', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('effective_from', '<=', $request->end_date);
        }
        
        // Get the filtered salaries
        $salaries = $query->get();
        
        return view('admin.salary.show', [
            'employee' => $employee,
            'salaries' => $salaries,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
    }

    /**
     * Show the form for editing the specified salary record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $salary = EmployeeSalary::findOrFail($id);
        $employee = $salary->employee;
        
        return view('admin.salary.edit', compact('salary', 'employee'));
    }

    /**
     * Update the specified salary record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'hra' => 'required|numeric|min:0',
            'da' => 'required|numeric|min:0',
            'other_allowances' => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'notes' => 'nullable|string',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            $salary = EmployeeSalary::findOrFail($id);
            
            // Set default values for missing fields
            $pfDeduction = $salary->pf_deduction ?? 0;
            $esiDeduction = $salary->esi_deduction ?? 0;
            $professionalTax = $salary->professional_tax ?? 0;
            $leavesDeduction = $request->leaves_deduction ?? $salary->leaves_deduction ?? 0;
            $totalDeductions = $pfDeduction + $esiDeduction + $professionalTax + $leavesDeduction;
            
            // Calculate gross and net salary
            $grossSalary = $request->basic_salary + $request->hra + $request->da + $request->other_allowances;
            $netSalary = $grossSalary - $totalDeductions;
            
            // Update salary record
            $salary->basic_salary = $request->basic_salary;
            $salary->hra = $request->hra;
            $salary->da = $request->da;
            $salary->other_allowances = $request->other_allowances;
            $salary->gross_salary = $grossSalary;
            $salary->total_deductions = $totalDeductions;
            $salary->net_salary = $netSalary;
            $salary->effective_from = $request->effective_from;
            $salary->start_date = $request->start_date;
            $salary->end_date = $request->end_date;
            $salary->leaves_deduction = $leavesDeduction;
            $salary->paid_at = $request->paid_at;
            $salary->notes = $request->notes;
            $salary->save();
            
            DB::commit();
            
            return redirect()->route('admin.salary.show', $salary->employee_id)
                ->with('success', 'Salary record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating salary record: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Failed to update salary record. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified salary record from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $salary = EmployeeSalary::findOrFail($id);
            $employeeId = $salary->employee_id;
            
            $salary->delete();
            
            return redirect()->route('admin.salary.show', $employeeId)
                ->with('success', 'Salary record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting salary record: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete salary record. ' . $e->getMessage());
        }
    }
}
