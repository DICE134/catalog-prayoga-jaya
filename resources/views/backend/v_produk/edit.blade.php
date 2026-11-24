{{-- Form UBAH produk + kelola FOTO TAMBAHAN --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    {{-- 1. Form data produk --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('backend.produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ['produk' => $produk] mengirim data produk ke form.blade.php --}}
                @include('backend.v_produk.form', ['produk' => $produk])

                <hr>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                <a href="{{ route('backend.produk.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

    {{-- 2. Foto tambahan (tabel foto_produk).
         Form TERPISAH dari form di atas, karena HTML tidak boleh memiliki form di dalam form. --}}
    <div class="card" id="foto-tambahan">
        <div class="card-header"><i class="bi bi-images me-1"></i>Foto Tambahan</div>
        <div class="card-body">
            <form action="{{ route('backend.produk.foto.store', $produk) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <label for="foto_tambahan" class="form-label">Unggah foto tambahan (bisa pilih beberapa sekaligus, maksimal 5)</label>
                <div class="input-group has-validation">
                    {{-- name="foto_tambahan[]" + multiple → terkirim sebagai array beberapa file --}}
                    <input type="file" name="foto_tambahan[]" id="foto_tambahan" multiple accept="image/jpeg,image/png,image/webp"
                           class="form-control @if ($errors->has('foto_tambahan') || $errors->has('foto_tambahan.*')) is-invalid @endif">
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Unggah</button>
                    {{-- Error bisa untuk daftar file (foto_tambahan) atau salah satu file (foto_tambahan.0, .1, ...) --}}
                    @foreach ($errors->get('foto_tambahan*') as $pesanPerKolom)
                        @foreach ($pesanPerKolom as $pesan)
                            <div class="invalid-feedback">{{ $pesan }}</div>
                        @endforeach
                    @endforeach
                </div>
                <div class="form-text">JPG, PNG, atau WEBP, maksimal 2 MB per foto.</div>
            </form>

            <div class="row g-3">
                @forelse ($produk->fotoProduk as $foto)
                    <div class="col-6 col-md-3">
                        <div class="card h-100">
                            <img src="{{ $foto->url_foto }}" alt="Foto tambahan {{ $produk->nama_produk }}"
                                 class="card-img-top" style="aspect-ratio: 1; object-fit: cover">
                            <div class="card-body p-2 text-center">
                                <form action="{{ route('backend.produk.foto.destroy', $foto) }}" method="POST"
                                      onsubmit="return confirm('Hapus foto tambahan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada foto tambahan.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
