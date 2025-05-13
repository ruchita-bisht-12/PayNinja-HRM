<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User; // Added User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Display all companies
        
        $companies = Company::with('admin')->get(); // Eager load admin
        // dd($companies);
        $users = User::with('company')->get(); // Fetch users with their company
        // dd($users);
        return view('superadmin.companies.index',compact('users','companies')); // Pass users to view
        
    }

    public function create()
    {
        // Show form to create new company
        $users = User::all(); // Fetch all users
        return view('superadmin.companies.create', compact('users')); // Pass users to view
    }

    public function store(Request $request)
    {
        
        // dd($request->all());
        // Validate company data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'domain' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'admin_id' => 'required|exists:users,id',
            // 'logo' => 'nullable|string',
            // 'created_by' => 'required|exists:super_admins,id'
        ]);

        $validated['created_by'] = Auth::user()->id; 
      
        // Store company data
        $company = Company::create($validated);

 // Update the admin's company_id if needed
 $admin = \App\Models\User::find($validated['admin_id']);
 $admin->company_id = $company->id;
 $admin->save();


        return redirect()->route('superadmin.companies.index')->with('success', 'Company Created Successfully');
    }

    public function edit($id)
    {
        // Show form to edit an existing company
        $company = Company::findOrFail($id);
        $users = User::all(); // Fetch all users
        return view('superadmin.companies.edit', compact('company', 'users')); // Pass users to view
    }

    public function update(Request $request, $id)
    {
        // Update company data
        $company = Company::findOrFail($id);

        // Validate input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:companies,email,$id",
            'domain' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'admin_id' => 'required|exists:users,id',
            // 'logo' => 'nullable|string'
        ]);

        $validated['created_by'] = Auth::user()->id; 

        // Update company
        $company->update($validated);

        // Update the admin's company_id if needed
        $admin = \App\Models\User::find($validated['admin_id']);
        $admin->company_id = $company->id;
        $admin->save();

        return redirect()->route('superadmin.companies.index')->with('success', 'Company Updated Successfully');
    }

    public function destroy($id)
    {
        // Delete company
        $company = Company::findOrFail($id);
        $company->delete();
        return redirect()->route('superadmin.companies.index')->with('success', 'Company Deleted Successfully');
    }
}
