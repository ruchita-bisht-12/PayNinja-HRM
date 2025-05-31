@extends('layouts.app')

@section('title', 'My Salary Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">My Salary History - {{ $employee->name }}</h5>
                    <div class="card-tools">
                        @if($currentSalary)
                            <div class="btn-group mr-2">
                                <a href="{{ route('employee.salary.payslip.view', ['employee' => $employee->id, 'monthYear' => now()->format('Y-m')]) }}" 
                                   class="btn btn-info btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf"></i> View Payslip
                                </a>
                                <a href="{{ route('employee.salary.payslip.download', ['employee' => $employee->id, 'monthYear' => now()->format('Y-m')]) }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($currentSalary)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Current Salary Structure</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Earnings</h6>
                                                <table class="table table-bordered mb-4">
                                                    <tbody>
                                                        <tr>
                                                            <th class="w-50">Basic Salary</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->basic_salary, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>HRA</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->hra, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>DA</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->da, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Other Allowances</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->other_allowances, 2) }}</td>
                                                        </tr>
                                                        <tr class="table-active">
                                                            <th><strong>Gross Salary</strong></th>
                                                            <th class="text-right">₹{{ number_format($currentSalary->gross_salary, 2) }}</th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Deductions</h6>
                                                <table class="table table-bordered mb-4">
                                                    <tbody>
                                                        <tr>
                                                            <th class="w-50">PF Deduction</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->pf_deduction, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>ESI Deduction</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->esi_deduction, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>TDS</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->tds_deduction, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Professional Tax</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->professional_tax, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Loan Deductions</th>
                                                            <td class="text-right">₹{{ number_format($currentSalary->loan_deductions, 2) }}</td>
                                                        </tr>
                                                        <tr class="table-active">
                                                            <th><strong>Total Deductions</strong></th>
                                                            <th class="text-right">₹{{ number_format($currentSalary->total_deductions, 2) }}</th>
                                                        </tr>
                                                        <tr class="table-success">
                                                            <th><h5 class="mb-0">Net Salary</h5></th>
                                                            <th class="text-right"><h5 class="mb-0">₹{{ number_format($currentSalary->net_salary, 2) }}</h5></th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <p class="text-muted mt-2 mb-0">
                                            <small>Effective from: {{ \Carbon\Carbon::parse($currentSalary->effective_from)->format('d M, Y') }}
                                            @if($currentSalary->is_current)
                                                <span class="badge bg-success ml-2">Current</span>
                                            @endif
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No salary information available.
                        </div>
                    @endif

                    @if($salaryHistory->count() > 0)
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Salary Revision History</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Period</th>
                                                <th class="text-right">Basic</th>
                                                <th class="text-right">HRA</th>
                                                <th class="text-right">DA</th>
                                                <th class="text-right">Allowances</th>
                                                <th class="text-right">Gross</th>
                                                <th class="text-right">Deductions</th>
                                                <th class="text-right">Net Salary</th>
                                                <th>Status</th>
                                                <th>Payment</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salaryHistory as $salary)
                                                <tr>
                                                    <td>
                                                        <div>{{ \Carbon\Carbon::parse($salary->start_date)->format('d M Y') }}</div>
                                                        <div class="text-muted small">to</div>
                                                        <div>{{ $salary->end_date ? \Carbon\Carbon::parse($salary->end_date)->format('d M Y') : 'Present' }}</div>
                                                    </td>
                                                    <td class="text-right">₹{{ number_format($salary->basic_salary, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->hra, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->da, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->other_allowances, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->gross_salary, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->total_deductions, 2) }}</td>
                                                    <td class="text-right"><strong>₹{{ number_format($salary->net_salary, 2) }}</strong></td>
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
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-clock"></i> Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('employee.salary.payslip.download', [
                                                            'employee' => $employee->id, 
                                                            'monthYear' => \Carbon\Carbon::parse($salary->start_date)->format('Y-m'),
                                                            'salaryId' => $salary->id
                                                        ]) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="Download Payslip"
                                                           @if(!$salary->paid_at) disabled @endif>
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        border: none;
        border-radius: 0.5rem;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.25rem;
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #6c757d;
    }
    .table td {
        vertical-align: middle;
    }
    .progress {
        height: 8px;
        border-radius: 4px;
    }
</style>
@endpush

@endsection
