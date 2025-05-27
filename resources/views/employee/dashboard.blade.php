@extends('layouts.app')
@section('title', 'Employee Dashboard')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Employee Dashboard</h1>
        </div>
        <div class="section-body">
            <p>Welcome, Employee! Here you can view your attendance, leaves, and personal info.</p>
            {{-- Add employee-specific dashboard widgets here --}}
        </div>
    </section>
</div>
@endsection
