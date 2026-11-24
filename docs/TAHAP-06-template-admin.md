# Tahap 6 — Template Admin dan Tabel Data

*Acuan: Modul Web Programming II, Minggu 6–7.*

## Tujuan

1. Membuat **satu layout admin** (sidebar, menu, pesan flash, CSS/JS) yang dipakai semua halaman admin, supaya kerangka HTML tidak ditulis berulang.
2. Memindahkan halaman login, dashboard, dan kategori ke layout tersebut dengan `@extends`, `@section`, dan `@yield`.
3. Menampilkan tabel kategori dengan **DataTables**: kotak cari, urut kolom, dan pagination.

> **Template Matrix Admin tidak tersedia.** Folder `public/backend` tidak ada di project ini. Sesuai aturan kerja, layout memakai **Bootstrap 5 via CDN** dengan struktur Blade yang sama seperti modul. Jika nanti template Matrix Admin disalin ke `public/backend`, yang perlu diganti hanya `resources/views/backend/v_layouts/app.blade.php`. Halaman-halaman lain tidak perlu diubah.

---

## Konsep: layout Blade

**Sebelum (Tahap 4–5):** setiap view berisi HTML lengkap.

```
v_kategori/index.blade.php  = <html><head>...Bootstrap...</head><body> ISI </body></html>
v_kategori/create.blade.php = <html><head>...Bootstrap...</head><body> ISI </body></html>
...  (5 file, kerangka yang sama ditulis 5 kali)
```

**Sesudah:** kerangka ditulis **sekali** di layout. Setiap halaman hanya berisi bagian yang berbeda.

```
┌─────────── backend/v_layouts/app.blade.php ────────────┐
│ <head> Bootstrap, ikon, DataTables CSS </head>          │
│ ┌─────────┐ ┌─────────────────────────────────────────┐ │
│ │ SIDEBAR │ │ <h1> @yield('judul') </h1>              │ │
│ │Dashboard│ │ @include('...flash')  ← pesan hijau/merah│ │
│ │Kategori │ │                                         │ │
│ │ ...     │ │ @yield('content')  ← ISI HALAMAN        │ │
│ │ Logout  │ │                                         │ │
│ └─────────┘ └─────────────────────────────────────────┘ │
│ <script> Bootstrap, jQuery, DataTables </script>        │
│ @stack('scripts')                                       │
└─────────────────────────────────────────────────────────┘
                         ▲
        @extends('backend.v_layouts.app')
                         │
┌────── backend/v_kategori/create.blade.php ──────┐
│ @section('judul', $judul)                       │
│ @section('content')                             │
│     <form> ... </form>                          │
│ @endsection                                     │
└─────────────────────────────────────────────────┘
```

| Direktif | Ditulis di | Arti |
|---|---|---|
| `@yield('content')` | layout | "Lubang" bernama `content` |
| `@yield('judul', 'Admin')` | layout | Lubang dengan nilai cadangan `'Admin'` jika tidak diisi |
| `@extends('backend.v_layouts.app')` | halaman | Memakai layout ini (harus baris pertama yang menghasilkan output) |
| `@section('content') ... @endsection` | halaman | Isi panjang untuk lubang `content` |
| `@section('judul', $judul)` | halaman | Isi singkat (satu nilai) untuk lubang `judul` |
| `@include('backend.v_layouts.flash')` | di mana saja | Menyisipkan file view lain apa adanya |
| `@stack('scripts')` + `@push('scripts')` | layout + halaman | Halaman bisa **menambahkan** JavaScript khusus ke bagian bawah layout |

---

## Daftar file yang dibuat atau diubah

| File | Status | Isi |
|---|---|---|
| `resources/views/backend/v_layouts/app.blade.php` | baru | Layout admin: sidebar (offcanvas di HP), judul, flash, `@yield('content')`, CDN, inisialisasi DataTables |
| `resources/views/backend/v_layouts/flash.blade.php` | baru | Pesan `success`/`error` yang bisa ditutup |
| `resources/views/backend/v_login/login.blade.php` | diubah | Memakai layout (tampil tanpa sidebar) |
| `resources/views/backend/v_beranda/index.blade.php` | diubah | Memakai layout + ikon |
| `resources/views/backend/v_kategori/index.blade.php` | diubah | Memakai layout + `<table class="datatable">` |
| `resources/views/backend/v_kategori/create.blade.php` | diubah | Memakai layout |
| `resources/views/backend/v_kategori/edit.blade.php` | diubah | Memakai layout |
| `tests/Feature/KategoriCrudTest.php` | diubah | +1 test: halaman memakai layout dan DataTables |

## Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:view backend.v_layouts.app` | Membuat file layout |
| `php artisan make:view backend.v_layouts.flash` | Membuat potongan view pesan flash |
| `php artisan view:cache` | Mengompilasi semua Blade sekaligus untuk memastikan tidak ada salah tulis direktif |
| `php artisan view:clear` | Menghapus hasil kompilasi Blade (jika tampilan tidak berubah setelah diedit) |
| `php artisan test` | Menjalankan semua test |

### Library dari CDN

| Library | Versi | Fungsi |
|---|---|---|
| Bootstrap | 5.3.3 | Tampilan: grid, kartu, tombol, form, offcanvas |
| Bootstrap Icons | 1.11.3 | Ikon (`<i class="bi bi-tags">`) |
| jQuery | 3.7.1 | Dibutuhkan DataTables |
| DataTables + gaya Bootstrap 5 | 2.1.8 | Cari, urut, dan pagination tabel |

**CDN** (*Content Delivery Network*) adalah server pihak lain yang menyediakan file library, sehingga kita tidak perlu menyimpannya di project. Konsekuensinya, **komputer harus terhubung ke internet** supaya tampilan admin tidak berantakan.

---

## Penjelasan kode penting

### 1. Satu layout untuk login dan halaman admin

```blade
@auth
    ... sidebar + <main> @yield('content') </main>
@else
    <main class="container"> @yield('content') </main>
@endauth
```

`@auth` bernilai benar jika ada user yang login. Halaman login hanya bisa dibuka tamu (middleware `guest` dari Tahap 5), sehingga di halaman login **sidebar tidak pernah tampil**. Satu layout cukup untuk keduanya.

### 2. Menandai menu aktif

```blade
<a href="{{ route('backend.kategori.index') }}"
   class="nav-link {{ request()->routeIs('backend.kategori.*') ? 'active' : '' }}">
```

`request()->routeIs('backend.kategori.*')` bernilai benar jika nama route halaman saat ini diawali `backend.kategori.`. Jadi menu **Kategori** tetap menyala di halaman daftar, tambah, maupun ubah. Bentuk `kondisi ? 'active' : ''` adalah *ternary*, yaitu `if` dalam satu baris.

### 3. Sidebar responsif (HP dan desktop)

```blade
<aside class="sidebar-admin offcanvas-lg offcanvas-start bg-dark" id="sidebarAdmin">
```

- Di layar **≥ 992px** (laptop), sidebar tampil tetap di kiri.
- Di layar lebih kecil (HP/tablet), sidebar tersembunyi dan bisa dibuka lewat tombol ☰ di bilah atas.

Semua ini diatur oleh class Bootstrap `offcanvas-lg`, tanpa JavaScript buatan sendiri.

### 4. Halaman memakai layout

```blade
@extends('backend.v_layouts.app')

@section('judul', $judul)

@section('content')
    <div class="card"> ...form... </div>
@endsection
```

Bandingkan dengan Tahap 4: tidak ada lagi `<html>`, `<head>`, link CDN, atau kode pesan flash. Yang tersisa hanya isi khas halaman itu.

### 5. DataTables

Di layout:

```js
$(function () {
    $('table.datatable').DataTable({
        pageLength: 10,
        language: { search: 'Cari:', ... },
    });
});
```

- `$(function () { ... })` artinya jalankan setelah halaman selesai dimuat.
- `$('table.datatable')` memilih **semua** tabel ber-class `datatable`. Tabel produk di Tahap 7 cukup diberi class yang sama.
- `language` berisi terjemahan teks bawaan DataTables (Search, Showing 1 to 10, Next, …) ke Bahasa Indonesia.

Di view kategori:

```blade
<table class="table table-striped datatable">
    <thead> ... <th data-orderable="false">Aksi</th> </thead>
    <tbody>
        @foreach ($kategori as $row) ... @endforeach
    </tbody>
</table>
```

- `data-orderable="false"` membuat kolom Aksi tidak bisa diurutkan, karena tidak masuk akal mengurutkan tombol.
- **`@forelse ... @empty` diganti `@foreach`.** Baris "Belum ada kategori" dengan `colspan="4"` membuat DataTables error (jumlah kolom tidak cocok). Pesan tabel kosong sekarang diatur DataTables lewat `emptyTable: 'Belum ada data'`.
- DataTables bekerja **di browser**: semua baris dikirim sekaligus, lalu dicari, diurutkan, dan dipecah per halaman oleh JavaScript. Cara ini cocok untuk data ratusan baris. Untuk puluhan ribu baris dibutuhkan *server-side processing*, yang tidak diperlukan di toko ini.

