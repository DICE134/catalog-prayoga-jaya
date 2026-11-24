{{--
    DETAIL PRODUK — dipakai oleh ProdukController@detail.
    Variabel dari controller:
      $produk        → produk Publis (dengan kategori dan fotoProduk)
      $linkWhatsapp  → https://wa.me/...?text=... berisi nama produk
      $produkSerupa  → maksimal 4 produk Publis dari kategori yang sama
--}}
@extends('v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="container py-5">

        {{-- Jalur navigasi --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-4">
                <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('produk.all') }}">Produk</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('produk.kategori', $produk->kategori_id) }}">{{ $produk->kategori->nama_kategori }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_produk }}</li>
            </ol>
        </nav>

        <div class="row g-5">

            {{-- ===== FOTO UTAMA + FOTO TAMBAHAN ===== --}}
            <div class="col-md-6">
                <img src="{{ $produk->url_foto }}" alt="Foto {{ $produk->nama_produk }}" id="fotoBesar" class="foto-detail">

                {{-- Galeri kecil hanya tampil jika ada foto tambahan (tabel foto_produk) --}}
                @if ($produk->fotoProduk->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-3" aria-label="Foto lainnya">
                        {{-- Foto utama ikut dijadikan thumbnail pertama, supaya bisa kembali ke foto awal --}}
                        @foreach (collect([$produk->url_foto])->merge($produk->fotoProduk->pluck('url_foto')) as $url)
                            <button type="button" class="btn p-0 border border-2 rounded-3 thumbnail-foto @if ($loop->first) border-dark @endif"
                                    data-foto="{{ $url }}" aria-label="Tampilkan foto {{ $loop->iteration }}">
                                <img src="{{ $url }}" alt="" width="72" height="72" class="rounded-3" style="object-fit: cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ===== INFORMASI PRODUK ===== --}}
            <div class="col-md-6">
                <p class="text-muted mb-2">{{ $produk->kategori->nama_kategori }}</p>
                <h1 class="judul-bagian fs-2">{{ $produk->nama_produk }}</h1>

                {{-- Harga format rupiah per satuan --}}
                <p class="harga fs-2 mb-0 mt-3">Rp. {{ number_format($produk->harga, 0, ',', '.') }}</p>
                <p class="text-muted">per {{ $produk->satuan }}</p>

                {{-- TOMBOL WHATSAPP — pengganti tombol "Pesan"/keranjang di template modul.
                     target="_blank": WhatsApp dibuka di tab/aplikasi baru. --}}
                <a href="{{ $linkWhatsapp }}" class="btn btn-toko btn-lg w-100 mt-2" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-2" aria-hidden="true"></i>Tanya / Pesan via WhatsApp
                </a>
                {{-- Catatan umum, boleh diubah sesuai kebijakan toko --}}
                <p class="small text-muted mt-2 mb-4">
                    Harga dapat berubah sewaktu-waktu. Tanyakan ketersediaan stok sebelum datang ke toko.
                </p>

                {{-- <dl> = daftar "istilah : keterangan", cocok untuk spesifikasi produk --}}
                <dl class="tabel-spek mb-4">
                    <div><dt>Merek</dt><dd>{{ $produk->merek ?? '-' }}</dd></div>
                    <div><dt>Spesifikasi / Ukuran</dt><dd>{{ $produk->spesifikasi ?? '-' }}</dd></div>
                    <div><dt>Satuan</dt><dd>{{ $produk->satuan }}</dd></div>
                    <div><dt>Kategori</dt><dd>{{ $produk->kategori->nama_kategori }}</dd></div>
                </dl>

                {{-- DESKRIPSI.
                     e() meng-escape HTML (aman dari XSS), lalu nl2br() mengubah baris baru menjadi <br>.
                     {!! !!} dipakai karena hasilnya sudah aman dan berisi tag <br>. --}}
                <h2 class="h6">Deskripsi</h2>
                @if ($produk->detail)
                    <p style="max-width: 65ch">{!! nl2br(e($produk->detail)) !!}</p>
                @else
                    <p class="text-muted">Belum ada deskripsi untuk produk ini.</p>
                @endif

                <a href="{{ route('produk.all') }}" class="fw-semibold">Kembali ke katalog</a>
            </div>
        </div>

        {{-- ===== PRODUK SERUPA (kategori sama, maksimal 4) ===== --}}
        @if ($produkSerupa->isNotEmpty())
            <section class="mt-5 pt-5 border-top" aria-labelledby="judul-serupa">
                <h2 id="judul-serupa" class="judul-bagian mb-4">Produk Serupa</h2>
                <div class="row row-cols-2 row-cols-lg-4 g-4">
                    @foreach ($produkSerupa as $row)
                        <div class="col">
                            @include('v_produk.kartu', ['row' => $row])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Klik thumbnail → foto besar berganti. Bingkai gelap menandai thumbnail yang sedang dipilih.
        document.querySelectorAll('.thumbnail-foto').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                document.getElementById('fotoBesar').src = tombol.dataset.foto;
                document.querySelectorAll('.thumbnail-foto').forEach(t => t.classList.remove('border-dark'));
                tombol.classList.add('border-dark');
            });
        });
    </script>
@endpush
