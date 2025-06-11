@extends('layouts.app')

@section('title', 'Create New Beneficiary Badge')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-3">Create New Beneficiary Badge</h4>
                    <p class="mb-0">Define a new allowance or deduction badge for payroll.</p>
                </div>
                <a href="{{ route('admin.payroll.beneficiary-badges.index') }}" class="btn btn-secondary add-list"><i class="fa-solid fa-arrow-left me-2"></i>Back to List</a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.payroll.beneficiary-badges.store') }}" method="POST">
                        @csrf
                        
                        @include('admin.payroll.beneficiary-badges._form')

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Create Badge</button>
                            <a href="{{ route('admin.payroll.beneficiary-badges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
