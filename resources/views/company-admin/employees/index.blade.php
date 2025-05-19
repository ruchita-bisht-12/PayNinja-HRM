@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Employee Management</h5>
                    <a href="{{ route('company.employees.create', ['companyId' => auth()->user()->company_id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Employee
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Current Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->user->name }}</td>
                                        <td>{{ $employee->user->email }}</td>
                                        <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($employee->user->role_name) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#roleModal{{ $employee->id }}">
                                                <i class="fas fa-user-edit me-1"></i>Change Role
                                            </button>

                                            <!-- Role Change Modal -->
                                            <div class="modal fade" id="roleModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Change Role for {{ $employee->user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('company-admin.employees.update-role', $employee->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="role" class="form-label">Select Role</label>
                                                                    <select name="role" id="role" class="form-select" required>
                                                                        <option value="admin" {{ $employee->user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                        <option value="employee" {{ $employee->user->role === 'employee' ? 'selected' : '' }}>Employee</option>
                                                                        <option value="reporter" {{ $employee->user->role === 'reporter' ? 'selected' : '' }}>Reporter</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $employees->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
