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
            $companyEmployees = User::where('company_id', $companyId)
                ->select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->orderBy('total', 'desc')
                ->get();
                
            $companyRoleLabels = $companyEmployees->pluck('role');
            $companyRoleData = $companyEmployees->pluck('total');
            
            return view('company_admin.dashboard', compact(
                'roleLabels', 
                'roleData', 
                'roleColors',
                'companyRoleLabels',
                'companyRoleData'
            ));
        } elseif ($user->role === 'admin') {
            return view('admin.dashboard', compact(
                'roleLabels', 
                'roleData', 
                'roleColors'
            ));
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
