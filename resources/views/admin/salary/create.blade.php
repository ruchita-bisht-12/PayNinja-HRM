@extends('layouts.app')

@section('title', 'Create Salary Record')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="section-header">
    <h1>Create Salary Record</h1>
    
</div>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Salary Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.salary.store', $employee->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Employee</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control" value="{{ $employee->name }} ({{ $employee->employee_id }})" readonly>
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Effective From</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                                       name="effective_from" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required>
                                @error('effective_from')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Salary Period</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                   name="start_date" value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                   name="end_date" value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Leaves Deduction</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('leaves_deduction') is-invalid @enderror" 
                                           name="leaves_deduction" value="{{ old('leaves_deduction', '0.00') }}">
                                </div>
                                <small class="form-text text-muted">Amount to be deducted for unpaid leaves</small>
                                @error('leaves_deduction')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Basic Salary</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                        ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('basic_salary') is-invalid @enderror" 
                                           name="basic_salary" value="{{ old('basic_salary', $currentSalary->basic_salary ?? '') }}" required>
                                    @error('basic_salary')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Allowances Section -->
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">HRA (House Rent Allowance)</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                        ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('hra') is-invalid @enderror" 
                                           name="hra" value="{{ old('hra', $currentSalary->hra ?? '0') }}" required>
                                </div>
                                @error('hra')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">DA (Dearness Allowance)</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                                ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('da') is-invalid @enderror" 
                                           name="da" value="{{ old('da', $currentSalary->da ?? '0') }}" required>
                                </div>
                                @error('da')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Other Allowances</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('other_allowances') is-invalid @enderror" 
                                           name="other_allowances" value="{{ old('other_allowances', $currentSalary->other_allowances ?? '0') }}" required>
                                </div>
                                @error('other_allowances')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Deductions Section -->
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">PF Deduction</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('pf_deduction') is-invalid @enderror" 
                                           name="pf_deduction" value="{{ old('pf_deduction', $currentSalary->pf_deduction ?? '0') }}" required>
                                </div>
                                @error('pf_deduction')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">ESI Deduction</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('esi_deduction') is-invalid @enderror" 
                                           name="esi_deduction" value="{{ old('esi_deduction', $currentSalary->esi_deduction ?? '0') }}" required>
                                </div>
                                @error('esi_deduction')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">TDS Deduction</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('tds_deduction') is-invalid @enderror" 
                                           name="tds_deduction" value="{{ old('tds_deduction', $currentSalary->tds_deduction ?? '0') }}" required>
                                </div>
                                @error('tds_deduction')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Professional Tax</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('professional_tax') is-invalid @enderror" 
                                           name="professional_tax" value="{{ old('professional_tax', $currentSalary->professional_tax ?? '0') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Loan Deductions</label>
                            <div class="col-sm-12 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            ₹
                                        </div>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('loan_deductions') is-invalid @enderror" 
                                           name="loan_deductions" value="{{ old('loan_deductions', $currentSalary->loan_deductions ?? '0') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Account Number</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       name="account_number" value="{{ old('account_number', $currentSalary->account_number ?? '') }}">
                                @error('account_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Bank Name</label>
                            <div class="col-sm-12 col-md-7">
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       name="bank_name" value="{{ old('bank_name', $currentSalary->bank_name ?? '') }}">
                                @error('bank_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Notes</label>
                            <div class="col-sm-12 col-md-7">
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          name="notes" rows="3">{{ old('notes', $currentSalary->notes ?? '') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Is Current</label>
                            <div class="col-sm-12 col-md-7">
                                <label class="custom-switch mt-2">
                                    <input type="checkbox" name="is_current" class="custom-switch-input" value="1" 
                                           {{ old('is_current', true) ? 'checked' : '' }}>
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Mark as current salary</span>
                                </label>
                                <small class="form-text text-muted">
                                    If checked, this will be marked as the current active salary for the employee.
                                </small>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <div class="col-md-7 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Salary Record
                                </button>
                                <a href="{{ route('admin.salary.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add any client-side validation or calculations here
    });
</script>
@endpush
