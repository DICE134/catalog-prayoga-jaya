{{-- Form TAMBAH produk --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="card">
        <div class="card-body">
            {{-- enctype="multipart/form-data" WAJIB untuk form yang mengirim file.
                 Tanpa ini, file foto tidak ikut terkirim. --}}
            <form action="{{ route('backend.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Isian form ditulis sekali di form.blade.php --}}
                @include('backend.v_produk.form')

                <hr>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('backend.produk.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
