{{-- Form TAMBAH kategori --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="card" style="max-width: 640px">
        <div class="card-body">
            {{-- Form dikirim ke route store dengan method POST --}}
            <form action="{{ route('backend.kategori.store') }}" method="POST">
                {{-- @csrf: token keamanan wajib di setiap form POST. Tanpa ini → error 419. --}}
                @csrf

                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    {{-- @error(...) is-invalid @enderror: beri garis merah jika kolom ini gagal validasi.
                         old('nama_kategori'): isi kembali ketikan sebelumnya setelah validasi gagal. --}}
                    <input type="text" name="nama_kategori" id="nama_kategori"
                           class="form-control @error('nama_kategori') is-invalid @enderror"
                           value="{{ old('nama_kategori') }}" placeholder="Contoh: Atap & Genteng" autofocus>
                    @error('nama_kategori')
                        {{-- $message berisi pesan error dari validasi di controller --}}
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('backend.kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
