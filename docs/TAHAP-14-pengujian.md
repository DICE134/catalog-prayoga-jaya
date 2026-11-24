# Tahap 14 — Pengujian dan Finishing

*Acuan: Modul Web Programming II, Minggu 5 (pengujian unit) dan prinsip frontend Modul III Minggu 1 (responsive, UX, accessibility).*

## 1. Tujuan

Memastikan website **benar**, **nyaman dipakai di semua ukuran layar**, **mudah diakses**, dan **siap diserahkan**. Pekerjaan tahap ini:

1. Feature test otomatis untuk semua fitur penting.
2. Pengecekan tampilan HP, tablet, dan desktop; atribut `alt`; kontras warna; dan ukuran tombol.
3. Memastikan semua teks berbahasa Indonesia dan harga berformat Rupiah.
4. Menulis `README.md`.
5. Menyusun checklist **"Yang harus saya ganti sebelum website dipakai"** (bagian 8).

## 2. Ruang lingkup (pengingat)

Website ini **katalog digital, bukan e-commerce**. Fitur keranjang, checkout, pembayaran online, pengiriman otomatis, manajemen stok, dan transaksi penjualan **sengaja tidak dibuat**. Pelanggan melihat katalog dan harga, lalu menghubungi toko lewat WhatsApp.

Batasan ini juga **diuji otomatis**:
- `AksesibilitasTest::test_tidak_ada_fitur_ecommerce` memeriksa bahwa kata "keranjang", "checkout", "wishlist", "bayar sekarang", "ongkir", dan "stok tersedia" tidak muncul di halaman publik mana pun.
- `php artisan route:list` tidak memiliki route cart, checkout, order, atau payment.

## 3. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `tests/Feature/AksesibilitasTest.php` | **Baru.** 36 pemeriksaan aksesibilitas, bahasa, format harga, dan ruang lingkup. |
| `lang/id.json` | **Baru.** Terjemahan teks pagination ("Showing … results" menjadi "Menampilkan … hasil"). |
| `resources/views/v_layouts/app.blade.php` | Warna toko digelapkan agar kontras memenuhi standar. Sejak desain ulang, semua gaya dipindah ke `public/css/frontend.css`. |
| `public/css/frontend.css` | **Desain ulang** (setelah Tahap 14): palet putih, abu muda, biru tua, dan oranye, dengan huruf Inter. Lihat Tahap 8 bagian 4.7. |
| `public/images/logo.svg`, `foto-toko.svg` | Warna disamakan dengan oranye toko (`#C2410C`). |
| `resources/views/welcome.blade.php` | **Dihapus.** Halaman bawaan Laravel yang tidak dipakai. |
| `README.md` | Ditulis ulang untuk proyek ini. |

## 4. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:test AksesibilitasTest --phpunit` | Membuat file pengujian. |
| `php artisan test` | Menjalankan **semua** pengujian. |
| `php artisan test --filter=KatalogTest` | Menjalankan pengujian satu file saja. |
| `php artisan route:list --except-vendor` | Menampilkan semua route buatan sendiri. |
| `php artisan view:clear` | Menghapus cache tampilan setelah mengubah layout. |

## 5. Pengujian otomatis

### 5.1 Hasil

**112 test, semuanya lulus (418 pemeriksaan, setelah desain ulang).**

| File test | Jumlah | Yang diuji |
|---|---|---|
| `ExampleTest` (Feature + Unit) | 2 | `/` dialihkan ke beranda |
| `KategoriCrudTest` | 11 | CRUD kategori, validasi, kategori berisi produk tidak bisa dihapus |
| `LoginTest` | 10 | Login, logout, akun nonaktif, batas percobaan, **tamu dialihkan ke login** |
| `ProdukCrudTest` | 13 | CRUD produk, unggah, ganti, dan hapus foto, validasi berbahasa Indonesia |
| `LayoutFrontendTest` | 10 | Navbar dan footer di semua halaman, data dari `config('toko')`, hero hanya di beranda |
| `BerandaTest` | 5 | **Beranda terbuka (200)**, 8 bagian, produk unggulan maksimal 6 |
| `KatalogTest` | 9 | **Hanya produk Publis, produk Blok tersembunyi, pencarian dan filter (juga digabung)**, pagination |
| `DetailProdukTest` | 7 | **404 untuk id tidak ada dan produk Blok**, tautan WhatsApp, produk serupa |
| `TentangTest` | 3 | Bagian halaman, kategori otomatis, data dari config |
| `KontakTest` | 6 | Data kontak, tiga kemungkinan tampilan peta, keamanan embed |
| `AksesibilitasTest` | 36 | Lihat 5.2 |

