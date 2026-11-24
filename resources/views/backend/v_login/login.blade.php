{{-- Form LOGIN admin. Memakai layout admin; karena belum login, layout tampil tanpa sidebar. --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="text-center mb-4">
        <i class="bi bi-shop display-5 text-primary"></i>
        <h1 class="h4 mt-2 mb-1">{{ config('app.name') }}</h1>
        <p class="text-muted mb-0">Halaman Admin</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">{{ $judul }}</h2>

            <form action="{{ route('backend.login.proses') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" autocomplete="username"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    {{-- Password TIDAK diisi ulang dengan old() demi keamanan --}}
                    <input type="password" name="password" id="password" autocomplete="current-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="ingat" id="ingat" value="1" class="form-check-input">
                    <label for="ingat" class="form-check-label">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                </button>
            </form>
        </div>
    </div>

    <p class="text-center mt-3"><a href="{{ route('beranda') }}">&larr; Kembali ke website</a></p>
@endsection
