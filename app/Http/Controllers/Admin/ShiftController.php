<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    /**
     * Display a listing of the shifts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::with('company')
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(10);
            
        return view('admin.shifts.index', compact('shifts'));
    }
    
    /**
     * Show the form for creating a new shift.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all();
        return view('admin.shifts.create', compact('companies'));
    }
    
    /**
     * Store a newly created shift in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
            'is_default' => 'boolean',
            'is_night_shift' => 'boolean',
            'has_break' => 'boolean',
            'break_start' => 'nullable|required_if:has_break,1|date_format:H:i|after:start_time|before:end_time',
            'break_end' => 'nullable|required_if:has_break,1|date_format:H:i|after:break_start|before:end_time',
            'description' => 'nullable|string',
        ]);
        
        // If this is set as default, unset default from other shifts
        if ($request->is_default) {
            Shift::where('company_id', $request->company_id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        
        $shift = Shift::create($validated);
        
        return redirect()
            ->route('admin.shifts.index')
            ->with('success', 'Shift created successfully');
    }
    
    /**
     * Display the specified shift.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function show(Shift $shift)
    {
        $this->authorize('view', $shift);
        $shift->load('company');
        return view('admin.shifts.show', compact('shift'));
    }
    
    /**
     * Show the form for editing the specified shift.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function edit(Shift $shift)
    {
        // $this->authorize('update', $shift);
        $companies = Company::all();
        return view('admin.shifts.edit', compact('shift', 'companies'));
    }
    
    /**
     * Update the specified shift in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shift $shift)
    {
        // $this->authorize('update', $shift);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
            'is_default' => 'boolean',
            'is_night_shift' => 'boolean',
            'has_break' => 'boolean',
            'break_start' => 'nullable|required_if:has_break,1|date_format:H:i|after:start_time|before:end_time',
            'break_end' => 'nullable|required_if:has_break,1|date_format:H:i|after:break_start|before:end_time',
            'description' => 'nullable|string',
        ]);
        
        // If this is set as default, unset default from other shifts
        if ($request->is_default) {
            Shift::where('company_id', $shift->company_id)
                ->where('id', '!=', $shift->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        } else {
            // If this was the default and we're unsetting it, set another as default
            $currentDefault = Shift::where('company_id', $shift->company_id)
                ->where('is_default', true)
                ->where('id', '!=', $shift->id)
                ->exists();
                
            if (!$currentDefault) {
                $newDefault = Shift::where('company_id', $shift->company_id)
                    ->where('id', '!=', $shift->id)
                    ->first();
                    
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }
        }
        
        $shift->update($validated);
        
        return redirect()
            ->route('admin.shifts.index')
            ->with('success', 'Shift updated successfully');
    }
    
    /**
     * Remove the specified shift from storage.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift)
    {
        // $this->authorize('delete', $shift);
        
        // Check if shift is assigned to any employee
        if ($shift->employeeShifts()->exists()) {
            return redirect()
                ->route('admin.shifts.index')
                ->with('error', 'Cannot delete shift. It is assigned to one or more employees.');
        }
        
        // If this was the default shift, set another as default
        if ($shift->is_default) {
            $newDefault = Shift::where('company_id', $shift->company_id)
                ->where('id', '!=', $shift->id)
                ->first();
                
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }
        
        $shift->delete();
        
        return redirect()
            ->route('admin.shifts.index')
            ->with('success', 'Shift deleted successfully');
    }
    
    /**
     * Show the form for assigning a shift to employees.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function showAssignForm(Shift $shift)
    {
        // $this->authorize('update', $shift);
        
        $employees = Employee::where('company_id', $shift->company_id)
            ->with('user')
            ->get();
            
        return view('admin.shifts.assign', compact('shift', 'employees'));
    }
    
    /**
     * Assign the shift to employees.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function assignShift(Request $request, Shift $shift)
    {
        // $this->authorize('update', $shift);
        
        $validated = $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_default' => 'boolean',
        ]);
        
        foreach ($validated['employee_ids'] as $employeeId) {
            // End any existing shift assignments that overlap
            EmployeeShift::where('employee_id', $employeeId)
                ->where(function($query) use ($validated) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', $validated['start_date']);
                })
                ->update(['end_date' => now()->subDay()]);
                
            // Create new shift assignment
            EmployeeShift::create([
                'employee_id' => $employeeId,
                'shift_id' => $shift->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_default' => $validated['is_default'] ?? false,
            ]);
            
            // If this is set as default, unset default from other shifts
            if ($validated['is_default'] ?? false) {
                EmployeeShift::where('employee_id', $employeeId)
                    ->where('id', '!=', $shift->id)
                    ->update(['is_default' => false]);
            }
        }
        
        return redirect()
            ->route('admin.shifts.index')
            ->with('success', 'Shift assigned to selected employees');
    }


    // API endpoint for shift timings
    public function getShiftTimings($shiftId)
    {
        $shift = Shift::findOrFail($shiftId);
        return response()->json([
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'grace_period_minutes' => $shift->grace_period_minutes,
            'has_break' => $shift->has_break,
            'break_start' => $shift->break_start,
            'break_end' => $shift->break_end,
            'is_night_shift' => $shift->is_night_shift
        ]);
    }
}
