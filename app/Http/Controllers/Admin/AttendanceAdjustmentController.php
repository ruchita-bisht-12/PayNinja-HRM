<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendanceAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAttendanceAdjustment::with(['employee', 'approvedBy']);
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status') && in_array($request->status, ['pending', 'approved'])) {
            $query->where('is_approved', $request->status === 'approved');
        }
        
        $adjustments = $query->latest()->paginate(20);
        
        return view('admin.attendance-adjustments.index', compact('adjustments'));
    }
    
    public function create()
    {
        $employees = Employee::active()->get();
        return view('admin.attendance-adjustments.create', compact('employees'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'type' => 'required|in:half_day,reimbursement',
            'amount' => 'required_if:type,reimbursement|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);
        
        $adjustment = new EmployeeAttendanceAdjustment($validated);
        $adjustment->is_approved = $request->user()->hasRole('admin');
        $adjustment->approved_by = $request->user()->hasRole('admin') ? $request->user()->id : null;
        $adjustment->approved_at = $request->user()->hasRole('admin') ? now() : null;
        $adjustment->save();
        
        return redirect()->route('admin.attendance-adjustments.index')
            ->with('success', 'Adjustment created successfully');
    }
    
    public function approve(EmployeeAttendanceAdjustment $adjustment)
    {
        if ($adjustment->is_approved) {
            return redirect()->back()->with('error', 'Adjustment is already approved');
        }
        
        $adjustment->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Adjustment approved successfully');
    }
    
    public function destroy(EmployeeAttendanceAdjustment $adjustment)
    {
        $adjustment->delete();
        return redirect()->back()->with('success', 'Adjustment deleted successfully');
    }
}
