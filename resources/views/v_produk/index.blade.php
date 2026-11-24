{{--
    KATALOG PRODUK — dipakai oleh ProdukController@produkAll dan @produkKategori.
    Variabel dari controller:
      $produk          → produk Publis, sudah dipaginasi (9 per halaman)
      $cari            → kata kunci pencarian (?q=), string kosong jika tidak mencari
      $kategoriAktif   → kategori yang sedang difilter, atau null untuk semua kategori
      $daftarKategori  → semua kategori untuk sidebar (dengan produk_count)
--}}
@extends('v_layouts.app')

@section('judul', $judul)

@section('content')
    {{-- 1. JUDUL HALAMAN + 2. PENCARIAN (di atas latar abu) --}}
    <div class="bagian-abu py-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    @if ($kategoriAktif)
                        <li class="breadcrumb-item"><a href="{{ route('produk.all') }}">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $kategoriAktif->nama_kategori }}</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Produk</li>
                    @endif
                </ol>
            </nav>
            <h1 class="judul-bagian fs-2 mb-4">{{ $kategoriAktif ? $kategoriAktif->nama_kategori : 'Katalog Produk' }}</h1>

            {{-- method="GET" → kata kunci masuk ke URL (?q=semen), sehingga hasil bisa dibagikan/di-bookmark.
                 action = halaman yang sedang dibuka → jika sedang di satu kategori, pencarian tetap di kategori itu. --}}
            <form action="{{ $kategoriAktif ? route('produk.kategori', $kategoriAktif->id) : route('produk.all') }}"
                  method="GET" role="search" style="max-width: 640px">
                <label for="q" class="visually-hidden">Cari produk</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" name="q" id="q" value="{{ $cari }}" class="form-control border-start-0"
                           placeholder="Cari nama produk atau merek">
                    <button type="submit" class="btn btn-toko">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            {{-- 3. FILTER KATEGORI --}}
            <aside class="col-lg-3" aria-label="Filter kategori">
                {{-- Di HP daftar kategori disembunyikan dan dibuka lewat tombol ini (collapse Bootstrap).
                     Di layar besar (d-lg-block) daftar selalu tampil. --}}
                <button class="btn btn-garis w-100 d-lg-none" type="button"
                        data-bs-toggle="collapse" data-bs-target="#daftarKategori"
                        aria-expanded="false" aria-controls="daftarKategori">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filter Kategori
                    @if ($kategoriAktif) : {{ $kategoriAktif->nama_kategori }} @endif
                </button>

                <div class="collapse d-lg-block mt-3 mt-lg-0" id="daftarKategori">
                    <h2 class="h6 d-none d-lg-block mb-3">Kategori</h2>
                    <nav class="daftar-filter d-grid gap-1">
                        {{-- array_filter(['q' => $cari]): ?q= hanya ditambahkan jika sedang mencari.
                             Jadi pindah kategori TIDAK menghapus kata kunci pencarian. --}}
                        <a href="{{ route('produk.all', array_filter(['q' => $cari])) }}"
                           class="@if (! $kategoriAktif) active @endif"
                           @if (! $kategoriAktif) aria-current="true" @endif>
                            Semua Kategori
                        </a>
                        @foreach ($daftarKategori as $kat)
                            {{-- Parameter yang tidak ada di URL route (q) otomatis menjadi query string:
                                 /produk/kategori/3?q=semen --}}
                            <a href="{{ route('produk.kategori', array_filter(['id' => $kat->id, 'q' => $cari])) }}"
                               class="@if ($kategoriAktif?->id === $kat->id) active @endif"
                               @if ($kategoriAktif?->id === $kat->id) aria-current="true" @endif>
                                <span>{{ $kat->nama_kategori }}</span>
                                <span class="jumlah">{{ $kat->produk_count }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            {{-- 4–6. DAFTAR PRODUK --}}
            <section class="col-lg-9" aria-label="Daftar produk">
                {{-- Keterangan hasil: firstItem()/lastItem()/total() dari objek pagination --}}
                <p class="text-muted small mb-4">
                    @if ($produk->total() > 0)
                        Menampilkan {{ $produk->firstItem() }}–{{ $produk->lastItem() }} dari {{ $produk->total() }} produk
                    @endif
                    @if ($cari !== '')
                        untuk pencarian <strong class="text-body">"{{ $cari }}"</strong>
                        <a href="{{ $kategoriAktif ? route('produk.kategori', $kategoriAktif->id) : route('produk.all') }}"
                           class="ms-1">Hapus pencarian</a>
                    @endif
                </p>

                <div class="row row-cols-2 row-cols-xl-3 g-4">
                    @forelse ($produk as $row)
                        <div class="col">
                            {{-- Partial kartu yang sama dengan Produk Unggulan di beranda --}}
                            @include('v_produk.kartu', ['row' => $row])
                        </div>
                    @empty
                        {{-- Pesan ramah jika hasil kosong --}}
                        <div class="col-12 w-100">
                            <div class="bagian-abu rounded-4 p-5">
                                <h2 class="h5">Produk tidak ditemukan</h2>
                                <p class="text-muted" style="max-width: 52ch">
                                    Coba kata kunci lain atau pilih kategori yang berbeda.
                                    Jika barang yang Anda cari belum ada di katalog, tanyakan langsung kepada kami.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="https://wa.me/{{ config('toko.wa') }}" class="btn btn-toko" target="_blank" rel="noopener">
                                        <i class="bi bi-whatsapp me-1" aria-hidden="true"></i>Tanya via WhatsApp
                                    </a>
                                    <a href="{{ route('produk.all') }}" class="btn btn-garis">Lihat semua produk</a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- PAGINATION: tombol halaman (gaya Bootstrap 5, teks dari lang/id).
                     Tidak tampil jika hasilnya muat dalam satu halaman. --}}
                <div class="mt-5">
                    {{ $produk->links() }}
                </div>
            </section>
        </div>
    </div>
@endsection
