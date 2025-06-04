@extends('layouts.app')

@section('title', 'Login')

@section('content')
<style>
    :root {
        --primary-color: #2196F3;
        --primary-dark: #1976D2;
        --text-color: #2c3345;
        --text-muted: #6c757d;
        --border-color: #edf2f7;
        --input-bg: #f8fafc;
        --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }
    .login-page {
        min-height: 100vh;
        background: #f0f5fa url('{{ asset('img/login-bg.jpg') }}') center/cover no-repeat fixed;
        background-blend-mode: overlay;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        margin: 0;
        background-attachment: fixed;
    }
    .login-page::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.97) 0%, rgba(240, 245, 250, 0.97) 100%);
        z-index: 1;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        width: 100%;
        max-width: 420px;
        padding: 0;
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .login-header {
        background: #fff;
        padding: 2.5rem 2rem;
        text-align: center;
        border-bottom: 1px solid var(--border-color);
        position: relative;
    }
    .login-header::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 25%;
        right: 25%;
        height: 1px;
        background: var(--primary-color);
        opacity: 0.5;
    }
    .login-header img {
        height: 80px;
        margin-bottom: 1.5rem;
        width: auto;
        object-fit: contain;
    }
    .login-header h4 {
        color: #6777ef;
        font-weight: 600;
        margin: 0;
    }
    .login-body {
        padding: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-weight: 500;
    }
    .input-group {
        position: relative;
        display: flex;
        width: 100%;
    }
    .input-group-text {
        background-color: var(--input-bg);
        border: 1px solid var(--border-color);
        border-right: none;
        border-radius: 12px 0 0 12px;
        color: var(--primary-color);
        padding: 0.75rem 1rem;
    }
    .form-control {
        border-radius: 0 12px 12px 0;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        font-size: 0.95rem;
        background-color: var(--input-bg);
        color: var(--text-color);
    }
    .input-group .form-control {
        flex: 1 1 auto;
    }
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.1);
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    .btn-primary {
        background: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: 12px;
        padding: 0.85rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }
    .btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(33, 150, 243, 0.15);
    }
    .btn-primary:hover::before {
        transform: translateX(100%);
    }
    .btn-primary:active {
        transform: translateY(0);
    }
    .form-check.remember-me {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-muted);
    }
    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        background-color: var(--input-bg);
    }
    .form-check-input:checked::before {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 0.8rem;
    }
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .form-check-label {
        cursor: pointer;
        user-select: none;
        font-size: 0.95rem;
    }
    .forgot-password {
        color: var(--primary-color);
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .forgot-password:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }
    .text-primary {
        color: var(--primary-color) !important;
        text-decoration: none;
    }
    .text-primary:hover {
        color: var(--primary-dark) !important;
        text-decoration: underline;
    }
    .text-muted {
        color: var(--text-muted) !important;
        font-size: 0.95rem;
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('img/logo.png') }}" alt="PayNinja HRM" style="max-width: 200px;">
            <h4>Welcome Back!</h4>
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf
                <div class="form-group mb-4">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                        <div class="form-group mb-4">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" name="password" required autocomplete="current-password">
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block mt-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check remember-me">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt me-2"></i> {{ __('Sign In') }}
                </button>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary">Register</a></p>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
