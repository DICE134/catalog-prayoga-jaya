# Tahap 9 — Halaman Beranda

*Acuan: Modul Web Programming III, Minggu 1–2 (`BerandaController@index`, `v_beranda/index.blade.php`).*

## 1. Tujuan

Beranda adalah halaman pertama yang dilihat pelanggan. Tugasnya memperkenalkan toko lalu mengarahkan pelanggan ke katalog produk. Beranda memuat 8 bagian berurutan:

| No | Bagian | Asal |
|---|---|---|
| 1 | Navbar | Layout (Tahap 8) |
| 2 | Hero: gambar, slogan, tombol katalog | `@section('hero')` |
| 3 | Pengenalan singkat toko | Teks **placeholder** |
| 4 | Kategori produk | Tabel `kategori` |
| 5 | Produk unggulan | Tabel `produk` (Publis + unggulan, maksimal 6) |
| 6 | Keunggulan atau layanan toko | Teks umum yang aman diedit |
| 7 | Tombol "Lihat Katalog" | Tautan ke `produk.all` |
| 8 | Footer | Layout (Tahap 8) |

Pembagian kerja dalam MVC:
- **Controller** (`BerandaController@index`) mengambil data dari **Model** (`Kategori`, `Produk`).
- **View** (`v_beranda/index.blade.php`) hanya menampilkan data tersebut.

## 2. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `app/Http/Controllers/BerandaController.php` | `index()` mengambil kategori dan produk unggulan. |
| `resources/views/v_beranda/index.blade.php` | Beranda lengkap dengan 8 bagian. |
| `resources/views/v_produk/kartu.blade.php` | **Baru.** Partial kartu produk, dipakai lagi di katalog (Tahap 10). |
| `public/css/frontend.css` | Gaya hero, kotak kategori, dan kartu produk (desain ulang, lihat Tahap 8 bagian 4.7). |
| `public/images/foto-toko.svg` | **Baru.** Foto toko sementara (placeholder). |
| `tests/Feature/BerandaTest.php` | **Baru.** 5 pengujian beranda. |
| `tests/Feature/LayoutFrontendTest.php` | Ditambah `RefreshDatabase`, karena beranda sekarang membaca database. |

## 3. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:view v_produk.kartu` | Membuat file partial kartu produk. |
| `php artisan make:test BerandaTest --phpunit` | Membuat file pengujian. |
| `php artisan test` | Menjalankan semua pengujian. |

## 4. Penjelasan kode

### 4.1 Controller: mengambil data

```php
$kategori = Kategori::withCount(['produk' => fn ($query) => $query->where('status', 1)])
    ->orderBy('nama_kategori')
    ->get();
```

- `withCount('produk')` menambahkan kolom `produk_count` ke setiap kategori. Hasilnya **hanya angka**, data produknya tidak ikut diambil, jadi lebih ringan.
- Fungsi `fn ($query) => $query->where('status', 1)` membuat hitungan **hanya mencakup produk Publis**. Produk Blok tidak terlihat oleh pelanggan, jadi tidak ikut dihitung.

```php
$produkUnggulan = Produk::with('kategori')
    ->where('status', 1)      // hanya Publis
    ->where('unggulan', 1)    // hanya yang dicentang "unggulan" di admin
    ->latest()                // terbaru dulu (urut created_at)
    ->take(6)                 // maksimal 6
    ->get();
```

`with('kategori')` mengambil nama kategori untuk ke-6 produk dalam **satu query tambahan**. Tanpa `with`, Laravel menjalankan satu query per produk. Masalah ini disebut N+1 dan sudah dijelaskan di Tahap 7.

### 4.2 Partial kartu produk

```blade
@include('v_produk.kartu', ['row' => $row])
```

Kartu produk (foto, nama, kategori, merek, spesifikasi, harga, tautan detail) ditulis **sekali** di `v_produk/kartu.blade.php`. Halaman katalog (Tahap 10) dan "Produk Serupa" (Tahap 11) memakai file yang sama. Jika tampilan kartu diubah, semua halaman itu ikut berubah. Hal ini terbukti saat desain ulang: cukup satu file kartu yang diganti.

Gaya kartu (class `kartu-produk` di `public/css/frontend.css`) mengikuti konsep minimalis:
- **Tanpa bayangan dan tanpa bingkai.** Foto persegi diletakkan di atas latar abu muda, lalu teks di bawahnya.
- **Harga** memakai class `harga`, yaitu tebal dan berwarna biru tua.
- **Seluruh kartu bisa diklik.** Tautan "Detail Produk" diberi class `stretched-link` dari Bootstrap, sehingga area kliknya melebar ke seluruh kartu (`position: relative` pada `.kartu-produk`). Ini memudahkan pengguna HP.

Harga ditulis dengan `number_format($row->harga, 0, ',', '.')`. Angka `68000` menjadi `68.000`, lalu diberi awalan `Rp.`.

### 4.3 Kategori dengan ikon

Bagian `@php ... @endphp` di view berisi daftar ikon untuk setiap nama kategori. Ekspresi `$ikonKategori[$row->nama_kategori] ?? 'bi-box-seam'` berarti: pakai ikon dari daftar, dan jika nama kategori tidak ada di daftar (misalnya kategori baru dari admin), pakai ikon kotak. Akibatnya kategori baru **tidak** menyebabkan error.

