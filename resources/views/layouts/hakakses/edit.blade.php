@extends('layouts.app')

@section('title', 'Edit Access Rights Data')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Access Rights Data</h1>
        </div>
        <form action="{{ route('hakakses.update', $hakakses->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Access Rights</label>
                    <input type="text" name="role" id="role" class="form-control" value="{{ $hakakses->role }}">
                </div>


                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </section>
</div>
@endsection