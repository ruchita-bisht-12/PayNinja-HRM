<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Badge Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $beneficiaryBadge->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="" disabled {{ old('type', $beneficiaryBadge->type ?? '') == '' ? 'selected' : '' }}>Select type</option>
                <option value="allowance" {{ old('type', $beneficiaryBadge->type ?? '') == 'allowance' ? 'selected' : '' }}>Allowance</option>
                <option value="deduction" {{ old('type', $beneficiaryBadge->type ?? '') == 'deduction' ? 'selected' : '' }}>Deduction</option>
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="calculation_type" class="form-label">Calculation Type <span class="text-danger">*</span></label>
            <select name="calculation_type" id="calculation_type" class="form-select @error('calculation_type') is-invalid @enderror" required>
                <option value="" disabled {{ old('calculation_type', $beneficiaryBadge->calculation_type ?? '') == '' ? 'selected' : '' }}>Select calculation type</option>
                <option value="flat" {{ old('calculation_type', $beneficiaryBadge->calculation_type ?? '') == 'flat' ? 'selected' : '' }}>Flat Amount</option>
                <option value="percentage" {{ old('calculation_type', $beneficiaryBadge->calculation_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
            </select>
            @error('calculation_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
            <input type="number" name="value" id="value" class="form-control @error('value') is-invalid @enderror" 
                   value="{{ old('value', $beneficiaryBadge->value ?? '') }}" required step="0.01" min="0">
            @error('value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3" id="based_on_group" style="display: {{ old('calculation_type', $beneficiaryBadge->calculation_type ?? 'flat') == 'percentage' ? 'block' : 'none' }};">
            <label for="based_on" class="form-label">Based On (e.g., basic_salary, gross_salary) <span id="based_on_required_asterisk" class="text-danger" style="display: {{ old('calculation_type', $beneficiaryBadge->calculation_type ?? 'flat') == 'percentage' ? 'inline' : 'none' }};">*</span></label>
            <input type="text" name="based_on" id="based_on" class="form-control @error('based_on') is-invalid @enderror" 
                   value="{{ old('based_on', $beneficiaryBadge->based_on ?? '') }}">
            @error('based_on')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $beneficiaryBadge->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $beneficiaryBadge->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                Active
            </label>
        </div>
        
        <div class="form-check mb-3">
            <input type="hidden" name="is_company_wide" value="0">
            <input class="form-check-input" type="checkbox" name="is_company_wide" id="is_company_wide" value="1" 
                   {{ old('is_company_wide', $beneficiaryBadge->is_company_wide ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_company_wide">
                Company-wide Badge (Applies to all employees)
            </label>
            <div class="form-text text-muted">
                When enabled, this badge will be automatically applied to all current and future employees.
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calculationTypeSelect = document.getElementById('calculation_type');
        const basedOnGroup = document.getElementById('based_on_group');
        const basedOnInput = document.getElementById('based_on');
        const basedOnRequiredAsterisk = document.getElementById('based_on_required_asterisk');

        function toggleBasedOnGroup() {
            if (calculationTypeSelect.value === 'percentage') {
                basedOnGroup.style.display = 'block';
                basedOnInput.setAttribute('required', 'required');
                basedOnRequiredAsterisk.style.display = 'inline';
            } else {
                basedOnGroup.style.display = 'none';
                basedOnInput.removeAttribute('required');
                basedOnRequiredAsterisk.style.display = 'none';
                // basedOnInput.value = ''; // Optionally clear value when hidden
            }
        }

        if (calculationTypeSelect) {
            calculationTypeSelect.addEventListener('change', toggleBasedOnGroup);
            // Initialize on page load
            toggleBasedOnGroup();
        }
    });
</script>
@endPushOnce
