# Tahap 8 — Layout Frontend

*Acuan: Modul Web Programming III, Minggu 1 (template E-Shop).*

## 1. Tujuan

Semua halaman publik (Beranda, Katalog, Detail, Tentang Kami, Kontak) memakai **satu kerangka yang sama**: navbar di atas dan footer di bawah. Kerangkanya cukup ditulis sekali di `v_layouts/app.blade.php`. Ini sama dengan yang dilakukan untuk halaman admin di Tahap 6.

Data toko (nama, slogan, WhatsApp, alamat, jam buka, peta) dikumpulkan di **satu tempat**, `config/toko.php`. Nilainya dibaca dari `.env`.

Template E-Shop dari modul tidak ditemukan karena folder `public/frontend/eshop` tidak ada. Sesuai aturan kerja, layout ini memakai **Bootstrap 5 dari CDN** dengan struktur Blade yang sama seperti di modul. Warna, huruf, dan gaya tampilan diatur di file **`public/css/frontend.css`** (lihat bagian 4.7).

Posisinya di MVC: semua pekerjaan tahap ini ada di bagian **View** dan **konfigurasi**. Controller tidak diubah.

## 2. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `config/toko.php` | **Baru.** Data toko, dibaca dari `.env`. |
| `.env`, `.env.example` | Kunci `TOKO_*` ditambahkan, berisi placeholder. |
| `resources/views/v_layouts/app.blade.php` | **Baru.** Layout frontend: navbar, slot hero, isi, footer. |
| `public/css/frontend.css` | **Baru** (desain ulang). Warna, huruf Inter, dan gaya semua halaman publik. |
| `public/images/logo.svg` | **Baru.** Logo sementara (placeholder). |
| `public/images/hero-material.svg` | **Baru.** Gambar hero sementara (placeholder). |
| `resources/views/v_beranda/index.blade.php` | Memakai layout dan mengisi `@section('hero')`. |
| `resources/views/v_produk/index.blade.php`, `detail.blade.php` | Memakai layout. |
| `resources/views/v_tentang/index.blade.php`, `v_kontak/index.blade.php` | Memakai layout. |
| `resources/views/v_tahap2_menu.blade.php` | **Dihapus.** Perannya diganti navbar. |
| `tests/Feature/LayoutFrontendTest.php` | **Baru.** 10 pengujian layout. |

## 3. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:config toko` | Membuat file kosong `config/toko.php`. |
| `php artisan make:view v_layouts.app` | Membuat file `resources/views/v_layouts/app.blade.php`. Tanda titik berarti folder. |
| `php artisan make:test LayoutFrontendTest --phpunit` | Membuat file pengujian. |
| `php artisan config:clear` | Menghapus cache konfigurasi supaya isi `.env` yang baru terbaca. |
| `php artisan view:clear` | Menghapus cache tampilan Blade. |
| `php artisan test` | Menjalankan semua pengujian. |

## 4. Penjelasan kode

### 4.1 `config/toko.php`: data toko di satu tempat

```php
'wa' => env('TOKO_WA', '6281234567890'),
```

`env('TOKO_WA', ...)` membaca nilai dari `.env`. Jika kunci itu tidak ada, Laravel memakai nilai kedua sebagai cadangan.

View **tidak boleh** memanggil `env()` secara langsung. View memanggil `config('toko.wa')`. Alasannya: setelah perintah `php artisan config:cache` (dipakai di server online), `env()` di luar folder `config/` selalu menghasilkan `null`.

Keuntungannya: ketika nomor WhatsApp berubah, cukup ubah **satu baris** di `.env`. Semua tombol WhatsApp di seluruh website ikut berubah.

### 4.2 Struktur layout

```
<header> NAVBAR (logo + Beranda, Produk, Tentang Kami, Kontak + tombol WhatsApp)
@hasSection('hero') @yield('hero') @endif   ← hanya beranda yang mengisi
<main> @yield('content') </main>
<footer> FOOTER (nama, slogan, menu, alamat, WhatsApp, jam buka) </footer>
```

### 4.3 Hero hanya di beranda

```blade
@hasSection('hero')
    @yield('hero')
@endif
```

- `@hasSection('hero')` bernilai *true* hanya jika halaman yang sedang dibuka menulis `@section('hero') ... @endsection`.
- Hanya `v_beranda/index.blade.php` yang menulis bagian itu, jadi halaman lain tidak menampilkan hero.

Ini sesuai catatan modul: banner hanya tampil di beranda.

### 4.4 Menu aktif

```blade
<a class="nav-link @if (request()->routeIs('produk.*')) active @endif" ...>Produk</a>
```

