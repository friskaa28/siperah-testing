@extends('layouts.app')

@section('title', 'Lupa Password - SIPERAH')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('img/logo-siperah.png') }}" alt="SIPERAH Logo" style="height: 80px; object-fit: contain;">
        </div>
        <h3 style="text-align: center; margin-bottom: 24px;">Reset Password</h3>

        @if (session('status'))
            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Kirim Link Reset Password
            </button>
            
            <div style="text-align: center; margin-top: 16px;">
                <a href="{{ route('login') }}" class="text-decoration-none">Kembali ke Login</a>
            </div>
        </form>
    </div>
</div>
@endsection
