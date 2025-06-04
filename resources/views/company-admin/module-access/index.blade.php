@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
        <div class="card-header d-flex justify-content-between align-items-center" style="background-color: yellow;">
    <h1 class="mb-0">(Under Development)</h1>
</div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Module Access Management</h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('company-admin.module-access.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Admin Access</th>
                                        <th>Employee Access</th>
                                        <th>Reporter Access</th>
                                        <th>Reportee Access</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Attendance Management -->
                                    <tr>
                                        <td>Attendance Management</td>
                                        <td>
                                            <div class="form-check">
                                                <!-- Hidden field to ensure value is sent even when unchecked -->
                                                <input type="hidden" name="modules[attendance][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[attendance][admin]" value="1" {{ $modules['attendance']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[attendance][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[attendance][employee]" value="1" {{ $modules['attendance']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[attendance][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[attendance][reporter]" value="1" {{ $modules['attendance']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[attendance][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[attendance][reportee]" value="1" {{ $modules['attendance']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Leave Management -->
                                    <tr>
                                        <td>Leave Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[leave][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][admin]" value="1" {{ $modules['leave']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[leave][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][employee]" value="1" {{ $modules['leave']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[leave][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][reporter]" value="1" {{ $modules['leave']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[leave][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][reportee]" value="1" {{ $modules['leave']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Salary Management -->
                                    <tr>
                                        <td>Salary Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[salary][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[salary][admin]" value="1" {{ $modules['salary']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[salary][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[salary][employee]" value="1" {{ $modules['salary']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[salary][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[salary][reporter]" value="1" {{ $modules['salary']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[salary][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[salary][reportee]" value="1" {{ $modules['salary']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Reimbursement -->
                                    <tr>
                                        <td>Reimbursement</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[reimbursement][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][admin]" value="1" {{ $modules['reimbursement']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[reimbursement][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][employee]" value="1" {{ $modules['reimbursement']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[reimbursement][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][reporter]" value="1" {{ $modules['reimbursement']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[reimbursement][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][reportee]" value="1" {{ $modules['reimbursement']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Teams Management -->
                                    <tr>
                                        <td>Teams Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[teams][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[teams][admin]" value="1" {{ $modules['teams']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[teams][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[teams][employee]" value="1" {{ $modules['teams']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[teams][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[teams][reporter]" value="1" {{ $modules['teams']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[teams][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[teams][reportee]" value="1" {{ $modules['teams']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Departments Management -->
                                    <tr>
                                        <td>Departments Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[departments][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[departments][admin]" value="1" {{ $modules['departments']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[departments][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[departments][employee]" value="1" {{ $modules['departments']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[departments][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[departments][reporter]" value="1" {{ $modules['departments']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[departments][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[departments][reportee]" value="1" {{ $modules['departments']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Designations Management -->
                                    <tr>
                                        <td>Designations Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[designations][admin]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[designations][admin]" value="1" {{ $modules['designations']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[designations][employee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[designations][employee]" value="1" {{ $modules['designations']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[designations][reporter]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[designations][reporter]" value="1" {{ $modules['designations']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check">
                                                <input type="hidden" name="modules[designations][reportee]" value="0">
                                                <input type="checkbox" class="form-check-input" name="modules[designations][reportee]" value="1" {{ $modules['designations']['reportee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