- `produk.*` cocok dengan `produk.all`, `produk.kategori`, dan `produk.detail`. Akibatnya menu "Produk" tetap tersorot saat pengunjung membuka detail produk.
- `aria-current="page"` memberi tahu pembaca layar (alat bantu tunanetra) halaman mana yang sedang dibuka.

### 4.5 Navbar responsif

Di HP, menu berubah menjadi tombol "hamburger" (☰) karena memakai `navbar-expand-lg` dan `collapse`. Fitur buka-tutup menu berasal dari JavaScript Bootstrap (`bootstrap.bundle.min.js`).

### 4.6 Aksesibilitas

- Tautan **"Lompat ke isi"** baru muncul ketika pengunjung menekan Tab. Pengguna keyboard bisa langsung melompat ke isi tanpa melewati menu.
- Logo memiliki `alt`.
- Ikon diberi `aria-hidden="true"` supaya tidak dibacakan oleh pembaca layar.

### 4.7 Desain tampilan: `public/css/frontend.css`

Konsep desainnya **minimalis, bersih, dan mudah dibaca**: ruang kosong yang cukup, elemen sederhana, dan foto produk yang jelas.

| Peran | Warna | Dipakai untuk |
|---|---|---|
| Latar utama | Putih `#FFFFFF` | Sebagian besar halaman |
| Area pendukung | Abu muda `#F4F6F9` | Hero, bagian berselang-seling, latar foto produk |
| Identitas & navigasi | Biru tua `#0F2C59` | Navbar, judul besar, harga, tautan |
| Footer | Biru tua gelap `#0A1F40` | Footer |
| Aksen tombol | Oranye `#C2410C` | Tombol aksi: Lihat Katalog, Cari, WhatsApp |

**Huruf:** Inter dari Google Fonts. Judul tebal (700–800) dengan jarak huruf sedikit rapat. Isi berukuran 16px dengan jarak baris 1,65 supaya nyaman dibaca. Harga memakai angka sejajar (`tabular-nums`).

Tiga hal penting di file ini:

1. **Variabel warna di satu tempat.** Semua warna ditulis sekali di `:root`, misalnya `--biru` dan `--oranye`. Untuk mengganti warna toko, cukup ubah satu baris.
2. **Menimpa variabel Bootstrap.** Contohnya `--bs-body-font-family` (huruf), `--bs-secondary-color` (warna `.text-muted`), dan `--bs-light-rgb` (warna `.bg-light`). Dengan cara ini, komponen Bootstrap yang sudah ada otomatis ikut desain baru tanpa mengubah HTML-nya.
3. **Urutan memuat.** `frontend.css` dimuat **setelah** `bootstrap.min.css` di layout. CSS yang dimuat belakangan menang, sehingga gaya kita bisa menimpa gaya Bootstrap.

**Kontras warna (aksesibilitas):** semua pasangan warna memenuhi standar WCAG, yaitu minimal 4,5 : 1 untuk teks biasa.

| Pasangan | Kontras |
|---|---|
| Teks putih di tombol oranye `#C2410C` | 5,18 : 1 |
| Teks abu `#5A6472` di latar abu muda | 5,54 : 1 |
| Biru tua di latar putih (harga, tautan) | 13,77 : 1 |

Jika warna diganti, cek kontrasnya dulu di webaim.org/resources/contrastchecker.

Class buatan sendiri yang dipakai di view:

| Class | Fungsi |
|---|---|
| `navbar-toko` | Navbar biru tua. Menu aktif ditandai garis oranye di bawahnya. |
| `btn-toko` / `btn-garis` | Tombol utama oranye / tombol kedua bergaris biru. |
| `judul-besar`, `judul-bagian` | Ukuran judul hero dan judul tiap bagian. |
| `bagian`, `bagian-abu` | Jarak atas-bawah bagian halaman, dan latar abu muda. |
| `kartu-produk`, `kotak-kategori` | Kartu produk dan kotak kategori (dijelaskan di Tahap 9). |
| `harga` | Harga tebal berwarna biru tua. |

Fokus keyboard dibuat jelas dengan garis oranye (`:focus-visible`). Animasi dimatikan untuk pengguna yang memilih "kurangi gerakan" (`prefers-reduced-motion`).

### 4.8 Bagian e-commerce yang tidak dibuat

Template E-Shop di modul memiliki fitur toko online. Website ini hanya **katalog**, jadi fitur berikut tidak dibuat:

| Bagian template E-Shop | Alasan tidak dipakai | Penggantinya |
|---|---|---|
| Ikon keranjang belanja | Tidak ada checkout. Pemesanan lewat WhatsApp. | Tombol oranye **"Chat WhatsApp"** (dengan ikon WhatsApp) di navbar |
| Wishlist (daftar keinginan) | Butuh akun pelanggan, dan katalog tidak memakai akun. | — |
| Menu akun / login pelanggan | Tidak ada akun pelanggan (Modul III Minggu 3–5 dilewati). | — |
| Pilihan mata uang | Semua harga memakai Rupiah. | Format `Rp. 68.000` |
| Ongkir / checkout | Tidak ada pengiriman atau pembayaran lewat sistem. | — |

