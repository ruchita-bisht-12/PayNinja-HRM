@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Employee Profile</h2>
        <p>Name: {{ $employee->name }}</p>
        <p>Email: {{ $employee->email }}</p>
        <p>Joined: {{ $employee->created_at->format('Y-m-d') }}</p>
        <!-- Add other relevant employee information -->
    </div>
@endsection
