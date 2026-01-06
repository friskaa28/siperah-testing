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
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border);">
            <p style="text-align: center; margin-bottom: 12px;">
                Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Daftar disini</a>
            </p>
            <p style="text-align: center; color: var(--text-light); font-size: 12px;">
                Contoh Login:<br>
                admin@siperah.com / password123<br>
                pengelola@siperah.com / password123<br>
                peternak1@siperah.com / password123
            </p>
        </div>
    </div>
</div>
@endsection