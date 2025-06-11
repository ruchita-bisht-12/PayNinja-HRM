@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Attendance Adjustment</h1>
        <a href="{{ route('admin.attendance-adjustments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.attendance-adjustments.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="employee_id">Employee *</label>
                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ old('date', now()->format('Y-m-d')) }}" required>
                    @error('date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="type">Type *</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="half_day" {{ old('type') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                        <option value="reimbursement" {{ old('type') == 'reimbursement' ? 'selected' : '' }}>Reimbursement</option>
                    </select>
                    @error('type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group" id="amount-group" style="display: none;">
                    <label for="amount">Amount *</label>
                    <input type="number" name="amount" id="amount" class="form-control" 
                           value="{{ old('amount', 0) }}" step="0.01" min="0">
                    @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2({
            theme: 'bootstrap4'
        });
        
        // Toggle amount field based on type
        function toggleAmountField() {
            if ($('#type').val() === 'reimbursement') {
                $('#amount-group').show();
                $('#amount').prop('required', true);
            } else {
                $('#amount-group').hide();
                $('#amount').prop('required', false);
            }
        }
        
        // Initial check
        toggleAmountField();
        
        // On type change
        $('#type').on('change', function() {
            toggleAmountField();
        });
    });
</script>
@endpush
@endsection
