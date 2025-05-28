@extends('layouts.app')

@section('title', 'Edit Salary for ' . $employee->name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Salary for {{ $employee->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.salary.update', $salary->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="basic_salary" class="form-label">Basic Salary <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('basic_salary') is-invalid @enderror" 
                                   id="basic_salary" name="basic_salary" 
                                   value="{{ old('basic_salary', $salary->basic_salary) }}" required>
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="hra" class="form-label">HRA <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('hra') is-invalid @enderror" 
                                   id="hra" name="hra" 
                                   value="{{ old('hra', $salary->hra) }}" required>
                            @error('hra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="da" class="form-label">DA <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('da') is-invalid @enderror" 
                                   id="da" name="da" 
                                   value="{{ old('da', $salary->da) }}" required>
                            @error('da')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="other_allowances" class="form-label">Other Allowances</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control" 
                                   id="other_allowances" name="other_allowances" 
                                   value="{{ old('other_allowances', $salary->other_allowances) }}">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="other_deductions" class="form-label">Other Deductions</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control" 
                                   id="other_deductions" name="other_deductions" 
                                   value="{{ old('other_deductions', $salary->other_deductions) }}">
                        </div>
                        <small class="text-muted">Additional deductions not covered by PF, ESI, etc.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="effective_from" class="form-label">Effective From <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                               id="effective_from" name="effective_from" 
                               value="{{ old('effective_from', is_string($salary->effective_from) ? $salary->effective_from : ($salary->effective_from ? $salary->effective_from->format('Y-m-d') : '')) }}" required>
                        @error('effective_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Salary Period</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $salary->start_date ? (is_string($salary->start_date) ? $salary->start_date : $salary->start_date->format('Y-m-d')) : '') }}"
                                           onchange="updateEndDateMin()" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $salary->end_date ? (is_string($salary->end_date) ? $salary->end_date : $salary->end_date->format('Y-m-d')) : '') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Leave empty if this is the current salary</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Duration</label>
                                    <div class="form-control-plaintext" id="duration-display">
                                        @if($salary->start_date && $salary->end_date)
                                            {{ \Carbon\Carbon::parse($salary->start_date)->diffInDays(\Carbon\Carbon::parse($salary->end_date)) + 1 }} days
                                        @elseif($salary->start_date)
                                            Ongoing since {{ \Carbon\Carbon::parse($salary->start_date)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="leaves_deduction" class="form-label">Leaves Deduction</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" class="form-control @error('leaves_deduction') is-invalid @enderror" 
                                   id="leaves_deduction" name="leaves_deduction" 
                                   value="{{ old('leaves_deduction', $salary->leaves_deduction) }}">
                            @error('leaves_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Amount to be deducted for unpaid leaves</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="paid_at" class="form-label">Paid At</label>
                        <input type="datetime-local" class="form-control @error('paid_at') is-invalid @enderror" 
                               id="paid_at" name="paid_at" 
                               value="{{ old('paid_at', $salary->paid_at ? (is_string($salary->paid_at) ? \Carbon\Carbon::parse($salary->paid_at)->format('Y-m-d\TH:i') : $salary->paid_at->format('Y-m-d\TH:i')) : '') }}">
                        @error('paid_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $salary->notes) }}</textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.salary.show', $employee->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Salary
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Enable form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        // Validate dates before form submission
                        const startDate = document.getElementById('start_date').value;
                        const endDate = document.getElementById('end_date').value;
                        
                        if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
                            event.preventDefault();
                            event.stopPropagation();
                            alert('End date must be after start date');
                            return false;
                        }
                        
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();

        // Update end date minimum based on start date
        function updateEndDateMin() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            
            if (startDate.value) {
                endDate.min = startDate.value;
                // If end date is before start date, clear it
                if (endDate.value && new Date(endDate.value) < new Date(startDate.value)) {
                    endDate.value = '';
                }
            }
            updateDuration();
        }

        // Calculate and display duration between dates
        function updateDuration() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const durationDisplay = document.getElementById('duration-display');
            
            if (!startDate) {
                durationDisplay.textContent = '-';
                return;
            }
            
            const start = new Date(startDate);
            
            if (endDate) {
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end dates
                durationDisplay.textContent = `${diffDays} days (${start.toLocaleDateString()} - ${end.toLocaleDateString()})`;
            } else {
                durationDisplay.textContent = `Ongoing since ${start.toLocaleDateString()}`;
            }
        }

        // Calculate and display salary totals
        function calculateTotals() {
            const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
            const hra = parseFloat(document.getElementById('hra').value) || 0;
            const da = parseFloat(document.getElementById('da').value) || 0;
            const otherAllowances = parseFloat(document.getElementById('other_allowances').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;

            const gross = basic + hra + da + otherAllowances;
            const net = gross - deductions;

            // Update the display elements if they exist
            const grossDisplay = document.getElementById('gross-display');
            const netDisplay = document.getElementById('net-display');
            
            if (grossDisplay) grossDisplay.textContent = gross.toFixed(2);
            if (netDisplay) netDisplay.textContent = net.toFixed(2);
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            updateEndDateMin();
            
            // Add input event listeners for calculations
            ['basic_salary', 'hra', 'da', 'other_allowances', 'deductions'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', calculateTotals);
                }
            });
            
            // Add date change listeners
            ['start_date', 'end_date'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', updateDuration);
                }
            });
            
            // Initial calculations
            calculateTotals();
            updateDuration();
        });
    </script>
    @endpush
@endsection
