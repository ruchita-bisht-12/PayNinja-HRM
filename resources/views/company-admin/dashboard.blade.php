@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Company Admin Dashboard</h2>
    
    <div class="row mt-4">
        <!-- Employee Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Employee Management</h5>
                    <p class="card-text">Manage employees and their roles within your company.</p>
                    <a href="{{ route('company-admin.employees.index') }}" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>Manage Employees
                    </a>
                </div>
            </div>
        </div>

        <!-- Module Access Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Module Access</h5>
                    <p class="card-text">Configure access rights for different modules.</p>
                    <a href="{{ route('company-admin.module-access.index') }}" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>Configure Access
                    </a>
                </div>
            </div>
        </div>

        <!-- Company Settings Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Company Settings</h5>
                    <p class="card-text">Manage company settings and configurations.</p>
                    <a href="{{ route('company-admin.settings.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <!-- Department Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Department Management</h5>
                    <p class="card-text">Manage company departments and their structures.</p>
                    <a href="{{ route('departments.index') }}" class="btn btn-primary">
                        <i class="fas fa-building me-2"></i>Manage Departments
                    </a>
                </div>
            </div>
        </div>

        <!-- Team Management Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Team Management</h5>
                    <p class="card-text">Manage teams and their members.</p>
                    <a href="{{ route('teams.index') }}" class="btn btn-primary">
                        <i class="fas fa-users-cog me-2"></i>Manage Teams
                    </a>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">View and generate company reports.</p>
                    <a href="{{ route('company-admin.reports.index') }}" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-2"></i>View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
