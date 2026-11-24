{{--
    LAYOUT FRONTEND (website publik) — kerangka untuk Beranda, Katalog, Detail, Tentang Kami, Kontak.
    Halaman memakainya dengan:  @extends('v_layouts.app')
      @section('judul', '...')            → judul tab browser
      @section('hero') ... @endsection    → banner/hero, HANYA diisi oleh halaman beranda
      @section('content') ... @endsection → isi utama halaman
      @push('scripts') ... @endpush       → JavaScript khusus halaman (opsional)

    Gaya tampilan ada di public/css/frontend.css (Bootstrap 5 + penyesuaian warna & huruf).
    Bagian e-commerce (keranjang, wishlist, akun, mata uang) sengaja TIDAK dibuat.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Beranda') — {{ config('toko.nama') }}</title>
    <meta name="description" content="Katalog produk {{ config('toko.nama') }}. Tanya harga dan pesan lewat WhatsApp.">
    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">

    {{-- Huruf Inter dari Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Dimuat SETELAH Bootstrap supaya bisa menimpa gayanya --}}
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">

{{-- Tautan "lompat ke isi" untuk pengguna keyboard/pembaca layar (aksesibilitas) --}}
<a href="#isi-utama" class="visually-hidden-focusable btn btn-light position-absolute m-2" style="z-index: 1100">Lompat ke isi</a>

{{-- ===================== NAVBAR ===================== --}}
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-toko py-3" aria-label="Menu utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('beranda') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo {{ config('toko.nama') }}" width="36" height="36">
                <span>{{ config('toko.nama') }}</span>
            </a>

            {{-- Tombol "hamburger": hanya muncul di layar kecil (HP) untuk membuka/menutup menu --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama"
                    aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuUtama">
                <ul class="navbar-nav ms-auto mb-3 mb-lg-0 gap-lg-3">
                    {{-- routeIs(): menandai menu yang sedang dibuka dengan class "active".
                         aria-current="page" memberi tahu pembaca layar halaman mana yang aktif. --}}
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('beranda')) active @endif"
                           @if (request()->routeIs('beranda')) aria-current="page" @endif
                           href="{{ route('beranda') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        {{-- produk.* = katalog, katalog per kategori, dan detail produk --}}
                        <a class="nav-link @if (request()->routeIs('produk.*')) active @endif"
                           @if (request()->routeIs('produk.*')) aria-current="page" @endif
                           href="{{ route('produk.all') }}">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('tentang')) active @endif"
                           @if (request()->routeIs('tentang')) aria-current="page" @endif
                           href="{{ route('tentang') }}">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('kontak')) active @endif"
                           @if (request()->routeIs('kontak')) aria-current="page" @endif
                           href="{{ route('kontak') }}">Kontak</a>
                    </li>
                </ul>
                {{-- Pengganti ikon keranjang di template E-Shop: pelanggan bertanya/memesan lewat WhatsApp --}}
                <a href="https://wa.me/{{ config('toko.wa') }}" class="btn btn-toko ms-lg-4" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-1" aria-hidden="true"></i>Chat WhatsApp
                </a>
            </div>
        </div>
    </nav>
</header>

{{-- ===================== HERO (khusus beranda) =====================
     @hasSection('hero') bernilai true hanya jika halaman mengisi @section('hero').
     Halaman selain beranda tidak mengisinya, jadi hero tidak tampil di sana. --}}
@hasSection('hero')
    @yield('hero')
@endif

{{-- ===================== ISI HALAMAN ===================== --}}
<main id="isi-utama" class="flex-grow-1">
    @yield('content')
</main>

{{-- ===================== FOOTER =====================
     Semua data toko diambil dari config/toko.php (yang membaca .env), bukan ditulis langsung. --}}
<footer class="footer pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h2 class="h5">{{ config('toko.nama') }}</h2>
                <p class="mb-0" style="max-width: 36ch">{{ config('toko.slogan') }}</p>
            </div>

            <div class="col-6 col-md-3">
                <h2 class="h6">Menu</h2>
                <ul class="list-unstyled mb-0 d-grid gap-2">
                    <li><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li><a href="{{ route('produk.all') }}">Produk</a></li>
                    <li><a href="{{ route('tentang') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('kontak') }}">Kontak</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h2 class="h6">Hubungi Kami</h2>
                <ul class="list-unstyled mb-0 d-grid gap-2">
                    <li class="d-flex gap-2">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i><span>{{ config('toko.alamat') }}</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i>
                        <a href="https://wa.me/{{ config('toko.wa') }}" target="_blank" rel="noopener">+{{ config('toko.wa') }}</a>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-clock" aria-hidden="true"></i><span>{{ config('toko.jam_buka') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4" style="border-color: rgba(255, 255, 255, .15); opacity: 1">
        {{-- now()->year: tahun berjalan otomatis (zona waktu Asia/Jakarta dari config/app.php) --}}
        <p class="small mb-0">&copy; {{ now()->year }} {{ config('toko.nama') }}</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