Semua test yang diwajibkan panduan sudah ada (ditandai **tebal** di tabel).

### 5.2 Isi `AksesibilitasTest`

Test ini menjalankan `DatabaseSeeder` (data contoh asli), lalu memeriksa **8 halaman publik**: beranda, katalog halaman 1 dan 2, katalog per kategori, hasil pencarian kosong, detail, tentang, dan kontak.

| Pemeriksaan | Alasan |
|---|---|
| `<html lang="id">` | Pembaca layar memakai lafal Bahasa Indonesia. |
| `<meta name="viewport">` | Tanpa ini, tampilan di HP mengecil seperti layar desktop. |
| Tepat satu `<h1>` per halaman | Struktur judul yang jelas untuk pembaca layar dan mesin pencari. |
| Semua `<img>` memiliki `alt` | Gambar bisa "dibacakan". `alt=""` hanya boleh untuk gambar di dalam tombol yang sudah memiliki `aria-label`. |
| `target="_blank"` wajib `rel="noopener"` | Keamanan: halaman baru tidak bisa mengendalikan tab asal. |
| `<iframe>` wajib memiliki `title` | Pembaca layar tahu isi bingkai peta. |
| Tidak ada kata e-commerce | Menjaga ruang lingkup katalog. |
| Harga tidak berupa angka mentah | Semua harga memakai format `Rp. 68.000`. |
| Pagination dan halaman 404 berbahasa Indonesia | Tidak ada teks Inggris yang tersisa. |

**Cara membaca DOM di test:** HTML diubah menjadi objek `DOMDocument`, lalu dicari dengan XPath. Contohnya `//img` berarti "semua tag img". Cara ini lebih teliti daripada mencari teks biasa.

### 5.3 Catatan penting: `$this->seed()` di `setUp()`

Awalnya test ini memakai `protected $seed = true;`. Akibatnya **24 test lain gagal**: data seeder tertinggal di database tes dan bentrok dengan data buatan test lain (misalnya kategori "Semen & Perekat" menjadi kembar).

Penyebabnya: `$seed = true` dijalankan **di luar** transaksi `RefreshDatabase`, sehingga datanya tidak dibatalkan. Solusinya, `$this->seed()` dipanggil di `setUp()` **setelah** `parent::setUp()`, yaitu di dalam transaksi, sehingga semua data contoh dihapus lagi setelah setiap test.

Pelajarannya: setiap test harus **mandiri** dan tidak boleh meninggalkan data untuk test lain.

## 6. Pemeriksaan responsif, aksesibilitas, dan bahasa

### 6.1 Kontras warna

Standar WCAG mensyaratkan teks biasa memiliki kontras **minimal 4,5 : 1** dengan latarnya.

**Pemeriksaan pertama (desain lama).** Pemeriksaan di tahap ini menemukan dua masalah:
- Tombol oranye `#d35400` dengan teks putih hanya **4,17 : 1**.
- Teks abu-abu di latar abu muda hanya **4,45 : 1**.

Keduanya sempat diperbaiki dengan menggelapkan warnanya. Pelajarannya: warna yang "terlihat jelas" belum tentu lolos standar, jadi kontras harus **dihitung**.

**Desain sekarang (setelah desain ulang).** Palet baru dipilih dengan mempertimbangkan kontras sejak awal. Hasil hitungnya:

