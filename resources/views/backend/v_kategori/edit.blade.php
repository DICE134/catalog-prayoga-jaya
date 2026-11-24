{{-- Form UBAH kategori --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="card" style="max-width: 640px">
        <div class="card-body">
            <form action="{{ route('backend.kategori.update', $kategori->id) }}" method="POST">
                @csrf
                {{-- Form HTML hanya mengenal GET/POST. @method('PUT') memberi tahu Laravel
                     bahwa ini sebenarnya permintaan PUT (update). --}}
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    {{-- old('nama_kategori', $kategori->nama_kategori): pakai ketikan terakhir jika validasi gagal,
                         jika tidak, tampilkan nama yang tersimpan di database --}}
                    <input type="text" name="nama_kategori" id="nama_kategori"
                           class="form-control @error('nama_kategori') is-invalid @enderror"
                           value="{{ old('nama_kategori', $kategori->nama_kategori) }}" autofocus>
                    @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                <a href="{{ route('backend.kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
