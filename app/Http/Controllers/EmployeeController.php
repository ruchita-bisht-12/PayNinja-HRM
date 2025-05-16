<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeDetail;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index($companyId)
    {
        $company = Company::findOrFail($companyId);
        
        $employees = Employee::with(['department', 'designation', 'user'])
            ->whereHas('user', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->orderBy('name')
            ->get();

        return view('company.employees.index', compact('company', 'employees'));
    }

    public function create($companyId)
    {
        $company = Company::findOrFail($companyId);
        $departments = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();

        return view('company.employees.create', compact('company', 'departments', 'designations'));
    }

    public function store(Request $request, $companyId)
    {
        $company = Company::findOrFail($companyId);

        // Validate user data
        $validated = $request->validate([

            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'employment_type' => 'required|in:permanent,contract,intern',
            'address' => 'nullable|string|max:500',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'company_id' => $companyId,
            'role' => 'employee'
        ]);

        // Create employee record
        $employee = Employee::create([
            'phone' => $validated['emergency_contact'],
            'email' => $validated['email'],
            'company_id' => $companyId,
            'user_id' => $user->id,
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'designation_id' => $validated['designation_id'],
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'emergency_contact' => $validated['emergency_contact'] ?? '',
            'joining_date' => $validated['joining_date'],
            'employment_type' => $validated['employment_type'],
            'address' => $validated['address'] ?? '',
            'created_by' => auth()->id()
        ]);

        // Create employee details
        EmployeeDetail::create([
            'user_id' => $user->id,
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'joining_date' => $validated['joining_date'],
            'employment_type' => $validated['employment_type']
        ]);

        return redirect()->route('company.employees.index', $companyId)
            ->with('success', 'Employee created successfully');
    }
    public function show()
    {
        // Display logged-in employee data
        $employee = auth()->user();  // Get the logged-in employee
        return view('employee.profile', compact('employee'));
    }

    public function listColleagues()
    {
        $currentUser = auth()->user();
        $colleagues = collect(); // Default to an empty collection
        $companyName = 'N/A';

        if ($currentUser && $currentUser->company_id) {
            if ($currentUser->company) {
                $companyName = $currentUser->company->name;
            }

            $colleagues = User::where('company_id', $currentUser->company_id)
                                ->orderBy('name')
                                ->get();
        }

        return view('employee.colleagues', compact('colleagues', 'currentUser', 'companyName'));
    }

    public function edit($companyId, $employeeId)
    {
        $company = Company::findOrFail($companyId);
        
        // First check if the employee exists
        $employee = Employee::with(['department', 'designation', 'user', 'employeeDetail'])
            ->findOrFail($employeeId);
        
        // Get the associated user
        $user = User::findOrFail($employee->user_id);
        
        // Make sure employee detail exists
        $employeeDetail = EmployeeDetail::firstOrCreate(
            ['user_id' => $user->id],
            [
                'joining_date' => now(),
                'employment_type' => 'permanent'
            ]
        );
        
        $departments = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();

        return view('company.employees.edit', compact('company', 'employee', 'departments', 'designations'));
    }

    public function update(Request $request, $companyId, $employeeId)
    {
        // dd($request->all());
        $company = Company::findOrFail($companyId);
        
        // First check if the employee exists
        $employee = Employee::findOrFail($employeeId);
        
        // Get the associated user
        $user = User::findOrFail($employee->user_id);
        
        // Get default department and designation if available
        $defaultDepartment = Department::where('company_id', $companyId)->first();
        $defaultDesignation = Designation::where('company_id', $companyId)->first();
        
        // First validate fields other than email
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'phone' => 'nullable|string|max:20',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'employment_type' => 'required|in:permanent,contract,intern',
            'address' => 'nullable|string|max:500',
        ]);
        
        // Validate email separately if it's being changed
        if ($request->has('email') && $request->email !== $user->email) {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);
            
            // Update user's email
            $user->update([
                'name' => $validated['name'],
                'email' => $request->email
            ]);
        } else {
            // Just update the name
            $user->update([
                'name' => $validated['name']
            ]);
        }
        
        // Update employee record
        $employee->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
            'designation_id' => $validated['designation_id'],
            'phone' => $validated['phone'] ?? '', // Use empty string as fallback instead of null
            'address' => $validated['address'] ?? '',
            // Don't update email as it's already set and has a unique constraint
            // Don't update created_by as it's already set
            'dob' => $validated['dob'],
            'gender' => $validated['gender'],
            'emergency_contact' => $validated['emergency_contact'] ?? '',
            'joining_date' => $validated['joining_date'],
            'employment_type' => $validated['employment_type']
        ]);

        // Update or create employee details
        $employeeDetail = EmployeeDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dob' => $validated['dob'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'joining_date' => $validated['joining_date'],
                'employment_type' => $validated['employment_type'],
            ]
        );

        return redirect()->route('company.employees.index', $companyId)
            ->with('success', 'Employee updated successfully');
    }
    
    /**
     * Remove the specified employee from storage.
     *
     * @param  int  $companyId
     * @param  int  $employeeId
     * @return \Illuminate\Http\Response
     */
    public function destroy($companyId, $employeeId)
    {
        try {
            // Find the employee record
            $employee = Employee::findOrFail($employeeId);
            
            // Get the user ID before deleting the employee
            $userId = $employee->user_id;
            
            // First delete any team member records associated with this employee
            // This is necessary because of the foreign key constraint
            \DB::table('team_members')->where('employee_id', $employeeId)->delete();
            
            // Force delete the employee record (bypass soft delete)
            $employee->forceDelete();
            
            // Delete associated employee details
            $employeeDetail = EmployeeDetail::where('user_id', $userId)->first();
            if ($employeeDetail) {
                $employeeDetail->forceDelete();
            }
            
            // Optionally, you can also delete the user account if needed
            // Uncomment the following lines if you want to delete the user as well
            // $user = User::find($userId);
            // if ($user) {
            //     $user->delete();
            // }
            
            return redirect()->route('company.employees.index', $companyId)
                ->with('success', 'Employee deleted successfully');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error deleting employee: ' . $e->getMessage());
            
            return redirect()->route('company.employees.index', $companyId)
                ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }
}