| Pasangan warna | Dipakai di | Kontras | Hasil |
|---|---|---|---|
| Teks putih di oranye `#C2410C` | Tombol Lihat Katalog, Cari, WhatsApp | 5,18 | ✅ |
| Teks putih di oranye hover `#9A3412` | Tombol saat disorot | 7,31 | ✅ |
| Teks putih di biru tua `#0F2C59` | Navbar, filter kategori aktif | 13,77 | ✅ |
| Teks putih di biru tua gelap `#0A1F40` | Footer | 16,37 | ✅ |
| Biru tua `#0F2C59` di putih | Harga, tautan, judul hero | 13,77 | ✅ |
| Teks redup `#5A6472` di putih | Keterangan (`.text-muted`) | 6,00 | ✅ |
| Teks redup `#5A6472` di abu muda `#F4F6F9` | Keterangan di bagian abu | 5,54 | ✅ |

Warna teks redup diatur lewat `--bs-secondary-color` di `public/css/frontend.css`, sehingga semua `.text-muted` otomatis memakainya tanpa aturan khusus untuk latar abu. Jika warna toko ingin diganti, ubah variabel `--oranye` atau `--biru` di file itu, lalu cek kontrasnya di webaim.org/resources/contrastchecker.

### 6.2 Responsif: dicek dari kode, **perlu Anda lihat sendiri di browser**

Tata letak di bawah sudah dirancang dan diperiksa dari kode. Tampilan visualnya belum saya lihat langsung, karena saya tidak membuka browser. Cara mengeceknya: tekan **F12**, lalu **Ctrl+Shift+M** (mode perangkat), lalu pilih ukuran layar.

| Bagian | HP (< 576 px) | Tablet (≥ 768 px) | Desktop (≥ 992 px) |
|---|---|---|---|
| Navbar | Tombol ☰ | Tombol ☰ | Menu terbuka |
| Hero beranda | Judul di atas, gambar di bawah | Judul di atas, gambar di bawah | Judul kiri, gambar kanan |
| Kartu produk (katalog) | 2 kolom | 2 kolom | 2 kolom, 3 kolom mulai 1200 px |
| Produk unggulan (beranda) | 2 kolom | 3 kolom | 3 kolom |
| Filter kategori | Tombol "Filter Kategori" | Tombol "Filter Kategori" | Daftar di kiri |
| Kategori di beranda | 1 kolom (2 kolom mulai 576 px) | 2 kolom | 4 kolom |
| Detail produk | Foto di atas info | Foto dan info berdampingan | Berdampingan |
| Peta Kontak | Mengecil proporsional (`ratio-4x3`) | Sama | Kolom kanan |
| Admin | Sidebar menjadi menu geser | Menu geser | Sidebar tetap |

Daftar yang perlu Anda cek:
- [ ] Buka setiap halaman di ukuran **iPhone SE (375 px)**. Tidak boleh ada geser ke samping (scroll horizontal).
- [ ] Tombol mudah ditekan dengan jempol. Tombol utama (WhatsApp, Cari, Lihat Katalog) memakai ukuran besar atau lebar penuh.
- [ ] Tekan **Tab** berulang kali dari awal halaman. Muncul tautan "Lompat ke isi", dan setiap tombol atau tautan terlihat jelas saat terpilih.

### 6.3 Bahasa Indonesia dan format Rupiah

| Bagian | Sumber teks |
|---|---|
| Pesan validasi | `lang/id/validation.php` |
| Pagination | `lang/id/pagination.php` + `lang/id.json` (baru) |
| Login dan autentikasi | `lang/id/auth.php` |
| DataTables admin | Pengaturan `language` di layout admin |
| Halaman 404 | `resources/views/errors/404.blade.php` |
| Harga | `number_format($harga, 0, ',', '.')` dengan awalan `Rp.` di kartu, detail, dan admin |

## 7. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| Banyak test gagal karena `UNIQUE constraint` | Data dari test lain tertinggal (misalnya `$seed = true`). | Panggil `$this->seed()` di `setUp()`. |
| `php artisan test` mengubah data di phpMyAdmin | `phpunit.xml` tidak memakai SQLite. | Pastikan ada `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:` di `phpunit.xml`. |
| Pagination masih "Showing … results" | `lang/id.json` belum ada, atau `APP_LOCALE` bukan `id`. | Periksa kedua hal itu. |
| Tampilan berubah setelah mengubah layout, tetapi tidak di browser | Cache tampilan atau cache browser. | `php artisan view:clear` dan Ctrl+F5. |

