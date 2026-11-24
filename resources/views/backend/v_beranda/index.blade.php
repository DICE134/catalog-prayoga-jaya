{{-- DASHBOARD admin --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    {{-- Auth::user() = data user yang sedang login (dari tabel users) --}}
    <p class="text-muted">Selamat datang, {{ Auth::user()->nama }}</p>

    <div class="row g-3">
        <div class="col-6 col-xl-3">
            <a href="{{ route('backend.kategori.index') }}" class="card text-center h-100 text-decoration-none">
                <div class="card-body">
                    <i class="bi bi-tags fs-3 text-primary"></i>
                    <div class="display-6">{{ $jumlahKategori }}</div>
                    <div class="text-muted">Kategori</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-eye fs-3 text-success"></i>
                    <div class="display-6">{{ $jumlahPublis }}</div>
                    <div class="text-muted">Produk Publis</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-eye-slash fs-3 text-secondary"></i>
                    <div class="display-6">{{ $jumlahBlok }}</div>
                    <div class="text-muted">Produk Blok</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="bi bi-star fs-3 text-warning"></i>
                    <div class="display-6">{{ $jumlahUnggulan }}</div>
                    <div class="text-muted">Produk Unggulan</div>
                </div>
            </div>
        </div>
    </div>
@endsection
