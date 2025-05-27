@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="section-body">
            <p>Welcome, Admin! Here you can manage your department, attendance, and reports.</p>
            {{-- Add admin-specific dashboard widgets here --}}
        </div>
    </section>
</div>
@endsection
