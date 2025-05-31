<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        $users = User::all();
        return view('superadmin.companies.index', compact('companies', 'users'));
    }

    public function create()
    {
        return view('superadmin.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'domain' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:10',
            'address' => 'nullable|string'
        ]);

        Company::create($validated);

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company created successfully.');
    }    public function show(Company $company)
    {
        // Load necessary relationships
        $company->load([
            'employees' => function($query) {
                $query->with('user', 'department', 'designation');
            },
            'departments' => function($query) {
                $query->withCount('employees')->take(5);
            },
            'designations' => function($query) {
                $query->withCount('employees')->take(5);
            }
        ]);

        // Get counts
        $companyAdminsCount = $company->employees->whereHas('user', fn($q) => $q->where('role', 'company_admin'))->count();
        $adminsCount = $company->employees->whereHas('user', fn($q) => $q->where('role', 'admin'))->count();
        $employeesCount = $company->employees->whereHas('user', fn($q) => $q->where('role', 'user'))->count();
        $departmentsCount = $company->departments->count();
        $designationsCount = $company->designations->count();

        // Get table data
        $companyAdmins = $company->employees()
            ->with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'company_admin'))
            ->take(5)
            ->get();

        $admins = $company->employees()
            ->with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'admin'))
            ->take(5)
            ->get();

        $employees = $company->employees()
            ->with(['user', 'department', 'designation'])
            ->whereHas('user', fn($q) => $q->where('role', 'user'))
            ->take(5)
            ->get();

        // Use already loaded relationships for departments and designations
        $departments = $company->departments;
        $designations = $company->designations;

        return view('superadmin.companies.show', compact(
            'company',
            'companyAdminsCount',
            'adminsCount',
            'employeesCount',
            'departmentsCount',
            'designationsCount',
            'companyAdmins',
            'admins',
            'employees',
            'departments',
            'designations'
        ));
    }

    public function edit(Company $company)
    {
        return view('superadmin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'domain' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:10',
            'address' => 'nullable|string'
        ]);

        $company->update($validated);

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
