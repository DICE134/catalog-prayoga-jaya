{{--
    TENTANG KAMI — membangun kepercayaan pelanggan.
    Bagian: 1. Sejarah singkat  2. Profil toko  3. Jenis material  4. Foto toko  5. Informasi pelayanan

    PERHATIAN: teks bertanda [PLACEHOLDER] BELUM data asli. Ganti dengan informasi
    yang benar dari pemilik toko. Data alamat, WhatsApp, dan jam buka diambil dari config('toko').
--}}
@extends('v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="container py-5">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
            </ol>
        </nav>
        <h1 class="judul-bagian fs-2 mb-4">Tentang {{ config('toko.nama') }}</h1>

        {{-- ===== 1. SEJARAH SINGKAT + 4. FOTO TOKO ===== --}}
        <section class="row g-4 align-items-center mb-5" aria-labelledby="judul-sejarah">
            <div class="col-md-6 order-md-2">
                {{-- PLACEHOLDER: ganti public/images/foto-toko.svg dengan foto asli toko --}}
                <figure class="mb-0">
                    <img src="{{ asset('images/foto-toko.svg') }}" alt="Foto bagian depan {{ config('toko.nama') }}"
                         class="img-fluid rounded w-100">
                    <figcaption class="small text-muted mt-2">{{ config('toko.nama') }}</figcaption>
                </figure>
            </div>
            <div class="col-md-6 order-md-1">
                <h2 id="judul-sejarah" class="judul-bagian fs-4">Sejarah Singkat</h2>
                {{-- PLACEHOLDER — ganti dengan sejarah asli toko --}}
                <p class="text-muted fst-italic">
                    [PLACEHOLDER — ceritakan sejarah toko: tahun berdiri, siapa pendirinya, bagaimana toko
                    berkembang sampai sekarang. Tulis 1–2 paragraf.]
                </p>
            </div>
        </section>

        {{-- ===== 2. PROFIL TOKO ===== --}}
        <section class="mb-5" aria-labelledby="judul-profil">
            <h2 id="judul-profil" class="judul-bagian fs-4">Profil Toko</h2>
            {{-- PLACEHOLDER — ganti dengan profil asli (visi, siapa pelanggannya, apa yang membedakan toko) --}}
            <p class="text-muted fst-italic">
                [PLACEHOLDER — jelaskan profil toko, misalnya fokus usaha, pelanggan yang dilayani
                (rumah tangga, tukang, kontraktor), dan komitmen toko.]
            </p>
            {{-- Data di bawah otomatis dari config/toko.php (.env) --}}
            <dl class="row mb-0">
                <dt class="col-sm-3">Nama toko</dt>
                <dd class="col-sm-9">{{ config('toko.nama') }}</dd>
                <dt class="col-sm-3">Alamat</dt>
                <dd class="col-sm-9">{{ config('toko.alamat') }}</dd>
                <dt class="col-sm-3">Jam operasional</dt>
                <dd class="col-sm-9">{{ config('toko.jam_buka') }}</dd>
            </dl>
        </section>

        {{-- ===== 3. JENIS MATERIAL YANG DIJUAL (otomatis dari tabel kategori) ===== --}}
        <section class="bg-light rounded p-4 mb-5" aria-labelledby="judul-material">
            <h2 id="judul-material" class="judul-bagian fs-4">Jenis Material yang Kami Jual</h2>
            <p class="text-muted">Klik salah satu untuk melihat produknya di katalog.</p>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($daftarKategori as $kat)
                    <a href="{{ route('produk.kategori', $kat->id) }}" class="btn btn-garis">
                        <i class="bi bi-check2-circle me-1 text-toko" aria-hidden="true"></i>{{ $kat->nama_kategori }}
                    </a>
                @empty
                    <p class="mb-0 text-muted">Belum ada kategori.</p>
                @endforelse
            </div>
        </section>

        {{-- ===== 5. INFORMASI PELAYANAN ===== --}}
        <section class="mb-4" aria-labelledby="judul-pelayanan">
            <h2 id="judul-pelayanan" class="judul-bagian fs-4 mb-3">Informasi Pelayanan</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-clock fs-2 text-toko" aria-hidden="true"></i>
                            <h3 class="h6 mt-2">Jam Operasional</h3>
                            <p class="mb-0 text-muted">{{ config('toko.jam_buka') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-whatsapp fs-2 text-success" aria-hidden="true"></i>
                            <h3 class="h6 mt-2">Tanya &amp; Pesan via WhatsApp</h3>
                            <p class="text-muted">Tanyakan harga, stok, dan pemesanan langsung kepada kami.</p>
                            <a href="https://wa.me/{{ config('toko.wa') }}" class="btn btn-toko btn-sm" target="_blank" rel="noopener">
                                Chat WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-truck fs-2 text-toko" aria-hidden="true"></i>
                            <h3 class="h6 mt-2">Pengambilan &amp; Pengiriman</h3>
                            {{-- PLACEHOLDER — isi sesuai layanan asli. Jangan tulis "gratis antar" jika tidak ada. --}}
                            <p class="mb-0 text-muted fst-italic">
                                [PLACEHOLDER — jelaskan apakah barang diambil di toko, bisa diantar,
                                dan area pengirimannya.]
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="text-center mt-5">
            <a href="{{ route('produk.all') }}" class="btn btn-toko btn-lg">
                <i class="bi bi-grid me-1" aria-hidden="true"></i>Lihat Katalog Produk
            </a>
        </div>
    </div>
@endsection
