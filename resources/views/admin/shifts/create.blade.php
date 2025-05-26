@extends('admin.shifts.form')

@section('content')
    @parent
    @section('card-title', 'Create New Shift')
    @section('form-action', route('admin.shifts.store'))
    @section('form-method', 'POST')
@endsection
