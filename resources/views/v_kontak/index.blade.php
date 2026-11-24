{{--
    KONTAK — memudahkan pelanggan menghubungi dan menemukan toko.
    Bagian: nomor WhatsApp, alamat, jam operasional, Google Maps, tombol WhatsApp.
    SEMUA data diambil dari config('toko') (file .env), tidak ditulis langsung di sini.
    Variabel dari KontakController:
      $linkWhatsapp → tautan wa.me dengan pesan pembuka
      $mapsEmbed    → alamat peta tertanam (null jika TOKO_MAPS_EMBED kosong/tidak valid)
      $mapsUrl      → tautan Google Maps biasa (string kosong jika belum diisi)
--}}
@extends('v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="container py-5">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kontak</li>
            </ol>
        </nav>
        <h1 class="judul-bagian fs-2">Hubungi Kami</h1>
        <p class="text-muted mb-4">Tanyakan harga, stok, atau pesan material langsung kepada kami.</p>

        <div class="row g-4">

            {{-- ===== KOLOM KIRI: INFORMASI KONTAK ===== --}}
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body">
                        <ul class="list-unstyled mb-4">
                            {{-- NOMOR WHATSAPP --}}
                            <li class="d-flex gap-3 mb-4">
                                <i class="bi bi-whatsapp fs-3 text-success" aria-hidden="true"></i>
                                <div>
                                    <h2 class="h6 mb-1">WhatsApp</h2>
                                    <a href="{{ $linkWhatsapp }}" target="_blank" rel="noopener" class="text-body">
                                        +{{ config('toko.wa') }}
                                    </a>
                                </div>
                            </li>

                            {{-- ALAMAT --}}
                            <li class="d-flex gap-3 mb-4">
                                <i class="bi bi-geo-alt fs-3 text-toko" aria-hidden="true"></i>
                                <div>
                                    <h2 class="h6 mb-1">Alamat</h2>
                                    {{-- <address>: tag HTML khusus untuk informasi kontak/alamat --}}
                                    <address class="mb-0">{{ config('toko.alamat') }}</address>
                                </div>
                            </li>

                            {{-- JAM OPERASIONAL --}}
                            <li class="d-flex gap-3">
                                <i class="bi bi-clock fs-3 text-toko" aria-hidden="true"></i>
                                <div>
                                    <h2 class="h6 mb-1">Jam Operasional</h2>
                                    <p class="mb-0">{{ config('toko.jam_buka') }}</p>
                                </div>
                            </li>
                        </ul>

                        {{-- TOMBOL WHATSAPP (besar, mudah ditekan di HP) --}}
                        <a href="{{ $linkWhatsapp }}" class="btn btn-toko btn-lg w-100" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Chat via WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== KOLOM KANAN: GOOGLE MAPS =====
                 Urutan keputusan:
                   1. TOKO_MAPS_EMBED diisi  → tampilkan peta tertanam (iframe)
                   2. hanya TOKO_MAPS_URL    → tampilkan tombol "Buka di Google Maps"
                   3. keduanya kosong        → pesan "lokasi belum diisi" --}}
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 mb-3">Lokasi Toko</h2>

                        @if ($mapsEmbed)
                            {{-- ratio-4x3: iframe ikut mengecil di HP tanpa terpotong.
                                 loading="lazy": peta baru dimuat saat di-scroll (hemat kuota). --}}
                            <div class="ratio ratio-4x3 rounded overflow-hidden border mb-3">
                                <iframe src="{{ $mapsEmbed }}" title="Peta lokasi {{ config('toko.nama') }}"
                                        style="border:0" allowfullscreen loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        @endif

                        @if ($mapsUrl !== '')
                            @unless ($mapsEmbed)
                                <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center text-center bg-light rounded p-4 mb-3">
                                    <i class="bi bi-map fs-1 text-toko" aria-hidden="true"></i>
                                    <p class="text-muted mt-2 mb-0">Klik tombol di bawah untuk melihat lokasi toko dan petunjuk arah.</p>
                                </div>
                            @endunless
                            <a href="{{ $mapsUrl }}" class="btn btn-garis" target="_blank" rel="noopener">
                                <i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Buka di Google Maps
                            </a>
                        @elseif (! $mapsEmbed)
                            {{-- PLACEHOLDER: isi TOKO_MAPS_URL atau TOKO_MAPS_EMBED di .env --}}
                            <div class="flex-grow-1 d-flex align-items-center justify-content-center bg-light rounded p-4 text-center">
                                <p class="text-muted fst-italic mb-0">(Lokasi Google Maps belum diisi)</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
