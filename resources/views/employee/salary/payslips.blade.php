@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">My Payslips</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">My Payslips</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Available Payslips</h3>
                            <div class="card-tools">
                                <a href="{{ route('employee.salary.details') }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Salary Details
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach($years as $year => $months)
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ $year }}</h4>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($months as $month)
                                                @php
                                                    $monthYear = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                                                    $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F');
                                                @endphp
                                                <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">{{ $monthName }} {{ $year }}</h5>
                                                            <div class="btn-group">
                                                                <a href="{{ route('employee.salary.payslip.view', ['employee' => $employee->id, 'monthYear' => $monthYear]) }}" 
                                                                   class="btn btn-info btn-sm" target="_blank">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                                <a href="{{ route('employee.salary.payslip.download', ['employee' => $employee->id, 'monthYear' => $monthYear]) }}" 
                                                                   class="btn btn-success btn-sm">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .card-outline {
        border-top: 3px solid #007bff !important;
        margin-bottom: 20px;
    }
    .card-title {
        margin-bottom: 0.5rem;
    }
    .btn-group {
        display: flex;
        justify-content: center;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@endsection
