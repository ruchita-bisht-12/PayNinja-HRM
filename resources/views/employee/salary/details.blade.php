@extends('layouts.app')

@section('title', 'My Salary Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Salary Details</h5>
                </div>
                <div class="card-body">
                    @if($currentSalary)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Current Salary Structure</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered mb-0">
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
                                        <p class="text-muted mt-2 mb-0">
                                            <small>Effective from: {{ \Carbon\Carbon::parse($currentSalary->effective_from)->format('d M, Y') }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">This Month's Summary</h6>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-3">
                                            <h6 class="text-muted">Current Month: {{ now()->format('F Y') }}</h6>
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small>Month Progress</small>
                                                <small>{{ now()->day }}/{{ now()->daysInMonth }} days</small>
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Salary Breakdown -->
                       
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
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th class="text-right">Basic Salary</th>
                                                <th class="text-right">HRA</th>
                                                <th class="text-right">DA</th>
                                                <th class="text-right">Allowances</th>
                                                <th class="text-right">Gross Salary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salaryHistory as $salary)
                                                <tr>
                                                    <td>{{ $salary->start_date ? \Carbon\Carbon::parse($salary->start_date)->format('d M, Y') : '-' }}</td>
                                                    <td>{{ $salary->end_date ? \Carbon\Carbon::parse($salary->end_date)->format('d M, Y') : 'Present' }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->basic_salary, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->hra, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->da, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->other_allowances, 2) }}</td>
                                                    <td class="text-right">₹{{ number_format($salary->gross_salary, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No salary history available</td>
                                                </tr>
                                            @endforelse
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
