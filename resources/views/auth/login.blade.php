@extends('layouts.app')

@section('title', 'Login - SIPERAH')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <img src="{{ asset('img/logo-siperah.png') }}" alt="SIPERAH Logo" style="height: 120px; object-fit: contain;">
        </div>
        <h2 style="text-align: center; margin-bottom: 24px; color: black; font-weight: 400;">Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label class="form-label" for="email">Email / Nomor HP</label>
                <input type="text" id="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label" for="password">Password</label>
                    <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none">Lupa Password?</a>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" required style="border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <span class="input-group-text" style="background: white; border-left: none; cursor: pointer; border-top-left-radius: 0; border-bottom-left-radius: 0;" onclick="togglePassword('password', 'toggleIcon')">
                        <i class="fas fa-eye" id="toggleIcon" style="color: var(--text-light);"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border);">
            <p style="text-align: center; margin-bottom: 12px;">
                Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Daftar disini</a>
            </p>
  
        </div>
    </div>
</div>
@endsection

<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>