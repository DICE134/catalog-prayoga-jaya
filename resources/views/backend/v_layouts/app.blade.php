{{--
    LAYOUT ADMIN (backend) — kerangka yang dipakai SEMUA halaman admin.
    Halaman lain memakainya dengan:  @extends('backend.v_layouts.app')
    lalu mengisi bagian-bagian berikut:
      @section('judul', '...')      → judul tab browser dan judul halaman
      @section('content') ... @endsection   → isi utama (masuk ke @yield('content'))
      @push('scripts') ... @endpush → JavaScript khusus halaman (opsional)

    Template Matrix Admin dari modul tidak ada di public/backend, jadi layout ini
    memakai Bootstrap 5 via CDN dengan struktur Blade yang sama.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- @yield('judul', 'Admin'): isi dari @section('judul'), atau 'Admin' jika halaman tidak mengisinya --}}
    <title>@yield('judul', 'Admin') — {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.8/css/dataTables.bootstrap5.min.css">

    <style>
        /* Sidebar selebar 240px di layar besar; di HP berubah jadi menu geser (offcanvas) */
        @media (min-width: 992px) {
            .sidebar-admin { width: 240px; min-height: 100vh; position: sticky; top: 0; }
        }
        .sidebar-admin .nav-link { color: rgba(255, 255, 255, .75); border-radius: .375rem; }
        .sidebar-admin .nav-link:hover { color: #fff; background: rgba(255, 255, 255, .08); }
        .sidebar-admin .nav-link.active { color: #fff; background: var(--bs-primary); }
    </style>
</head>
<body class="bg-light">

{{-- @auth ... @else ... @endauth: tampilan berbeda untuk yang sudah login dan yang belum --}}
@auth
    {{-- Bilah atas khusus HP/tablet: tombol untuk membuka sidebar --}}
    <nav class="navbar navbar-dark bg-dark d-lg-none px-3">
        <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarAdmin" aria-controls="sidebarAdmin" aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h6">Admin {{ config('app.name') }}</span>
    </nav>

    <div class="d-lg-flex">
        {{-- SIDEBAR. offcanvas-lg: tampil tetap di layar besar (≥992px), jadi menu geser di layar kecil --}}
        <aside class="sidebar-admin offcanvas-lg offcanvas-start bg-dark text-white" tabindex="-1"
               id="sidebarAdmin" aria-labelledby="sidebarAdminLabel">
            <div class="offcanvas-header border-bottom border-secondary">
                <h2 class="offcanvas-title h6 mb-0" id="sidebarAdminLabel">
                    <i class="bi bi-shop"></i> {{ config('app.name') }}
                </h2>
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas"
                        data-bs-target="#sidebarAdmin" aria-label="Tutup menu"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column p-3">
                <ul class="nav nav-pills flex-column gap-1 w-100">
                    {{-- request()->routeIs(...) bernilai true jika halaman yang dibuka cocok dengan nama route,
                         dipakai untuk menandai menu yang sedang aktif (class "active") --}}
                    <li class="nav-item">
                        <a href="{{ route('backend.beranda') }}"
                           class="nav-link {{ request()->routeIs('backend.beranda') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        {{-- backend.kategori.* = semua route kategori (index, create, edit, ...) --}}
                        <a href="{{ route('backend.kategori.index') }}"
                           class="nav-link {{ request()->routeIs('backend.kategori.*') ? 'active' : '' }}">
                            <i class="bi bi-tags me-2"></i>Kategori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('backend.produk.index') }}"
                           class="nav-link {{ request()->routeIs('backend.produk.*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam me-2"></i>Produk
                        </a>
                    </li>
                </ul>

                <hr class="border-secondary w-100">

                <ul class="nav nav-pills flex-column gap-1 w-100">
                    <li class="nav-item">
                        <a href="{{ route('beranda') }}" class="nav-link" target="_blank" rel="noopener">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Lihat Website
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('backend.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                                <i class="bi bi-box-arrow-left me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>

                <div class="mt-auto pt-3 small text-white-50">
                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->nama }}
                </div>
            </div>
        </aside>

        {{-- ISI UTAMA --}}
        <main class="flex-grow-1 p-3 p-lg-4" style="min-width: 0">
            <h1 class="h3 mb-3">@yield('judul')</h1>

            {{-- Pesan flash ditulis SEKALI di layout, jadi semua halaman otomatis bisa menampilkannya --}}
            @include('backend.v_layouts.flash')

            @yield('content')
        </main>
    </div>
@else
    {{-- Belum login (halaman login): tanpa sidebar, isi di tengah --}}
    <main class="container py-5" style="max-width: 440px">
        @include('backend.v_layouts.flash')

        @yield('content')
    </main>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- DataTables butuh jQuery --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Semua <table class="datatable"> otomatis diberi fitur cari, urut, dan pagination.
    // Teks tombol/keterangan diterjemahkan ke Bahasa Indonesia.
    $(function () {
        $('table.datatable').DataTable({
            pageLength: 10,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(disaring dari _MAX_ data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' },
            },
        });
    });
</script>
{{-- Tempat JavaScript tambahan dari halaman (lewat @push('scripts')) --}}
@stack('scripts')
</body>
</html>
