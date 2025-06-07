@extends('layouts.app')
@section('title', 'Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
<style>
    /* Stats Cards */
    .card-statistic-1 {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        border: none;
        overflow: hidden;
        background: #fff;
    }
    .card-statistic-1:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .card-icon {
        font-size: 2.2rem;
        opacity: 0.9;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        width: 70px;
        height: 70px;
        margin: 20px auto 15px;
        color: white;
    }
    .card-wrap {
        padding: 15px 20px;
        text-align: center;
    }
    .card-header h4 {
        font-size: 1rem;
        color: #6c757d;
        margin: 0 0 5px 0;
        font-weight: 500;
    }
    .card-body {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c3e50;
        padding: 0 0 15px 0;
        line-height: 1.2;
    }
    
    /* Quick Action Cards */
    .action-card {
        display: block;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
        border: 1px solid #eef1f6;
        color: #2c3e50;
        text-decoration: none;
        margin-bottom: 1.5rem;
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        border-color: #e0e6ed;
    }
    .action-card .card-body {
        padding: 2rem 1.5rem;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .action-card .card-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1.2rem;
        font-size: 1.8rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-card h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0.5rem 0 0.25rem;
        color: #2c3e50;
    }
    .action-card .coming-soon {
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 400;
        display: block;
        margin-top: 0.25rem;
    }
    
    /* Section Titles */
    .section-title {
        color: #2c3e50;
        font-weight: 600;
        margin: 2.5rem 0 1.5rem;
        position: relative;
        padding-bottom: 12px;
        font-size: 1.25rem;
    }
    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 4px;
        background: linear-gradient(45deg, #6777ef, #9c27b0);
        border-radius: 4px;
    }
    
    /* Charts */
    .chart-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.04);
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #eef1f6;
    }
    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #2c3e50;
        display: flex;
        align-items: center;
    }
    .chart-title i {
        margin-right: 10px;
        color: #6777ef;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-icon {
            width: 60px;
            height: 60px;
            font-size: 1.8rem;
        }
        .card-body {
            font-size: 1.5rem;
        }
        .action-card .card-body {
            padding: 1.5rem 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Admin Dashboard</h1>
        </div>

        <!-- Stats Row -->
        <div class="row">
            <!-- Total Employees -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background: linear-gradient(135deg, #6777ef 0%, #9c27b0 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Employees</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalEmployees ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Departments</h4>
                        </div>
                        <div class="card-body">
                            {{ $departmentCount ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Attendance -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Today's Attendance</h4>
                        </div>
                        <div class="card-body">
                            {{ $todayAttendanceCount ?? 0 }}/{{ $totalEmployees ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon" style="background: linear-gradient(135deg, #26c6da 0%, #00acc1 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending Requests</h4>
                        </div>
                        <div class="card-body">
                            {{ $pendingRequests ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Employee Distribution Chart -->
            <div class="col-lg-8">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie"></i> Employee Distribution by Department
                    </div>
                    <div id="employeeDistributionChart" style="min-height: 300px;"></div>
                </div>
            </div>
            
            <!-- Attendance Overview -->
            <div class="col-lg-4">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-bar"></i> Attendance Overview
                    </div>
                    <div id="attendanceChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <h4 class="section-title">Quick Actions</h4>
            </div>
            
            <!-- Employee Management -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ route('company.employees.index', ['companyId' => auth()->user()->company_id]) }}" class="action-card">
                    <div class="card-body">
                        <div class="card-icon" style="background: linear-gradient(135deg, #6777ef 0%, #9c27b0 100%);">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h5>Manage Employees</h5>
                        <span class="text-muted small">Add, edit, or remove employees</span>
                    </div>
                </a>
            </div>

            <!-- Attendance -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ route('attendance.dashboard') }}" class="action-card">
                    <div class="card-body">
                        <div class="card-icon" style="background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h5>Attendance</h5>
                        <span class="text-muted small">View and manage attendance</span>
                    </div>
                </a>
            </div>

            <!-- Leave Management -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ route('company.leave-requests.index') }}" class="action-card">
                    <div class="card-body">
                        <div class="card-icon" style="background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);">
                            <i class="fas fa-calendar-minus"></i>
                        </div>
                        <h5>Leave Management</h5>
                        <span class="text-muted small">Approve or reject leave requests</span>
                    </div>
                </a>
            </div>

            <!-- Reports -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="#" class="action-card" onclick="alert('Reports feature coming soon!'); return false;">
                    <div class="card-body">
                        <div class="card-icon" style="background: linear-gradient(135deg, #26c6da 0%, #00acc1 100%);">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5>Reports</h5>
                        <span class="coming-soon">Coming Soon</span>
                        <span class="text-muted small">Generate detailed reports</span>
                    </div>
                </a>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Employee Distribution Chart
        var employeeOptions = {
            series: [{
                name: 'Employees',
                data: @json($departmentData['counts'] ?? [])
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: false,
                        reset: true
                    }
                },
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            xaxis: {
                categories: @json($departmentData['names'] ?? []),
            },
            colors: ['#6777ef', '#9c27b0', '#66bb6a', '#ffa726', '#26c6da', '#ff5252', '#ab47bc', '#7e57c2', '#5c6bc0', '#42a5f5'],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' employees';
                    }
                }
            }
        };

        var employeeChart = new ApexCharts(document.querySelector("#employeeDistributionChart"), employeeOptions);
        employeeChart.render();

        // Attendance Overview Chart
        var presentCount = {{ $todayAttendanceCount ?? 0 }};
        var onLeaveCount = {{ $onLeaveCount ?? 0 }};
        var totalEmployees = {{ $totalEmployees ?? 1 }}; // Prevent division by zero
        var accountedFor = presentCount + onLeaveCount;
        var notMarkedCount = Math.max(0, totalEmployees - accountedFor);
        var absentCount = {{ $absentCount ?? 0 }};
        
        // Log the values for debugging
        console.log('Present:', presentCount, 'On Leave:', onLeaveCount, 'Not Marked:', notMarkedCount, 'Absent:', absentCount, 'Total:', totalEmployees);
        
        // Donut chart configuration with four categories
        var attendanceOptions = {
            series: [presentCount, onLeaveCount, notMarkedCount, absentCount],
            chart: {
                type: 'donut',
                height: 320,
                toolbar: {
                    show: true,
                    tools: {
                        download: true
                    }
                }
            },
            labels: ['Present', 'On Leave', 'Not Marked', 'Absent'],
            colors: ['#66bb6a', '#ffa726', '#6c757d', '#ff5252'],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            },
                            value: {
                                fontSize: '20px',
                                fontWeight: 'bold',
                                color: '#2c3e50'
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, { seriesIndex, w }) {
                    // Show both count and percentage on the chart
                    const count = w.config.series[seriesIndex];
                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    const percentage = total > 0 ? Math.round((count / total) * 100) : 0;
                    return `${count} (${percentage}%)`;
                },
                style: {
                    fontSize: '12px',
                    fontWeight: 'bold',
                    colors: ['#fff']
                },
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 1,
                    opacity: 0.8
                }
            },
            tooltip: {
                y: {
                    formatter: function(value, { seriesIndex, w }) {
                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${w.config.labels[seriesIndex]}: ${value} (${percentage}%)`;
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 280
                    },
                    legend: {
                        position: 'bottom',
                        fontSize: '12px'
                    }
                }
            }]
        };
        
        // Add noData configuration if no employees
        if (totalEmployees === 0) {
            attendanceOptions.noData = {
                text: 'No attendance data available',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    color: '#6c757d',
                    fontSize: '14px',
                    fontFamily: 'inherit'
                }
            };
        }

        var attendanceChart = new ApexCharts(document.querySelector("#attendanceChart"), attendanceOptions);
        attendanceChart.render();

        // Handle window resize
        window.addEventListener('resize', function() {
            employeeChart.updateOptions({
                chart: {
                    width: '100%'
                }
            });
        });
    });
</script>
@endpush

@endsection
