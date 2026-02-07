@extends('layouts.app')

@section('title', 'Reset Password - SIPERAH')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('img/logo-siperah.png') }}" alt="SIPERAH Logo" style="height: 80px; object-fit: contain;">
        </div>
        <h3 style="text-align: center; margin-bottom: 24px;">Buat Password Baru</h3>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password-confirm">Konfirmasi Password Baru</label>
                <input type="password" id="password-confirm" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection
