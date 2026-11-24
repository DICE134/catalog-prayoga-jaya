{{--
    KARTU PRODUK (partial) — dipakai ulang di Beranda, Katalog, dan Produk Serupa.
    Cara memakai:  @include('v_produk.kartu', ['row' => $produk])
    Kategori sebaiknya sudah diambil dengan with('kategori') di controller.
    Seluruh kartu bisa diklik (stretched-link pada tautan "Detail Produk").
--}}
<article class="kartu-produk h-100 d-flex flex-column">
    {{-- url_foto: accessor model Produk → foto asli, atau placeholder jika belum ada foto --}}
    <img src="{{ $row->url_foto }}" alt="Foto {{ $row->nama_produk }}" class="foto" loading="lazy">

    <h3 class="nama">{{ $row->nama_produk }}</h3>
    <p class="small text-muted mb-2">
        {{ $row->kategori->nama_kategori }}@if ($row->merek), {{ $row->merek }}@endif
        @if ($row->spesifikasi)<br>{{ $row->spesifikasi }}@endif
    </p>

    {{-- number_format(angka, 0 desimal, pemisah desimal ',', pemisah ribuan '.') → 68.000 --}}
    <p class="harga mt-auto mb-2">
        Rp. {{ number_format($row->harga, 0, ',', '.') }}
        <span class="small fw-normal text-muted">/ {{ $row->satuan }}</span>
    </p>

    <a href="{{ route('produk.detail', $row->id) }}" class="stretched-link small fw-semibold">
        Detail Produk<span class="visually-hidden"> {{ $row->nama_produk }}</span>
    </a>
</article>
