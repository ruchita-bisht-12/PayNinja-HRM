@extends('layouts.app')
@section('title', 'Employee Dashboard')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Employee Dashboard</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">Welcome, {{ auth()->user()->name }}!</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Quick Access Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <a href="{{ route('attendance.check-in') }}" class="card card-link">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-clock fa-3x text-primary"></i>
                            </div>
                            <h6 class="card-title mb-0">Check In/Out</h6>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <a href="{{ route('attendance.my-attendance') }}" class="card card-link">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-calendar-check fa-3x text-success"></i>
                            </div>
                            <h6 class="card-title mb-0">My Attendance</h6>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <a href="{{ route('employee.leave-requests.create') }}" class="card card-link">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-calendar-plus fa-3x text-warning"></i>
                            </div>
                            <h6 class="card-title mb-0">Apply Leave</h6>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <a href="{{ route('employee.salary.details') }}" class="card card-link">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-money-bill-wave fa-3x text-info"></i>
                            </div>
                            <h6 class="card-title mb-0">Salary Details</h6>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="row mt-4">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Today's Status</h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-4 text-center">
                                    <i class="fas fa-user-clock fa-3x text-primary"></i>
                                </div>
                                <div class="col-8">
                                    <div class="mb-2">
                                        <strong>Check In Time:</strong>
                                        @if(isset($todayAttendance) && $todayAttendance->check_in)
                                            <span class="ml-2">{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }}</span>
                                        @else
                                            <span class="ml-2 text-muted">Not checked in</span>
                                        @endif
                                    </div>
                                    <div class="mb-2">
                                        <strong>Check Out Time:</strong>
                                        @if(isset($todayAttendance) && $todayAttendance->check_out)
                                            <span class="ml-2">{{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}</span>
                                        @else
                                            <span class="ml-2 text-muted">Not checked out</span>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>Status:</strong>
                                        @if(isset($todayAttendance))
                                            @if($todayAttendance->status === 'Present')
                                                <span class="badge badge-success">Present</span>
                                            @elseif($todayAttendance->status === 'Late')
                                                <span class="badge badge-warning">Late</span>
                                            @elseif($todayAttendance->status === 'Absent')
                                                <span class="badge badge-danger">Absent</span>
                                            @elseif($todayAttendance->status === 'On Leave')
                                                <span class="badge badge-info">On Leave</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $todayAttendance->status }}</span>
                                            @endif
                                            
                                            @if($todayAttendance->check_in && $todayAttendance->check_out)
                                                @php
                                                    $checkIn = \Carbon\Carbon::parse($todayAttendance->check_in);
                                                    $checkOut = \Carbon\Carbon::parse($todayAttendance->check_out);
                                                    $hours = $checkOut->diffInHours($checkIn);
                                                    $minutes = $checkOut->diffInMinutes($checkIn) % 60;
                                                @endphp
                                                <span class="ml-2 text-muted">({{ sprintf('%d:%02d', $hours, $minutes) }} hrs)</span>
                                            @endif
                                        @else
                                            <span class="badge badge-warning">Not Checked In</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Leave Balance</h4>
                        </div>
                        <div class="card-body">
                            <div class="leave-balance-summary mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-calendar-alt text-warning mr-2"></i>
                                        <strong>Total Available:</strong>
                                        <span class="ml-2">{{ $leaveBalance ?? 0 }} Days</span>
                                    </div>
                                    <a href="{{ route('employee.leave-requests.index') }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-history"></i> View History
                                    </a>
                                </div>
                            </div>
                            
                            <div class="leave-types">
                                @foreach($leaveTypes as $leave)
                                <div class="leave-type-item mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="leave-type-badge" style="background-color: {{ $leave['color'] }}"></span>
                                            <span class="leave-type-name">{{ $leave['name'] }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-muted small">
                                                {{ $leave['available_days'] }} of {{ $leave['total_days'] }} days
                                            </span>
                                            <div class="progress mt-1" style="height: 4px;">
                                                @php
                                                    $percentage = $leave['total_days'] > 0 
                                                        ? ($leave['available_days'] / $leave['total_days']) * 100 
                                                        : 0;
                                                @endphp
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: {{ $percentage }}%; background-color: {{ $leave['color'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('style')
<style>
    .card-link {
        text-decoration: none !important;
        color: inherit;
        transition: transform 0.2s;
    }
    .card-link:hover {
        transform: translateY(-5px);
    }
    .badge {
        padding: 0.5em 1em;
        font-size: 85%;
    }
    .fa-3x {
        font-size: 3em;
    }
</style>
@endpush
