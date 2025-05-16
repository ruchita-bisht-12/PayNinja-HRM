<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin');
    }

    public function index()
    {
        $companies = Company::with('admin')->get();
        $users = User::with('company')->get();
        return view('superadmin.companies.index', compact('users', 'companies'));
    }

    public function create()
    {
        $users = User::all();
        return view('superadmin.companies.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'domain' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'admin_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = Auth::user()->id;
        $company = Company::create($validated);

        $admin = User::find($validated['admin_id']);
        $admin->company_id = $company->id;
        $admin->role = 'companyadmin';
        $admin->save();

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company Created Successfully');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $users = User::all();
        return view('superadmin.companies.edit', compact('company', 'users'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:companies,email,$id",
            'domain' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'admin_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = Auth::user()->id;
        $company->update($validated);

        $admin = User::find($validated['admin_id']);
        $admin->company_id = $company->id;
        $admin->role = 'companyadmin';
        $admin->save();

        return redirect()->route('company-super-admin.companies.index')
            ->with('success', 'Company Updated Successfully');
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return redirect()->route('company-super-admin.companies.index')
            ->with('success', 'Company Deleted Successfully');
    }
}