Setiap kotak kategori (class `kotak-kategori`) menaut ke `route('produk.kategori', $row->id)`, yaitu katalog per kategori yang dibuat di Tahap 10. Kotaknya berupa ikon dalam persegi abu, nama kategori, dan jumlah produk, dengan garis tipis yang berubah biru saat disorot.

### 4.3b Susunan tampilan beranda

- **Hero** (`class="hero"`) berlatar abu muda dan terbagi dua kolom: judul besar berwarna biru tua (`judul-besar`) beserta slogan dan dua tombol di kiri, gambar di kanan. Di HP, kedua kolom bertumpuk.
- Bagian-bagian halaman **berselang-seling** antara putih (`bagian`) dan abu muda (`bagian-abu`), sehingga batas antarbagian terlihat tanpa garis tambahan.
- Semua judul bagian **rata kiri** supaya mata mudah mengikuti.
- **Tombol utama** (Lihat Katalog) berwarna oranye (`btn-toko`). **Tombol kedua** (Tanya via WhatsApp di hero) bergaris biru (`btn-garis`), supaya hanya ada satu tombol yang paling menonjol.

### 4.4 `@forelse ... @empty`

```blade
@forelse ($produkUnggulan as $row)
    ...kartu...
@empty
    <p>Belum ada produk unggulan...</p>
@endforelse
```

Cara kerjanya sama dengan `@foreach`, tetapi bagian `@empty` tampil jika data kosong. Hasilnya pesan yang ramah, bukan halaman kosong.

### 4.5 Aksesibilitas

- Setiap `<section>` memiliki judul (`<h2>`) yang ditautkan lewat `aria-labelledby`.
- Gambar kartu memakai `loading="lazy"`, jadi foto baru dimuat ketika di-scroll. Ini menghemat kuota di HP.
- Tautan "Detail Produk" diberi teks tersembunyi berisi nama produk (`visually-hidden`), sehingga pembaca layar membacanya "Detail Produk Semen Portland", bukan "Detail Produk" berkali-kali.

## 5. Cara menguji di browser

1. Buka `http://localhost:8000`. Kedelapan bagian tampil dari atas ke bawah.
2. **Kategori:** 8 kotak kategori tampil dengan jumlah produknya. Klik salah satu, dan URL berubah menjadi `/produk/kategori/{id}`.
3. **Produk unggulan:** 6 kartu tampil dengan harga Rupiah. Klik di mana saja pada kartu (foto, nama, atau harga), dan halaman detail terbuka.
4. **Coba dari admin:**
   - Login ke `/backend/login`, ubah salah satu produk unggulan menjadi **Blok**, lalu muat ulang beranda. Produk itu hilang dari Produk Unggulan.
   - Centang **unggulan** pada produk lain, dan produk itu muncul di beranda.
5. Perkecil jendela browser. Produk unggulan tampil 2 kolom di HP dan 3 kolom di tablet ke atas. Kotak kategori tampil 1 kolom di HP, 2 kolom di tablet, dan 4 kolom di desktop.

## 6. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| `Undefined variable $kategori` | Variabel tidak dikirim dari controller. | Pastikan `'kategori' => $kategori` ada di `view(...)`. |
| `Attempt to read property "nama_kategori" on null` | Produk menunjuk kategori yang sudah tidak ada. | Periksa `kategori_id`. Penghapusan kategori yang masih memiliki produk sudah dicegah sejak Tahap 4. |
| Jumlah produk per kategori ikut menghitung produk Blok | `withCount('produk')` dipakai tanpa syarat. | Tambahkan `fn ($q) => $q->where('status', 1)`. |
| Produk unggulan tidak muncul | Produk belum dicentang unggulan atau masih Blok. | Ubah di halaman admin. |
| Hanya tulisan "Detail Produk" yang bisa diklik, bukan seluruh kartu | `.kartu-produk` tidak memiliki `position: relative`, atau class `stretched-link` hilang. | Periksa `frontend.css` dan `kartu.blade.php`. |
| Ikon tidak tampil | Nama ikon salah ketik, atau CDN Bootstrap Icons tidak termuat. | Cek nama ikon di icons.getbootstrap.com dan koneksi internet. |

## 7. Perbedaan dengan modul (Laravel 10)

| Modul | Project ini |
|---|---|
| Kartu produk ditulis langsung di beranda | Dijadikan partial `v_produk/kartu.blade.php` supaya bisa dipakai ulang di katalog. |
| Tombol "Pesan"/keranjang di kartu | Diganti tombol "Detail Produk". Pemesanan lewat WhatsApp di halaman detail. |
| `make:view` belum ada | Partial dibuat dengan `php artisan make:view v_produk.kartu`. |

## 8. Yang harus Anda ganti nanti

- [ ] **Paragraf pengenalan** di `v_beranda/index.blade.php`. Cari teks `[PLACEHOLDER — ganti dengan perkenalan singkat toko ...]`.
- [ ] **Foto toko** `public/images/foto-toko.svg`: ganti dengan foto asli.
- [ ] **Poin keunggulan** di blok `$keunggulan`: sesuaikan dengan layanan asli toko. Jangan menuliskan layanan yang tidak ada, misalnya "gratis antar".
- [ ] **Ikon kategori**: jika menambah kategori baru dari admin, tambahkan ikonnya di daftar `$ikonKategori` (opsional).
