{{-- READ: daftar produk (DataTables) dengan nama kategori dari relasi --}}
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="mb-3">
        <a href="{{ route('backend.produk.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Produk
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle datatable w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th data-orderable="false">Foto</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Unggulan</th>
                            <th data-orderable="false" style="min-width: 170px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{-- url_foto = accessor di model Produk (foto asli atau placeholder) --}}
                                    <img src="{{ $row->url_foto }}" alt="Foto {{ $row->nama_produk }}"
                                         width="56" height="56" class="rounded border" style="object-fit: cover">
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $row->nama_produk }}</div>
                                    <small class="text-muted">
                                        {{ $row->merek ?? 'Tanpa merek' }}
                                        @if ($row->spesifikasi) · {{ $row->spesifikasi }} @endif
                                    </small>
                                </td>
                                {{-- $row->kategori->nama_kategori: nama kategori lewat relasi belongsTo.
                                     Datanya sudah diambil sekaligus oleh with('kategori') di controller. --}}
                                <td>{{ $row->kategori->nama_kategori }}</td>
                                {{-- data-order: DataTables mengurutkan berdasarkan angka asli, bukan teks "Rp. 68.000" --}}
                                <td data-order="{{ $row->harga }}" class="text-nowrap">
                                    Rp. {{ number_format($row->harga, 0, ',', '.') }}
                                    <small class="text-muted">/ {{ $row->satuan }}</small>
                                </td>
                                <td>
                                    @if ($row->status == 1)
                                        <span class="badge text-bg-success">Publis</span>
                                    @else
                                        <span class="badge text-bg-secondary">Blok</span>
                                    @endif
                                </td>
                                <td data-order="{{ $row->unggulan ? 1 : 0 }}">
                                    @if ($row->unggulan)
                                        <i class="bi bi-star-fill text-warning" title="Unggulan"></i>
                                        <span class="visually-hidden">Ya</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('backend.produk.edit', $row) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Ubah
                                    </a>
                                    <form action="{{ route('backend.produk.destroy', $row) }}" method="POST" class="d-inline"
                                          data-konfirmasi="Hapus produk {{ $row->nama_produk }} beserta semua fotonya?"
                                          onsubmit="return confirm(this.dataset.konfirmasi)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
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
