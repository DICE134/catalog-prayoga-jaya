{{--
    HALAMAN 404 (tidak ditemukan) — Laravel otomatis memakai file errors/404.blade.php
    setiap kali terjadi 404, misalnya dari firstOrFail()/findOrFail() atau URL yang salah.
--}}
@extends('v_layouts.app')

@section('judul', 'Halaman Tidak Ditemukan')

@section('content')
    <div class="container text-center py-5">
        <p class="display-1 fw-bold text-toko mb-0">404</p>
        <h1 class="h3">Halaman tidak ditemukan</h1>
        <p class="text-muted">Produk atau halaman yang Anda cari tidak ada atau sudah tidak tersedia.</p>
        <a href="{{ route('produk.all') }}" class="btn btn-toko me-2">Lihat Katalog</a>
        <a href="{{ route('beranda') }}" class="btn btn-garis">Ke Beranda</a>
    </div>
@endsection
