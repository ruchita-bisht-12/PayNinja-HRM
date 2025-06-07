<?php

namespace App\Http\Controllers;

use App\Models\Company; // Added
use App\Models\User;    // Added
use App\Models\Department; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added for role breakdown
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $totalCompanies = Company::count();
    //     $totalUsers = User::count();
    //     $totalDepartments = Department::count();

    //     $usersByRole = User::select('role', DB::raw('count(*) as total'))
    //                         ->groupBy('role')
    //                         ->pluck('total', 'role');

    //     // Fetch companies with their admin users
    //     // Eager load the 'admin' relationship to avoid N+1 queries
    //     $companiesWithAdmins = Company::with('admin')->get();

    //     // Get the authenticated user
    //     $loggedInUser = auth()->user();

    //     return view('home', compact(
    //         'totalCompanies',
    //         'totalUsers',
    //         'totalDepartments',
    //         'usersByRole',
    //         'companiesWithAdmins', // Add this to the compact function
    //         'loggedInUser'         // Add this to the compact function
    //     ));
    // }

    public function index(){
        $user = Auth::user();
        $loggedInUser = $user;
        
        // Common data for all roles
        $employeeRoles = User::whereNotNull('role')
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->orderBy('total', 'desc')
            ->get();
            
        // Prepare data for charts
        $roleLabels = $employeeRoles->pluck('role');
        $roleData = $employeeRoles->pluck('total');
        $roleColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
            '#5a5c69', '#858796', '#e83e8c', '#fd7e14', '#20c9a6'
        ];
        
        // Sample upcoming holidays (replace with actual data from your database)
        $upcomingHolidays = [
            [
                'name' => 'New Year\'s Day',
                'date' => now()->addDays(5)->format('Y-m-d'),
                'type' => 'Public Holiday'
            ],
            [
                'name' => 'Republic Day',
                'date' => now()->addDays(15)->format('Y-m-d'),
                'type' => 'Public Holiday'
            ],
            [
                'name' => 'Company Foundation Day',
                'date' => now()->addDays(30)->format('Y-m-d'),
                'type' => 'Company Holiday'
            ]
        ];

        if ($user->role === 'superadmin') {
            $totalCompanies = Company::count();
            $totalUsers = User::count();
            $totalDepartments = Department::count();
            $usersByRole = User::select('role', DB::raw('count(*) as total'))
                                ->groupBy('role')
                                ->pluck('total', 'role');
            $companiesWithAdmins = Company::with('admin')->get();
            
            return view('superadmin.dashboard', compact(
                'totalCompanies', 
                'totalUsers', 
                'totalDepartments', 
                'usersByRole', 
                'companiesWithAdmins', 
                'loggedInUser',
                'roleLabels',
                'roleData',
                'roleColors'
            ));
        } elseif ($user->role === 'company_admin') {
            // For company admin, show data for their company only
            $companyId = $user->company_id;
            
            // Get employee distribution by role
            $companyEmployees = User::where('company_id', $companyId)
                ->select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->orderBy('total', 'desc')
                ->get();
            
            // Get department count
            $departmentCount = Department::where('company_id', $companyId)->count();
            
            // Get today's attendance
            $today = now()->format('Y-m-d');
            $todayAttendanceCount = DB::table('attendances as a')
                ->join('employees as e', 'a.employee_id', '=', 'e.id')
                ->whereNull('a.deleted_at')
                ->whereDate('a.created_at', $today)
                ->where('e.company_id', $companyId)
                ->count();
            
            $totalEmployees = $companyEmployees->sum('total');
            
            // Get pending leave requests count
            $pendingLeaves = \App\Models\LeaveRequest::whereHas('employee', function($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->where('status', 'pending')
                ->count();
            
            // Sample recent activities (replace with actual activities from your system)
            $recentActivities = [
                [
                    'title' => 'New employee onboarded',
                    'time' => '2 hours ago',
                    'description' => 'John Doe joined the Marketing team'
                ],
                [
                    'title' => 'Leave request approved',
                    'time' => '5 hours ago',
                    'description' => 'Approved leave request for Jane Smith'
                ],
                [
                    'title' => 'New department created',
                    'time' => '1 day ago',
                    'description' => 'Created new department: Product Development'
                ],
                [
                    'title' => 'Performance review completed',
                    'time' => '2 days ago',
                    'description' => 'Completed Q2 performance reviews for Sales team'
                ],
                [
                    'title' => 'Training session scheduled',
                    'time' => '3 days ago',
                    'description' => 'Scheduled customer service training for next week'
                ]
            ];
            
            $companyRoleLabels = $companyEmployees->pluck('role');
            $companyRoleData = $companyEmployees->pluck('total');
            
            return view('company_admin.dashboard', compact(
                'roleLabels', 
                'roleData', 
                'roleColors',
                'companyRoleLabels',
                'companyRoleData',
                'departmentCount',
                'todayAttendanceCount',
                'totalEmployees',
                'recentActivities',
                'pendingLeaves',
                'upcomingHolidays'
            ));
        } elseif ($user->role === 'admin') {
            // Get total employees in the company
            $totalEmployees = User::where('company_id', $user->company_id)
                ->where('role', '!=', 'superadmin')
                ->where('role', '!=', 'company_admin')
                ->count();

            // Get department data for the company
            $departments = Department::where('company_id', $user->company_id)
                ->withCount('employees')
                ->orderBy('employees_count', 'desc')
                ->get();
                
            $departmentNames = $departments->pluck('name')->toArray();
            $departmentCounts = $departments->pluck('employees_count')->toArray();
            
            $departmentData = [
                'names' => $departmentNames,
                'counts' => $departmentCounts
            ];

            // Get today's attendance count
            $today = now()->format('Y-m-d');
            $todayAttendanceCount = DB::table('attendances as a')
                ->join('employees as e', 'a.employee_id', '=', 'e.id')
                ->whereDate('a.created_at', $today)
                ->whereNull('a.deleted_at')
                ->where('e.company_id', $user->company_id)
                ->count();
                
            // Get employees on leave today
            $onLeaveCount = \App\Models\LeaveRequest::whereHas('employee', function($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                })
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->where('status', 'approved')
                ->count();

            // Get pending leave requests count
            $pendingRequests = \App\Models\LeaveRequest::whereHas('employee', function($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                })
                ->where('status', 'pending')
                ->count();

            return view('admin.dashboard', [
                'totalEmployees' => $totalEmployees,
                'departmentCount' => count($departmentData['names']),
                'departmentData' => $departmentData,
                'todayAttendanceCount' => $todayAttendanceCount,
                'onLeaveCount' => $onLeaveCount,
                'pendingRequests' => $pendingRequests,
                'roleLabels' => $roleLabels,
                'roleData' => $roleData,
                'roleColors' => $roleColors,
            ]);
        } elseif ($user->role === 'user') {
            return view('user.dashboard', compact('loggedInUser'));
        } else {
            // Employee dashboard
            $employee = $user->employee;
            
            if (!$employee) {
                return redirect()->route('home')->with('error', 'Employee record not found.');
            }

            // Get today's attendance
            $todayAttendance = $employee->attendances()
                ->whereDate('date', now()->toDateString())
                ->first();

            // Get leave balance
            $leaveBalance = $employee->leaveBalance ?? 0;

            return view('employee.dashboard', compact(
                'loggedInUser',
                'todayAttendance',
                'leaveBalance'
            ));
        }
    }

    public function blank()
    {
        return view('layouts.blank-page');
    }
}