## 5. Cara menguji di browser

1. Pastikan server berjalan (`php artisan serve`), lalu buka `http://localhost:8000`.
2. **Beranda** menampilkan navbar biru tua, hero berlatar abu muda (judul besar di kiri, gambar di kanan) dengan tombol oranye "Lihat Katalog", dan footer biru tua.
3. Klik **Produk**, **Tentang Kami**, dan **Kontak**. Navbar dan footer tetap sama, hero **tidak** muncul, dan menu yang sedang dibuka ditandai garis oranye di bawahnya.
4. Perkecil jendela browser atau tekan F12, lalu pilih mode HP. Menu berubah menjadi tombol ☰.
5. Klik **Chat WhatsApp**. Browser membuka `https://wa.me/6281234567890` (nomor contoh).
6. **Coba ubah data toko:** di `.env`, ganti `TOKO_ALAMAT="Jl. Contoh No. 1"`, jalankan `php artisan config:clear`, lalu muat ulang halaman. Alamat di footer langsung berubah.

## 6. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| Perubahan `.env` tidak terlihat | Konfigurasi masih ter-cache. | `php artisan config:clear` |
| `View [v_layouts.app] not found` | Nama atau folder layout salah ketik. | Pastikan file ada di `resources/views/v_layouts/app.blade.php`. |
| Hero muncul di semua halaman | Hero ditulis langsung di layout, bukan lewat `@section('hero')`. | Pindahkan hero ke `@section('hero')` di halaman beranda. |
| Tampilan polos tanpa gaya | Internet mati, sehingga CDN Bootstrap tidak termuat. | Sambungkan internet, atau unduh Bootstrap ke `public/`. |
| Perubahan di `frontend.css` tidak terlihat | Browser masih memakai file CSS lama dari cache. | Tekan Ctrl+F5. |
| Gaya `frontend.css` kalah oleh Bootstrap | File dimuat **sebelum** Bootstrap. | Letakkan `<link>` ke `frontend.css` setelah `bootstrap.min.css`. |
| Huruf tetap Arial/Segoe, bukan Inter | Google Fonts tidak termuat (offline). | Website tetap jalan memakai huruf cadangan. Sambungkan internet. |
| Tombol ☰ tidak membuka menu | `bootstrap.bundle.min.js` tidak termuat. | Periksa tag `<script>` di bagian bawah layout. |
| Nilai `.env` dengan spasi terpotong | Nilai tidak diberi tanda kutip. | Tulis `TOKO_ALAMAT="Jl. Mawar No. 5"`. |

## 7. Perbedaan dengan modul (Laravel 10)

| Modul | Project ini (Laravel 13) |
|---|---|
| Menyalin template E-Shop ke `public/frontend` | Template tidak ada, jadi memakai Bootstrap 5 dari CDN + `public/css/frontend.css` untuk desain sendiri. |
| File layout dibuat manual | Memakai `php artisan make:view v_layouts.app` (perintah ini belum ada di Laravel 10). |
| `config/toko.php` dibuat manual | Memakai `php artisan make:config toko` (juga perintah baru). |
| `TOKO_MAPS_URL` berisi teks "(tautan Google Maps belum diisi)" | Dibiarkan **kosong**, karena teks biasa di dalam `href` menghasilkan tautan rusak. Halaman Kontak di Tahap 13 akan menampilkan keterangan "belum diisi" jika nilainya kosong. |
| Test memakai `/** @dataProvider */` | PHPUnit 12 memakai atribut `#[DataProvider('...')]`. |

## 8. Yang harus Anda ganti nanti

Semua nilai di bawah ada di file `.env`. Setelah mengubahnya, jalankan `php artisan config:clear`.

- [ ] `TOKO_SLOGAN`: slogan asli toko.
- [ ] `TOKO_WA`: nomor WhatsApp asli, ditulis dengan awalan 62 tanpa tanda `+` dan tanpa spasi.
- [ ] `TOKO_ALAMAT`: alamat lengkap.
- [ ] `TOKO_JAM_BUKA`: jam operasional.
- [ ] `TOKO_MAPS_URL` dan `TOKO_MAPS_EMBED`: diisi di Tahap 13.
- [ ] `public/images/logo.svg`: ganti dengan logo asli.
- [ ] `public/images/hero-material.svg`: ganti dengan foto asli. Jika formatnya JPG, sesuaikan juga nama file di `v_beranda/index.blade.php`.
