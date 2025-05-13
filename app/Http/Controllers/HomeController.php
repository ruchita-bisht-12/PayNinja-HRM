<?php

namespace App\Http\Controllers;

use App\Models\Company; // Added
use App\Models\User;    // Added
use App\Models\Department; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added for role breakdown

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
    public function index()
    {
        $totalCompanies = Company::count();
        $totalUsers = User::count();
        $totalDepartments = Department::count();

        $usersByRole = User::select('role', DB::raw('count(*) as total'))
                            ->groupBy('role')
                            ->pluck('total', 'role');

        // Fetch companies with their admin users
        // Eager load the 'admin' relationship to avoid N+1 queries
        $companiesWithAdmins = Company::with('admin')->get();

        // Get the authenticated user
        $loggedInUser = auth()->user();

        return view('home', compact(
            'totalCompanies',
            'totalUsers',
            'totalDepartments',
            'usersByRole',
            'companiesWithAdmins', // Add this to the compact function
            'loggedInUser'         // Add this to the compact function
        ));
    }

    public function blank()
    {
        return view('layouts.blank-page');
    }
}
