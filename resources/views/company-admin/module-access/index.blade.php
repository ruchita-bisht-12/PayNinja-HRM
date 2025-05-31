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
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Leave Management -->
                                    <tr>
                                        <td>Leave Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][admin]" value="1" {{ $modules['leave']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][employee]" value="1" {{ $modules['leave']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[leave][reporter]" value="1" {{ $modules['leave']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Reimbursement -->
                                    <tr>
                                        <td>Reimbursement</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][admin]" value="1" {{ $modules['reimbursement']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][employee]" value="1" {{ $modules['reimbursement']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[reimbursement][reporter]" value="1" {{ $modules['reimbursement']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Team Management -->
                                    <tr>
                                        <td>Team Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[team][admin]" value="1" {{ $modules['team']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[team][employee]" value="1" {{ $modules['team']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[team][reporter]" value="1" {{ $modules['team']['reporter'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Department Management -->
                                    <tr>
                                        <td>Department Management</td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[department][admin]" value="1" {{ $modules['department']['admin'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[department][employee]" value="1" {{ $modules['department']['employee'] ?? false ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="modules[department][reporter]" value="1" {{ $modules['department']['reporter'] ?? false ? 'checked' : '' }}>
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
