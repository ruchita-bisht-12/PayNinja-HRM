@extends('layouts.app')
@section('title', 'User Dashboard')
@section('content')
<div class="main-content main-contant-01">
    <section class="section">
        <div class="section-header">
            <h1>User Dashboard</h1>
        </div>
        <div class="section-body">
            <p>Welcome, {{ $loggedInUser->name }}! You don't have any Company or Organization, CONTACT US if you want to register your company.</p>
        </div>
    </section>
</div>
@endsection
