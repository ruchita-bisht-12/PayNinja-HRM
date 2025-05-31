<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\ModuleAccess;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:company_admin');
    }

    /**
     * Display the company admin dashboard.
     */
    // public function dashboard()
    // {
    //     $user = Auth::user();
        
    //     try {
    //         // Check if user has an employee record
    //         if (!$user->employee) {
    //             // Get the first company if company_id is not set
    //             $companyId = $user->company_id ?? Company::first()?->id;
                
    //             if (!$companyId) {
    //                 // If no company exists, create a default one
    //                 // $company = Company::create([
    //                 //     'name' => $user->name . "'s Company",
    //                 //     'email' => $user->email,
    //                 //     'phone' => '',
    //                 //     'website' => '',
    //                 //     'address' => '',
    //                 //     'status' => 'active',
    //                 //     'created_by' => $user->id,
    //                 // ]);
    //                 // $companyId = $company->id;
    //                 // $user->company_id = $companyId;
    //                 // $user->save();
    //                 Log::info('No company found for this user');
    //             }

    //             // Create a basic employee record
    //             $employee = Employee::create([
    //                 'user_id' => $user->id,
    //                 'company_id' => $companyId,
    //                 'name' => $user->name,
    //                 'email' => $user->email,
    //                 'department_id' => 1, // Default department
    //                 'designation_id' => 1, // Default designation
    //                 'gender' => 'other',
    //                 'employment_type' => 'full_time',
    //                 'joining_date' => now(),
    //                 'phone' => '',
    //                 'address' => '',
    //                 'emergency_contact' => '',
    //                 'status' => 'active',
    //                 'created_by' => $user->id,
    //             ]);

    //             // Refresh the user's employee relationship
    //             $user = \App\Models\User::find($user->id);
    //             $user->load('employee');
    //         }

    //         // Make sure we have a valid user
    //         if (!$user) {
    //             throw new \Exception('User not found');
    //         }

    //         // Get the company, either from employee or directly from user
    //         $company = $user->company;
            
    //         if (!$company) {
    //             throw new \Exception('No company found for this user');
    //         }

    //         return view('company-admin.dashboard.index', compact('company'));
            
    //     } catch (\Exception $e) {
    //         Log::error('Error in CompanyAdminController@dashboard: ' . $e->getMessage());
    //         return redirect()->route('home')->with('error', 'Failed to load dashboard: ' . $e->getMessage());
    //     }
    // }

    public function dashboard()
{
    $user = Auth::user();
    
    try {
        // Check if user has an employee record
        if (!$user->employee) {
            // Get the first company if company_id is not set
            $companyId = $user->company_id ?? Company::first()?->id;
            
            if (!$companyId) {
                Log::info('No company found for this user');
            }

            // Create a basic employee record
            $employee = Employee::create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'name' => $user->name,
                'email' => $user->email,
                'department_id' => Department::first()?->id ?? 1,
                'designation_id' => Designation::first()?->id ?? 1,
                'gender' => 'other',
                'employment_type' => 'full_time',
                'joining_date' => now(),
                'phone' => '',
                'address' => '',
                'emergency_contact' => '',
                'status' => 'active',
                'created_by' => $user->id,
            ]);

            // Refresh the user's employee relationship
            $user = User::find($user->id);
            $user->load('employee');
        }

        // Get the company
        $company = $user->company;
        
        if (!$company) {
            throw new \Exception('No company found for this user');
        }

        // Get total employees
        $totalEmployees = User::where('company_id', $company->id)->count();

        // Get today's attendance count
        $todayAttendanceCount = DB::table('attendances as a')
            ->join('employees as e', 'a.employee_id', '=', 'e.id')
            ->whereDate('a.created_at', now()->format('Y-m-d'))
            ->where('e.company_id', $company->id)
            ->count();

        // Get employees on leave
        $onLeaveCount = \App\Models\LeaveRequest::whereHas('employee', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where('status', 'approved')
            ->count();

        // Get department count
        $departmentCount = Department::where('company_id', $company->id)->count();

        // Get employee distribution by role
        $roles = User::where('company_id', $company->id)
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $companyRoleLabels = $roles->keys()->toArray();
        $companyRoleData = $roles->values()->toArray();

        return view('company_admin.dashboard', compact(
            'totalEmployees',
            'todayAttendanceCount',
            'onLeaveCount',
            'departmentCount',
            'companyRoleLabels',
            'companyRoleData'
        ));
        
    } catch (\Exception $e) {
        Log::error('Error in CompanyAdminController@dashboard: ' . $e->getMessage());
        return redirect()->route('home')->with('error', 'Failed to load dashboard: ' . $e->getMessage());
    }
}

    /**
     * Display module access management page.
     */
    public function moduleAccess()
    {
        $user = Auth::user();
        $company = $user->employee->company;
        
        // Get current module access settings
        $modules = ModuleAccess::where('company_id', $company->id)
            ->get()
            ->groupBy('module_name')
            ->map(function ($moduleGroup) {
                return $moduleGroup->mapWithKeys(function ($access) {
                    return [$access->role => $access->has_access];
                });
            })
            ->toArray();

        return view('company-admin.module-access.index', compact('modules'));
    }

    /**
     * Update module access settings.
     */
    public function updateModuleAccess(Request $request)
    {
        try {
            $user = Auth::user();
            $company = $user->employee->company;

            DB::beginTransaction();

            foreach ($request->input('modules', []) as $moduleName => $roleAccess) {
                foreach ($roleAccess as $role => $hasAccess) {
                    ModuleAccess::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'module_name' => $moduleName,
                            'role' => $role,
                        ],
                        ['has_access' => (bool) $hasAccess]
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Module access settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating module access: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating module access settings.');
        }
    }

    /**
     * List employees for the company.
     */
    public function employees()
    {
        $user = Auth::user();
        $company = $user->employee->company;
        
        $employees = Employee::with(['user', 'department'])
            ->where('company_id', $company->id)
            ->paginate(10);

        return view('company-admin.employees.index', compact('employees'));
    }

    /**
     * Update employee role.
     */
    public function updateEmployeeRole(Request $request, Employee $employee)
    {
        try {
            $user = Auth::user();
            $company = $user->employee->company;

            // Ensure employee belongs to the same company
            if ($employee->company_id !== $company->id) {
                return redirect()->back()->with('error', 'Unauthorized action.');
            }

            $request->validate([
                'role' => 'required|in:admin,employee,reporter'
            ]);

            DB::beginTransaction();

            // Load the user relationship if not already loaded
            $employee->load('user');
            
            // Check if user exists
            if (!$employee->user) {
                throw new \Exception('User record not found for this employee.');
            }

            // Update user role
            $employee->user->role = $request->role;
            $employee->user->save();

            DB::commit();
            return redirect()->back()->with('success', 'Employee role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating employee role: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating employee role: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new employee.
     */
    public function createEmployee()
    {
        $user = Auth::user();
        $company = $user->employee->company;
        
        $departments = \App\Models\Department::where('company_id', $company->id)->get();
        $designations = \App\Models\Designation::where('company_id', $company->id)->get();
        
        return view('company-admin.employees.create', compact('company', 'departments', 'designations'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function storeEmployee(Request $request)
    {
        $user = Auth::user();
        $company = $user->employee->company;

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'joining_date' => 'required|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'employee',
                'company_id' => $company->id,
            ]);

            // Create employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'],
                'employment_type' => $validated['employment_type'],
                'joining_date' => $validated['joining_date'],
                'address' => $validated['address'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('company-admin.employees.index')
                ->with('success', 'Employee created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating employee: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating employee. Please try again.');
        }
    }

    /**
     * Display company settings.
     */
    public function settings()
    {
        $user = Auth::user();
        $company = $user->employee->company;
        
        return view('company-admin.settings.index', compact('company'));
    }

    /**
     * Update company settings.
     */
    public function updateSettings(Request $request)
    {
        try {
            $user = Auth::user();
            $company = $user->employee->company;

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'website' => 'required|url',
                'address' => 'required|string'
            ]);

            $company->update($request->all());

            return redirect()->back()->with('success', 'Company settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating company settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating company settings.');
        }
    }
}
