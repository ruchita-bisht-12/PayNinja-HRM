@extends('layouts.app')

@section('title', isset($admin) ? 'Edit Company Admin' : 'Assign Company Admin')

@section('content')
<div class="main-content-01">
    <section class="section">
        <div class="section-header">
            <h1>{{ isset($admin) ? 'Edit Company Admin' : 'Assign Company Admin' }}</h1>
        </div>
        <div class="section-body">
            <form action="{{ isset($admin) ? route('superadmin.assign-company-admin.update', $admin->id) : route('superadmin.assign-company-admin.store') }}" method="POST" id="assignCompanyAdminForm">
                @csrf
                @if(isset($admin))
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="user_id">Select User</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', isset($admin) ? $admin->user_id : null) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="company_id">Select Company</label>
                    <select name="company_id" id="company_id" class="form-control" required @if(isset($admin)) readonly disabled @endif>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $company)
                            @php
                                $isAssignedCompany = \App\Models\Employee::where('company_id', $company->id)
                                    ->whereHas('user', function($q){ $q->where('role', 'company_admin'); })
                                    ->exists();
                                $isCurrentCompany = isset($admin) && old('company_id', $admin->company_id) == $company->id;
                            @endphp
                            <option value="{{ $company->id }}" {{ $isCurrentCompany ? 'selected' : '' }} @if($isAssignedCompany && !$isCurrentCompany) disabled @endif>
                                {{ $company->name }} @if($isAssignedCompany && !$isCurrentCompany) (Assigned) @endif
                            </option>
                        @endforeach
                    </select>
                    @if(isset($admin))
                        <input type="hidden" name="company_id" value="{{ $admin->company_id }}">
                    @endif
                    @error('company_id')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" maxlength="10" value="{{ old('phone', $admin->phone ?? '') }}">
                    @error('phone')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob', isset($admin) && $admin->dob ? \Carbon\Carbon::parse($admin->dob)->format('Y-m-d') : '') }}">
                    @error('dob')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="">-- Select Gender --</option>
                        <option value="male" {{ old('gender', $admin->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $admin->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $admin->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact</label>
                    <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" maxlength="10" value="{{ old('emergency_contact', $admin->emergency_contact ?? '') }}">
                    @error('emergency_contact')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <input type="hidden" name="joining_date" id="joining_date" value="{{ old('joining_date', isset($admin) ? $admin->joining_date : date('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $admin->address ?? '') }}</textarea>
                    @error('address')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ isset($admin) ? 'Update' : 'Assign' }}</button>
            </form>
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userSelect = document.getElementById('user_id');
        var companySelect = document.getElementById('company_id');
        var form = document.getElementById('assignCompanyAdminForm');
        var oldValues = {};
        if (userSelect && form) {
            userSelect.addEventListener('focus', function() {
                Array.from(form.elements).forEach(function(el) {
                    if (el.name && el.name !== 'company_id' && el.name !== 'user_id' && el.type !== 'hidden' && el.type !== 'submit') {
                        oldValues[el.name] = el.value;
                    }
                });
            });
            userSelect.addEventListener('change', function() {
                var companyValue = companySelect ? companySelect.value : '';
                Array.from(form.elements).forEach(function(el) {
                    if (el.name && el.name !== 'company_id' && el.name !== 'user_id' && el.type !== 'hidden' && el.type !== 'submit') {
                        el.value = '';
                    }
                });
                if (companySelect) companySelect.value = companyValue;
            });
        }
    });
</script>
@endsection