## 8. Yang harus saya ganti sebelum website dipakai

### A. Data toko di `.env`

Setelah mengubah `.env`, jalankan `php artisan config:clear`.

- [ ] `TOKO_SLOGAN`: masih "(Slogan toko belum diisi)".
- [ ] `TOKO_JAM_BUKA`: masih "(Jam operasional belum diisi)".
- [ ] `TOKO_MAPS_EMBED` (opsional): kode "Sematkan peta" dari Google Maps.
- [x] `TOKO_WA`: sudah diisi.
- [x] `TOKO_ALAMAT`: sudah diisi.
- [x] `TOKO_MAPS_URL`: sudah diisi.

### B. Teks placeholder di view

Cari kata **`PLACEHOLDER`** di VS Code (Ctrl+Shift+F).

- [ ] `resources/views/v_beranda/index.blade.php`: paragraf perkenalan toko.
- [ ] `resources/views/v_beranda/index.blade.php`: 4 poin "Kenapa Belanja di Sini?" (blok `$keunggulan`). Sesuaikan dengan layanan asli.
- [ ] `resources/views/v_tentang/index.blade.php`: sejarah singkat.
- [ ] `resources/views/v_tentang/index.blade.php`: profil toko.
- [ ] `resources/views/v_tentang/index.blade.php`: pengambilan dan pengiriman. Jika toko tidak mengantar barang, ubah menjadi "Ambil di toko" atau hapus kartunya.

### C. Gambar di `public/images/`

- [ ] `logo.svg`: logo asli toko.
- [ ] `hero-material.svg`: foto material atau toko untuk banner beranda. Jika memakai `.jpg`, ubah nama file di `v_beranda/index.blade.php`.
- [ ] `foto-toko.svg`: foto depan toko (dipakai di Beranda dan Tentang Kami).
- [ ] `produk-placeholder.svg` boleh tetap ada. Gambar ini otomatis tampil untuk produk yang belum memiliki foto.

### D. Data produk (lewat halaman admin)

- [ ] Ganti **harga** dan **merek** 12 produk contoh dengan data asli. Di seeder ditandai `// CONTOH — ganti dengan data asli`.
- [ ] Unggah **foto asli** setiap produk (menu Produk, tombol Ubah).
- [ ] Hapus produk contoh yang tidak dijual, dan tambahkan produk asli.
- [ ] Atur produk **Unggulan** (maksimal 6 yang tampil di beranda).
- [ ] Periksa daftar **kategori**, lalu tambah atau ubah sesuai barang yang dijual.

### E. Keamanan (wajib sebelum online)

- [ ] **Ganti akun admin sementara.** Email `admin@prayogajaya.test` dan password `admin12345` diketahui siapa pun yang membaca README. Halaman ubah password belum dibuat, jadi cara paling mudah adalah lewat Tinker:
  ```bash
  php artisan tinker
  >>> $u = App\Models\User::first(); $u->email = 'email-asli@contoh.com'; $u->password = 'password-baru-yang-kuat'; $u->save();
  ```
  Password otomatis di-hash karena cast `'hashed'` di model `User`.
- [ ] Di server online, ubah di `.env`: `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL=https://domain-anda`.
- [ ] Di server online, jalankan `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`, dan `php artisan storage:link`.

### F. Opsional (tidak wajib untuk katalog)

- [ ] Halaman ubah password admin.
- [ ] Laporan daftar produk dalam format PDF (Modul II Minggu 12, dilewati sesuai panduan).
- [ ] Mengunduh Bootstrap ke `public/` supaya tampilan tidak bergantung pada internet (CDN).

## 9. Perbedaan dengan modul (Laravel 10)

| Modul | Project ini |
|---|---|
| Pengujian contoh sederhana | 112 test, termasuk pemeriksaan aksesibilitas otomatis memakai DOM dan XPath. |
| `@dataProvider` di komentar | PHPUnit 12 memakai atribut `#[DataProvider('...')]`. |
| `make:test` membuat test Pest atau PHPUnit | Memakai `--phpunit` agar sesuai modul. |
| Terjemahan JSON tidak dibahas | `lang/id.json` diperlukan untuk teks pagination Laravel 11 ke atas. |