---

## Cara menguji di browser

1. `php artisan serve`, lalu pastikan komputer **terhubung ke internet** (untuk CDN).
2. Buka `http://localhost:8000/backend/login`. Kartu login tampil di tengah, **tanpa sidebar**, dengan ikon toko.
3. Login (`admin@prayogajaya.test` / `admin12345`). Dashboard tampil dengan sidebar gelap di kiri dan menu **Dashboard** menyala biru.
4. Klik **Kategori** di sidebar. Menu Kategori menyala, dan di atas tabel ada **"Tampilkan 10 data"** dan kotak **"Cari:"**.
5. **Cari:** ketik `cat`. Hanya baris "Cat & Pelapis" yang tersisa, dan keterangan berubah menjadi *"Menampilkan 1 sampai 1 dari 1 data (disaring dari 8 data)"*.
6. **Urut:** klik judul kolom **Jumlah Produk**. Baris terurut naik, dan klik sekali lagi untuk turun. Kolom **Aksi** tidak bisa diklik untuk diurutkan.
7. **Pagination:** tambah 3 kategori baru (misalnya Atap, Pintu, Kaca) sehingga totalnya 11. Muncul tombol halaman **1 2**.
8. Tambah, ubah, atau hapus kategori. Pesan hijau muncul di bawah judul dan bisa ditutup dengan tombol ×.
9. **Tampilan HP:** tekan `F12` → ikon HP/tablet (*Toggle device toolbar*) → pilih lebar 375px. Sidebar hilang dan diganti tombol ☰ di atas. Klik tombol itu, maka menu bergeser masuk dari kiri.
10. Klik **Lihat Website**. Website publik terbuka di tab baru.

```bash
php artisan test
```

Hasil saat tahap ini dikerjakan: **23 test lulus**. Semua halaman admin membalas 200 dengan sidebar, judul tab, dan menu aktif yang benar. Halaman login tampil tanpa sidebar. Semua file CDN bisa diakses. Fitur DataTables (cari, urut, pagination) berjalan di browser dan perlu dicek manual dengan langkah 4–7.

---

## Kesalahan umum dan cara memperbaikinya

| Gejala / pesan | Penyebab | Solusi |
|---|---|---|
| Halaman tampil polos tanpa gaya | Tidak ada internet, sehingga CDN gagal dimuat | Sambungkan internet. Untuk offline, unduh file library ke `public/` dan ubah link-nya. |
| Tabel tidak punya kotak cari | Lupa class `datatable` di `<table>`, atau jQuery dimuat **setelah** DataTables | Tambahkan class. Urutan script: jQuery → DataTables. |
| Konsol browser: `$ is not defined` | jQuery belum dimuat | Pastikan `<script src=".../jquery.min.js">` ada sebelum DataTables |
| DataTables: *Incorrect column count* | Ada baris `<td colspan>` (misalnya pesan "kosong") atau jumlah `<td>` ≠ jumlah `<th>` | Gunakan `@foreach`. Pastikan jumlah kolom sama. |
| Isi halaman tidak muncul, hanya layout | Lupa `@section('content')` / `@endsection`, atau nama section salah ketik | Nama di `@section` harus sama dengan `@yield` |
| Konten muncul **di atas** layout | `@extends` tidak di baris pertama, atau ada teks di luar `@section` | Taruh `@extends` paling atas. Semua isi masuk ke dalam `@section`. |
| `View [backend.v_layouts.app] not found` | Nama atau letak file layout salah | Cek `resources/views/backend/v_layouts/app.blade.php` |
| Tampilan tidak berubah setelah diedit | Cache Blade | `php artisan view:clear` |
| Menu aktif tidak menyala | Pola `routeIs(...)` tidak cocok dengan nama route | Cek nama route dengan `php artisan route:list --path=backend` |

---

## Perbedaan dengan modul (Laravel 10)

| Modul | Project ini |
|---|---|
| Template **Matrix Admin** dari `public/backend` | Template tidak tersedia, jadi dipakai **Bootstrap 5 + Bootstrap Icons via CDN** dengan struktur Blade yang sama |
| DataTables bawaan template Matrix | DataTables 2.1.8 via CDN, teks diterjemahkan ke Bahasa Indonesia |
| `@extends`, `@section`, `@yield`, `@include` | **Sama persis** (Blade tidak berubah antara Laravel 10 dan 13) |
| View dibuat manual | `php artisan make:view` |
