{{--
    BERANDA — 8 bagian berurutan:
      1. Navbar ............ dari layout v_layouts/app.blade.php
      2. Hero .............. @section('hero') di bawah
      3. Pengenalan toko ... teks PLACEHOLDER
      4. Kategori produk ... dari tabel kategori ($kategori)
      5. Produk unggulan ... status Publis + unggulan, maksimal 6 ($produkUnggulan)
      6. Keunggulan toko ... teks umum, aman diedit
      7. Tombol katalog .... "Lihat Katalog"
      8. Footer ............ dari layout
    Data dikirim oleh BerandaController@index.
--}}
@extends('v_layouts.app')

@section('judul', $judul)

{{-- ===== 2. HERO (hanya di beranda) ===== --}}
@section('hero')
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="judul-besar mb-3">{{ config('toko.nama') }}</h1>
                    {{-- PLACEHOLDER: slogan dari TOKO_SLOGAN di .env --}}
                    <p class="fs-5 teks-pengantar mb-4">{{ config('toko.slogan') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('produk.all') }}" class="btn btn-toko btn-lg">Lihat Katalog</a>
                        <a href="https://wa.me/{{ config('toko.wa') }}" class="btn btn-garis btn-lg" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-1" aria-hidden="true"></i>Tanya via WhatsApp
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    {{-- PLACEHOLDER: ganti public/images/hero-material.svg dengan foto asli --}}
                    <img src="{{ asset('images/hero-material.svg') }}" alt="Material bangunan di {{ config('toko.nama') }}"
                         class="hero-gambar">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')

    {{-- ===== 3. PENGENALAN SINGKAT ===== --}}
    <section class="bagian" aria-labelledby="judul-pengenalan">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-md-5">
                    {{-- PLACEHOLDER: ganti public/images/foto-toko.svg dengan foto asli toko --}}
                    <img src="{{ asset('images/foto-toko.svg') }}" alt="Foto {{ config('toko.nama') }}"
                         class="img-fluid rounded-4">
                </div>
                <div class="col-md-7">
                    <h2 id="judul-pengenalan" class="judul-bagian">Selamat Datang di {{ config('toko.nama') }}</h2>
                    {{-- PLACEHOLDER — ganti paragraf ini dengan perkenalan asli toko --}}
                    <p class="text-muted fst-italic">
                        [PLACEHOLDER — ganti dengan perkenalan singkat toko, misalnya sejak kapan toko berdiri,
                        di mana lokasinya, dan siapa pelanggannya.]
                    </p>
                    <p class="teks-pengantar">
                        Lihat daftar material bangunan beserta harganya di katalog kami, lalu tanyakan
                        ketersediaan atau pesan langsung lewat WhatsApp.
                    </p>
                    <a href="{{ route('tentang') }}" class="fw-semibold">Selengkapnya tentang kami</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 4. KATEGORI PRODUK ===== --}}
    @php
        // Ikon Bootstrap Icons per nama kategori. Kategori baru yang tidak ada di daftar ini
        // otomatis memakai ikon 'bi-box-seam' (lihat ?? di bawah).
        $ikonKategori = [
            'Semen & Perekat' => 'bi-bricks',
            'Cat & Pelapis' => 'bi-paint-bucket',
            'Besi & Baja' => 'bi-hammer',
            'Pipa & Sanitasi' => 'bi-droplet',
            'Keramik & Lantai' => 'bi-grid-3x3',
            'Kayu & Triplek' => 'bi-tree',
            'Perkakas' => 'bi-tools',
            'Listrik' => 'bi-lightning-charge',
        ];
    @endphp
    <section class="bagian bagian-abu" aria-labelledby="judul-kategori">
        <div class="container">
            <h2 id="judul-kategori" class="judul-bagian">Kategori Produk</h2>
            <p class="teks-pengantar mb-4">Pilih kategori untuk melihat produk dan harganya.</p>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                @forelse ($kategori as $row)
                    <div class="col">
                        {{-- Tiap kategori menaut ke route produk.kategori (katalog per kategori) --}}
                        <a href="{{ route('produk.kategori', $row->id) }}" class="kotak-kategori">
                            <span class="ikon-kotak">
                                <i class="bi {{ $ikonKategori[$row->nama_kategori] ?? 'bi-box-seam' }}" aria-hidden="true"></i>
                            </span>
                            <span>
                                <span class="d-block fw-semibold">{{ $row->nama_kategori }}</span>
                                {{-- produk_count berasal dari withCount() di controller --}}
                                <span class="small text-muted">{{ $row->produk_count }} produk</span>
                            </span>
                        </a>
                    </div>
                @empty
                    <p class="text-muted">Belum ada kategori.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== 5. PRODUK UNGGULAN ===== --}}
    <section class="bagian" aria-labelledby="judul-unggulan">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
                <h2 id="judul-unggulan" class="judul-bagian mb-0">Produk Unggulan</h2>
                <a href="{{ route('produk.all') }}" class="fw-semibold text-nowrap">Lihat semua produk</a>
            </div>
            <div class="row row-cols-2 row-cols-md-3 g-4 g-lg-5">
                @forelse ($produkUnggulan as $row)
                    <div class="col">
                        {{-- Kartu produk dari partial v_produk/kartu.blade.php --}}
                        @include('v_produk.kartu', ['row' => $row])
                    </div>
                @empty
                    <p class="text-muted">Belum ada produk unggulan. Tandai produk sebagai unggulan di halaman admin.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== 6. KEUNGGULAN / LAYANAN TOKO =====
         Teks umum yang aman diedit. Sesuaikan dengan layanan asli toko
         (misalnya jangan tulis "gratis antar" jika toko tidak menyediakannya). --}}
    @php
        $keunggulan = [
            ['ikon' => 'bi-boxes', 'judul' => 'Material Lengkap', 'teks' => 'Berbagai kebutuhan bangunan dalam satu toko, dari semen sampai perkakas.'],
            ['ikon' => 'bi-tag', 'judul' => 'Harga Jelas', 'teks' => 'Harga tercantum di katalog, sehingga mudah membandingkan sebelum membeli.'],
            ['ikon' => 'bi-whatsapp', 'judul' => 'Tanya Mudah', 'teks' => 'Tanyakan stok dan pesan langsung lewat WhatsApp.'],
            ['ikon' => 'bi-people', 'judul' => 'Pelayanan Ramah', 'teks' => 'Kami bantu memilih material yang sesuai kebutuhan Anda.'],
        ];
    @endphp
    <section class="bagian bagian-abu" aria-labelledby="judul-keunggulan">
        <div class="container">
            <h2 id="judul-keunggulan" class="judul-bagian mb-4">Kenapa Belanja di Sini?</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                @foreach ($keunggulan as $item)
                    <div class="col">
                        <span class="ikon-kotak mb-3 bg-white"><i class="bi {{ $item['ikon'] }}" aria-hidden="true"></i></span>
                        <h3 class="h6 mb-1">{{ $item['judul'] }}</h3>
                        <p class="text-muted small mb-0">{{ $item['teks'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== 7. TOMBOL MENUJU KATALOG ===== --}}
    <section class="bagian" aria-labelledby="judul-ajakan">
        <div class="container">
            <div class="d-md-flex align-items-center justify-content-between gap-4">
                <div class="mb-3 mb-md-0">
                    <h2 id="judul-ajakan" class="judul-bagian">Cari material untuk proyek Anda?</h2>
                    <p class="teks-pengantar mb-0">Lihat semua produk beserta harganya di katalog.</p>
                </div>
                <a href="{{ route('produk.all') }}" class="btn btn-toko btn-lg px-5">Lihat Katalog</a>
            </div>
        </div>
    </section>

@endsection
