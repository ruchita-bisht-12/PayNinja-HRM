@extends('layouts.app')

@section('title', 'Employee Salary History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Salary History - {{ $employee->name }}</h5>
                    <div class="card-tools">
                        <a href="{{ route('admin.salary.create', ['employee' => $employee->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Salary
                        </a>
                        <a href="{{ route('admin.salary.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Date Range Filter -->
                    <form action="{{ route('admin.salary.show', $employee->id) }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           value="{{ $start_date ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" 
                                           value="{{ $end_date ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.salary.show', $employee->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Basic Salary</th>
                                    <th>HRA</th>
                                    <th>DA</th>
                                    <th>Allowances</th>
                                    <th>Gross</th>
                                    <th>Deductions</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaries as $salary)
                                    <tr>
                                        <td>
                                            <div>{{ $salary->start_date ? \Carbon\Carbon::parse($salary->start_date)->format('d M Y') : 'N/A' }}</div>
                                            <div class="text-muted small">to</div>
                                            <div>{{ $salary->end_date ? \Carbon\Carbon::parse($salary->end_date)->format('d M Y') : 'Present' }}</div>
                                        </td>
                                        <td>₹{{ number_format($salary->basic_salary, 2) }}</td>
                                        <td>₹{{ number_format($salary->hra, 2) }}</td>
                                        <td>₹{{ number_format($salary->da, 2) }}</td>
                                        <td>₹{{ number_format($salary->other_allowances, 2) }}</td>
                                        <td>₹{{ number_format($salary->gross_salary, 2) }}</td>
                                        <td>₹{{ number_format($salary->total_deductions, 2) }}</td>
                                        <td><strong>₹{{ number_format($salary->net_salary, 2) }}</strong></td>
                                        <td>
                                            @if($salary->is_current)
                                                <span class="badge bg-success">Current</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($salary->paid_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Paid on 
                                                    {{ \Carbon\Carbon::parse($salary->paid_at)->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.salary.edit', $salary->id) }}" 
                                                   class="btn btn-primary" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.salary.destroy', $salary->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this salary record?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No salary records found for the selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
