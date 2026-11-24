{{-- READ: daftar kategori, memakai DataTables (cari, urut, pagination) --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="mb-3">
        <a href="{{ route('backend.kategori.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                {{-- class "datatable" → otomatis diaktifkan DataTables oleh script di layout --}}
                <table class="table table-striped table-hover align-middle datatable w-100">
                    <thead>
                        <tr>
                            <th style="width: 60px">No</th>
                            <th>Nama Kategori</th>
                            <th style="width: 140px">Jumlah Produk</th>
                            {{-- data-orderable="false": kolom Aksi tidak bisa diurutkan --}}
                            <th style="width: 170px" data-orderable="false">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Untuk DataTables pakai @foreach (bukan @forelse): baris "kosong" dengan colspan
                             membuat DataTables error. Pesan tabel kosong sudah diatur di layout. --}}
                        @foreach ($kategori as $row)
                            <tr>
                                {{-- $loop->iteration = nomor urut perulangan, mulai dari 1 --}}
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->nama_kategori }}</td>
                                <td>{{ $row->produk_count }}</td>
                                <td>
                                    <a href="{{ route('backend.kategori.edit', $row->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Ubah
                                    </a>

                                    {{-- Hapus WAJIB lewat form (bukan link), karena butuh method DELETE dan token CSRF.
                                         Teks konfirmasi disimpan di data-konfirmasi lalu dibaca JavaScript lewat
                                         this.dataset.konfirmasi, sehingga tanda petik di nama tidak merusak kode. --}}
                                    <form action="{{ route('backend.kategori.destroy', $row->id) }}" method="POST" class="d-inline"
                                          data-konfirmasi="Hapus kategori {{ $row->nama_kategori }}?"
                                          onsubmit="return confirm(this.dataset.konfirmasi)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
