@extends('layouts.app')

@section('title', 'Employee Payslips')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Payslips</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form action="{{ route('company-admin.payslips.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Search</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Name, Email, or Employee ID" value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="department_id">Department</label>
                                    <select name="department_id" id="department_id" class="form-control select2">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_range">Date Range</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control float-right" id="date_range" name="date_range" 
                                               value="{{ request('date_range') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                                <div class="form-group ml-2">
                                    <a href="{{ route('company-admin.payslips.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Export Button -->
                    <div class="mb-3">
                        <button class="btn btn-success export-btn">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>

                    <!-- Payslips Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Basic Salary</th>
                                    <th>Gross Salary</th>
                                    <th>Net Salary</th>
                                    <th>Effective From</th>
                                    <th>Effective To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payslips as $payslip)
                                    <tr>
                                        <td>{{ $payslip['employee_name'] }}</td>
                                        <td>{{ $payslip['employee_number'] }}</td>
                                        <td>{{ $payslip['department'] }}</td>
                                        <td>{{ $payslip['designation'] }}</td>
                                        <td class="text-right">{{ $payslip['formatted_basic_salary'] }}</td>
                                        <td class="text-right">{{ $payslip['formatted_gross_salary'] }}</td>
                                        <td class="text-right">{{ $payslip['formatted_net_salary'] }}</td>
                                        <td>{{ $payslip['formatted_effective_from'] }}</td>
                                        <td>{{ $payslip['formatted_effective_to'] }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ $payslip['payslip_url'] }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="View Payslip" 
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ $payslip['download_url'] }}" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Download Payslip">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No payslips found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $salaries->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Payslips</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportForm" action="#" method="GET">
                <div class="modal-body">
                    <input type="hidden" name="export_search" value="{{ request('search') }}">
                    <input type="hidden" name="export_department_id" value="{{ request('department_id') }}">
                    <input type="hidden" name="export_date_range" value="{{ request('date_range') }}">
                    
                    <div class="form-group">
                        <label for="export_format">Export Format</label>
                        <select name="export_format" id="export_format" class="form-control">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Date Range Picker -->
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
</style>
@endpush

@push('scripts')
<!-- Date Range Picker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize date range picker
        $('#date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear'
            },
            autoUpdateInput: false,
            showDropdowns: true,
            minYear: 2020,
            maxYear: parseInt(moment().format('YYYY'), 10) + 1,
            ranges: {
                'Today': [moment(), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            }
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an option',
            allowClear: true
        });

        // Handle export button click
        $('.export-btn').click(function(e) {
            e.preventDefault();
            $('#exportModal').modal('show');
        });

        // Handle export form submission
        $('#exportForm').on('submit', function(e) {
            e.preventDefault();
            const format = $('#export_format').val();
            const url = '{{ route("company-admin.payslips.export") }}' + '?format=' + format +
                       '&search=' + encodeURIComponent($('input[name="export_search"]').val()) +
                       '&department_id=' + $('input[name="export_department_id"]').val() +
                       '&date_range=' + encodeURIComponent($('input[name="export_date_range"]').val());
            
            window.location.href = url;
            $('#exportModal').modal('hide');
        });
    });
</script>
@endpush
