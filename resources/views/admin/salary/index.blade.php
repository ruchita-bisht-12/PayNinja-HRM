@extends('layouts.app')

@section('title', 'Employee Salaries')

@section('content')
<div class="section-header">
    <h1>Employee Salaries</h1>
   
</div>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Employee Salary List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="employee-salaries-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Basic Salary</th>
                                    <th>Total Allowances</th>
                                    <th>Total Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Effective From</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                    @php
                                        $salary = $employee->currentSalary;
                                        $totalAllowances = $salary ? ($salary->housing_allowance + $salary->transport_allowance + $salary->meal_allowance + $salary->medical_allowance) : 0;
                                        $totalDeductions = $salary ? ($salary->tax_deduction + $salary->insurance_deduction + $salary->other_deduction) : 0;
                                        $netSalary = $salary ? ($salary->basic_salary + $totalAllowances - $totalDeductions) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3 rounded-circle" style="width: 45px; height: 45px; overflow: hidden;">
                                                    <img src="{{ $employee->profile_photo_url }}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        <a href="{{ route('admin.salary.show', $employee->id) }}">{{ $employee->name }}</a>
                                                    </h6>
                                                    <small>{{ $employee->employee_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $salary ? number_format($salary->basic_salary, 2) : 'N/A' }}</td>
                                        <td>{{ $salary ? number_format($totalAllowances, 2) : 'N/A' }}</td>
                                        <td>{{ $salary ? number_format($totalDeductions, 2) : 'N/A' }}</td>
                                        <td>
                                            <strong>{{ $salary ? number_format($netSalary, 2) : 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            @if($salary && $salary->effective_from)
                                                @php
                                                    $effectiveDate = is_string($salary->effective_from) 
                                                        ? \Carbon\Carbon::parse($salary->effective_from)
                                                        : $salary->effective_from;
                                                @endphp
                                                {{ $effectiveDate->format('d M Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($salary)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-warning">Not Set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if($salary)
                                                    <a href="{{ route('admin.salary.edit', $salary->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.salary.show', $employee->id) }}" class="btn btn-sm btn-info" title="View History">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.salary.create', ['employee' => $employee->id]) }}" class="btn btn-sm btn-success" title="Set Salary">
                                                        <i class="fas fa-plus-circle"></i> Set Salary
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No employees found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable if needed
        // $('#employee-salaries-table').DataTable();
    });
</script>
@endpush
