# Prompt Claude Code — Website Katalog Toko Material Prayoga Jaya

Dokumen ini adalah **prompt kerja** untuk Claude Code di Visual Studio Code. Urutan pengerjaannya mengikuti dua modul kuliah Anda:

- **Modul Web Programming II** (Laravel 10, backend): instalasi, controller/route/view, database, CRUD, login, template admin, data master.
- **Modul Web Programming III** (frontend): template E-Shop, tampilan beranda dan produk, filter kategori, detail produk.

Bagian modul yang tidak relevan untuk katalog dilewati dan dijelaskan di Tahap 14.

---

## Cara memakai

1. Pastikan Laragon (PHP 8.1 atau lebih baru, MySQL) dan Composer sudah terpasang, sesuai Minggu 1 Modul II.
2. Buat folder kosong, misalnya `C:\Laravel10\TokoPrayogaJaya`, lalu buka folder itu di VS Code (File → Open Folder).
3. Simpan file ini di folder tersebut dengan nama `PROMPT-Claude-Code-Toko-Prayoga-Jaya.md`.
4. Buka terminal VS Code, jalankan `claude`, lalu tempel **Prompt Pembuka** di bawah.
5. Setelah setiap tahap selesai, baca penjelasannya dan jalankan hasilnya di browser. Jika sudah paham, ketik: `lanjut ke Tahap berikutnya`.

---

## Prompt Pembuka (tempel ke Claude Code)

```
Kamu adalah asisten pemandu belajar saya. Saya mahasiswa yang sedang mengerjakan
project website katalog untuk "Toko Material Prayoga Jaya" (toko material bangunan)
memakai Laravel 10.

Baca file PROMPT-Claude-Code-Toko-Prayoga-Jaya.md di folder ini sampai habis.
File itu berisi aturan kerja, spesifikasi, dan 14 tahap pengerjaan yang mengikuti
urutan Modul Web Programming II dan III. Ikuti urutannya persis.

Mulai dari Tahap 1 saja. Jangan lompat ke tahap berikutnya sebelum saya bilang "lanjut".
```

---

## A. Aturan kerja untuk Claude Code

Aturan ini berlaku di semua tahap.

1. **Satu tahap pada satu waktu.** Selesaikan satu tahap, tulis ringkasan, lalu berhenti dan tunggu saya mengetik "lanjut".
2. **Saya sedang belajar.** Sebelum menulis kode di suatu tahap, jelaskan dalam Bahasa Indonesia sederhana: apa yang akan dibuat, kenapa dibuat, dan di mana letaknya di struktur Laravel (MVC). Hindari istilah tanpa penjelasan.
3. **Jalankan lewat perintah Artisan** seperti di modul (`php artisan make:controller`, `make:model -m`, `make:migration`, dan seterusnya). Jangan membuat file Laravel secara manual jika ada perintah Artisan-nya, dan sebutkan perintah yang dipakai.
4. **Catat setiap tahap** ke file `docs/TAHAP-XX-nama-tahap.md` (XX = nomor tahap). Isinya:
   - tujuan tahap;
   - daftar file yang dibuat atau diubah;
   - perintah yang dijalankan beserta artinya;
   - penjelasan kode penting per bagian;
   - cara menguji hasilnya di browser;
   - kesalahan umum dan cara memperbaikinya.
5. **Beri komentar Bahasa Indonesia** pada kode penting (route, controller, query, relasi model) supaya mudah dipelajari.
6. **Jangan mengarang data toko.** Alamat, nomor WhatsApp, jam operasional, sejarah toko, dan harga produk belum saya berikan. Simpan semuanya sebagai **placeholder yang jelas** (lihat bagian C) dan beri tahu saya apa saja yang harus saya ganti.
7. **Foto produk dan foto toko:** buat gambar placeholder lokal di `public/images/` (SVG sederhana). Jangan mengunduh foto dari internet. Saya akan mengganti dengan foto asli.
8. **Selalu uji.** Di akhir tiap tahap jalankan `php artisan serve` (atau minta saya membuka `http://localhost:8000`) dan cek apakah ada error. Perbaiki sebelum melapor selesai.
9. **Jika template dari modul tidak ada**, jangan berhenti. Cek dulu `public/frontend/eshop` dan `public/backend`. Jika kosong, beri tahu saya, lalu pakai Bootstrap 5 via CDN dengan struktur Blade yang sama (`layouts/app.blade.php` + `@yield('content')`), supaya konsep modulnya tetap terpakai.
10. **Tanya dulu** jika ada keputusan yang mengubah arah project. Untuk hal kecil, pilih yang paling sederhana dan jelaskan alasannya.

---

## B. Ruang lingkup website

**Jenis:** website katalog produk. Pelanggan melihat produk dan harga, lalu bertanya atau memesan lewat **WhatsApp**. Tidak ada keranjang, ongkir, atau pembayaran online.

**Teknologi (sesuai modul):** Laravel 10, PHP 8.1+, MySQL (Laragon), Blade, route/controller/model Eloquent, Bootstrap, validasi Bahasa Indonesia, zona waktu Asia/Jakarta.

**Halaman publik:** Beranda, Katalog Produk, Detail Produk, Tentang Kami, Kontak.

**Halaman admin (hanya untuk pemilik toko):** login, kelola kategori, kelola produk beserta fotonya. Admin ini diperlukan supaya produk bisa ditambah tanpa mengedit kode, sebagaimana alur Modul II.

### Rincian isi setiap halaman publik

**A. Beranda (Home)**
Tujuan: memperkenalkan toko dan mengarahkan pelanggan ke katalog produk.
1. Navbar (logo dan menu navigasi)
2. Hero section (foto material bangunan dan slogan toko)
3. Pengenalan singkat Toko Material Prayoga Jaya
4. Kategori produk
5. Produk unggulan
6. Keunggulan atau layanan toko
7. Tombol menuju katalog
8. Footer (alamat dan kontak)

**B. Katalog Produk**
Tujuan: memudahkan pelanggan melihat produk yang tersedia.
1. Judul halaman katalog
2. Kolom pencarian produk
3. Filter kategori
4. Daftar produk dalam bentuk kartu
5. Harga setiap produk
6. Tombol detail produk

**C. Detail Produk**
Tujuan: memberikan informasi lebih lengkap mengenai produk yang dipilih.
- Foto produk, nama produk, merek, spesifikasi atau ukuran, harga, deskripsi singkat
- Tombol WhatsApp untuk bertanya atau melakukan pemesanan

**D. Tentang Kami**
Tujuan: membangun kepercayaan pelanggan terhadap toko.
- Sejarah singkat, profil toko, jenis material yang dijual, foto toko, informasi pelayanan

**E. Kontak**
Tujuan: memudahkan pelanggan menghubungi dan menemukan lokasi toko.
- Nomor WhatsApp, alamat toko, jam operasional, Google Maps atau tautan lokasi, tombol WhatsApp

---

## C. Data toko (placeholder yang harus saya ganti)

Simpan di `config/toko.php`, nilainya dibaca dari `.env`, sehingga cukup diganti di satu tempat. Semua view memanggil `config('toko.xxx')`.

| Kunci `.env` | Isi sementara | Dipakai di |
|---|---|---|
| `TOKO_NAMA` | Toko Material Prayoga Jaya | navbar, footer, judul halaman |
| `TOKO_SLOGAN` | (slogan sementara, saya ganti) | hero beranda |
| `TOKO_WA` | 6281234567890 | tombol WhatsApp (format internasional tanpa `+`) |
| `TOKO_ALAMAT` | (alamat belum diisi) | footer, kontak |
| `TOKO_JAM_BUKA` | (jam operasional belum diisi) | kontak |
| `TOKO_MAPS_URL` | (tautan Google Maps belum diisi) | kontak |
| `TOKO_MAPS_EMBED` | (kosong) | peta tertanam di kontak, opsional |

Tombol WhatsApp memakai pola `https://wa.me/{TOKO_WA}?text={pesan}`. Pada halaman detail produk, pesan otomatis berisi nama produk, misalnya "Halo Prayoga Jaya, saya ingin bertanya tentang {nama produk}". Gunakan `urlencode()` atau `rawurlencode()`.

**Data contoh (seeder).** Kategori dan produk di seeder hanya **contoh** supaya halaman terlihat hidup. Harga dan merek contoh harus ditandai dengan komentar `// CONTOH — ganti dengan data asli` dan saya ganti lewat halaman admin. Contoh kategori: Semen & Perekat, Cat & Pelapis, Besi & Baja, Pipa & Sanitasi, Keramik & Lantai, Kayu & Triplek, Perkakas, Listrik. Buat minimal 10 produk contoh (9 berstatus Publis, 1 berstatus Blok) supaya filter, pencarian, dan pagination bisa diuji.

---

## D. Rancangan database

Ikuti pola Modul II (migration, model, seeder, `$guarded`).

**users:** id, nama, email, password, role (1 = super admin, 0 = admin), status, timestamps.

**kategori:** id, nama_kategori.

**produk:** id, kategori_id (relasi ke kategori), nama_produk, merek, spesifikasi (ukuran atau tipe), satuan (misalnya sak, batang, kaleng, lembar), harga (integer rupiah), detail (deskripsi), foto, status (1 = Publis, 0 = Blok), unggulan (boolean, untuk bagian "Produk Unggulan" di beranda), timestamps.

**foto_produk:** id, produk_id, foto (foto tambahan per produk).

Relasi: `Produk belongsTo Kategori`, `Kategori hasMany Produk`, `Produk hasMany FotoProduk`.

---

## E. Tahap pengerjaan

Setiap tahap menunjuk bagian modul yang menjadi acuannya.

### Tahap 1 — Instalasi dan konfigurasi Laravel
*Acuan: Modul II, Minggu 1.*
1. Cek `php -v`, `composer -V`, dan pastikan MySQL di Laragon menyala. Jelaskan fungsi masing-masing.
2. Buat project: `composer create-project laravel/laravel:^10.0 TokoPrayogaJaya`. Jika folder sudah dibuka di VS Code, sesuaikan agar isinya berada di folder kerja.
3. Buat database `db_prayogajaya` di MySQL. Atur `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `APP_NAME`).
4. Jalankan `php artisan key:generate` dan `php artisan serve`, lalu buka `http://localhost:8000`.
5. Atur `config/app.php`: `locale` = `id`, `timezone` = `Asia/Jakarta`.
6. Tulis `docs/TAHAP-01-instalasi.md`, termasuk penjelasan struktur folder Laravel (`app`, `routes`, `resources/views`, `database`, `public`).

### Tahap 2 — Controller, route, dan view
*Acuan: Modul II, Minggu 2.*
1. Buat `BerandaController`, `ProdukController`, `TentangController`, dan `KontakController` dengan `php artisan make:controller`.
2. Isi `routes/web.php` dengan nama route: `beranda`, `produk.all`, `produk.kategori`, `produk.detail`, `tentang`, `kontak`. URL `/` dialihkan ke `beranda`.
3. Buat view sementara bertuliskan "Halaman ... berhasil terbuka" untuk membuktikan alur route → controller → view.
4. Jelaskan siklus request. Tulis `docs/TAHAP-02-controller-route-view.md`.

### Tahap 3 — Database: migration, model, seeder
*Acuan: Modul II, Minggu 3 dan Minggu 9–10 (data master dan seeder).*
1. Buat migration dan model: `Kategori`, `Produk`, `FotoProduk` (`make:model -m`). Ubah tabel `users` sesuai bagian D.
2. Isi model: `$table`, `$guarded`, dan relasi sesuai bagian D.
3. Buat seeder: 1 akun admin (email dan password sementara, saya ganti nanti), kategori contoh, dan produk contoh sesuai bagian C.
4. Jalankan `php artisan migrate:fresh --seed`. Tunjukkan cara memeriksa data di phpMyAdmin.
5. Tulis `docs/TAHAP-03-database.md`, termasuk penjelasan migration vs model vs seeder.

### Tahap 4 — Konsep CRUD
*Acuan: Modul II, Minggu 4.*
Sebelum membangun admin, jelaskan pola CRUD (index, create, store, edit, update, destroy) dan `Route::resource` lewat contoh kecil pada kategori:
1. Buat `KategoriController` dengan `--resource`.
2. Implementasikan tampil, simpan, ubah, dan hapus kategori, lengkap dengan validasi dan pesan `invalid-feedback`.
3. Tulis `docs/TAHAP-04-crud.md`.

### Tahap 5 — Autentikasi login admin
*Acuan: Modul II, Minggu 5.*
1. Buat `LoginController` (form login, proses `Auth::attempt`, logout) dan route `backend/login`, `backend/logout`.
2. Lindungi semua route `backend/*` dengan middleware `auth`.
3. Buat halaman `backend/beranda` sebagai dashboard sederhana setelah login.
4. Tulis `docs/TAHAP-05-login.md`. Jelaskan bahwa password disimpan ter-hash (`bcrypt`), bukan teks biasa.

### Tahap 6 — Template admin dan tabel data
*Acuan: Modul II, Minggu 6–7.*
1. Pasang layout admin `resources/views/backend/v_layouts/app.blade.php` (template Matrix Admin jika tersedia di `public/backend`; jika tidak, Bootstrap 5).
2. Pindahkan halaman login dan dashboard ke layout tersebut dengan `@extends`, `@section`, dan `@yield`.
3. Tampilkan data kategori dengan DataTable (cari, urut, pagination).
4. Tulis `docs/TAHAP-06-template-admin.md`.

### Tahap 7 — Kelola produk dan foto (data master dan join)
*Acuan: Modul II, Minggu 9–11.*
1. `ProdukController` resource di `backend/produk`: daftar, tambah, ubah, hapus.
2. Form produk memuat: kategori (dropdown dari tabel `kategori`), nama, merek, spesifikasi, satuan, harga, deskripsi, foto utama, status (Publis/Blok), dan penanda unggulan.
3. Unggah foto ke `storage/app/public/img-produk`, lalu jalankan `php artisan storage:link`. Validasi tipe dan ukuran file.
4. Foto tambahan lewat tabel `foto_produk`. Tampilkan nama kategori di daftar produk memakai relasi (`with('kategori')`).
5. Atur validasi dengan bahasa Indonesia (`php artisan lang:publish`, folder `lang/id`).
6. Tulis `docs/TAHAP-07-kelola-produk.md`.

### Tahap 8 — Layout frontend (template E-Shop)
*Acuan: Modul III, Minggu 1.*
1. Pasang template E-Shop dari `public/frontend/eshop` (salin `css`, `fonts`, `img`, `js` ke `public/frontend`). Jika tidak ada, pakai Bootstrap 5 dan beri tahu saya.
2. Buat `resources/views/v_layouts/app.blade.php` berisi **navbar** (logo dan menu: Beranda, Produk, Tentang Kami, Kontak) dan **footer** (alamat dan kontak dari `config('toko')`). Bagian isi memakai `@yield('content')`.
3. Banner atau hero hanya tampil di beranda, tidak di halaman lain (seperti catatan modul).
4. Hapus bagian e-commerce yang tidak dipakai dari template: keranjang, wishlist, akun, dan mata uang. Jelaskan apa yang dihapus dan kenapa.
5. Tulis `docs/TAHAP-08-layout-frontend.md`.

### Tahap 9 — Halaman Beranda
*Acuan: Modul III, Minggu 1–2 (`BerandaController@index`, `v_beranda/index.blade.php`).*
Bangun delapan bagian sesuai rincian A di atas, berurutan:
1. Navbar (dari layout).
2. Hero: gambar placeholder material bangunan, slogan dari `config('toko.slogan')`, dan tombol ke katalog.
3. Pengenalan singkat Toko Material Prayoga Jaya (teks sementara, saya ganti).
4. Kategori produk: ambil dari tabel `kategori`, tiap kategori menaut ke `produk.kategori`.
5. Produk unggulan: `Produk::where('status', 1)->where('unggulan', 1)`, maksimal 6, tampil sebagai kartu.
6. Keunggulan atau layanan toko: 3–4 poin dengan ikon, berupa teks umum yang aman diedit.
7. Tombol besar "Lihat Katalog" menuju `produk.all`.
8. Footer (dari layout).
Tulis `docs/TAHAP-09-beranda.md`.

### Tahap 10 — Halaman Katalog Produk
*Acuan: Modul III, Minggu 2 (`produkAll`, `produkKategori`, filter kategori, pagination kustom).*
1. Judul halaman katalog.
2. Kolom pencarian (`GET ?q=`): cari di `nama_produk` dan `merek` dengan `where ... like`.
3. Filter kategori di sidebar (menggantikan "Filter By Brand" dari template). Pencarian dan filter harus **bisa digabung**, dan pagination harus mempertahankannya lewat `->appends(request()->query())`.
4. Daftar produk berupa kartu: foto, nama, kategori, merek, dan **harga** (`Rp. {{ number_format($row->harga, 0, ',', '.') }}`).
5. Tombol "Detail Produk" menuju `route('produk.detail', $row->id)`.
6. Hanya produk berstatus Publis yang tampil. Tampilkan pesan ramah jika hasil kosong. Gunakan pagination (9 produk per halaman).
7. Tulis `docs/TAHAP-10-katalog.md`.

### Tahap 11 — Halaman Detail Produk
*Acuan: Modul III, Minggu 2 (`ProdukController@detail`, `v_produk/detail.blade.php`).*
1. Tampilkan foto utama dan foto tambahan, nama, merek, spesifikasi atau ukuran, harga, dan deskripsi.
2. Produk dengan status Blok atau id tidak ada menghasilkan halaman 404 (`firstOrFail`).
3. Tombol **WhatsApp** (hijau, ikon WhatsApp) dengan pesan otomatis berisi nama produk, seperti di bagian C. Ini menggantikan tombol "Pesan"/keranjang di template modul.
4. Tambahkan bagian "Produk Serupa" dari kategori yang sama, maksimal 4 produk (opsional).
5. Tulis `docs/TAHAP-11-detail-produk.md`.

### Tahap 12 — Halaman Tentang Kami
*Tidak ada di modul, memakai pola yang sama dengan halaman lain.*
Bagian: sejarah singkat, profil toko, jenis material yang dijual (ambil otomatis dari daftar kategori), foto toko (placeholder), dan informasi pelayanan. Semua teks sejarah dan profil adalah **placeholder** yang diberi tanda jelas, karena saya belum memberikan datanya. Tulis `docs/TAHAP-12-tentang-kami.md`.

### Tahap 13 — Halaman Kontak
*Tidak ada di modul, memakai pola yang sama dengan halaman lain.*
Bagian: nomor WhatsApp, alamat, jam operasional, Google Maps (iframe jika `TOKO_MAPS_EMBED` diisi, kalau tidak tampilkan tombol tautan lokasi), dan tombol WhatsApp. Semua nilai dari `config('toko')`. Tulis `docs/TAHAP-13-kontak.md`.

### Tahap 14 — Pengujian dan finishing
*Acuan: Modul II, Minggu 5 (pengujian unit) dan prinsip frontend Modul III Minggu 1: responsive, UX, accessibility.*
1. Tulis feature test sederhana (`php artisan make:test`) untuk: beranda terbuka (200), katalog menampilkan produk Publis dan menyembunyikan produk Blok, pencarian dan filter bekerja, detail produk 404 untuk id tidak ada, dan admin tanpa login dialihkan ke halaman login. Jalankan `php artisan test`.
2. Cek tampilan di lebar HP, tablet, dan desktop. Pastikan semua gambar punya atribut `alt`, kontras teks terbaca, dan tombol mudah ditekan di HP.
3. Pastikan semua teks tampil dalam Bahasa Indonesia dan harga berformat rupiah.
4. Buat `README.md`: cara menjalankan project, akun admin sementara, daftar placeholder yang harus diganti (lihat bagian C), dan struktur folder.
5. Tulis `docs/TAHAP-14-pengujian.md` dan sebuah checklist "Yang harus saya ganti sebelum website dipakai".

---

## F. Bagian modul yang sengaja dilewati

| Bagian modul | Alasan |
|---|---|
| Modul III Minggu 3: login/registrasi member Google | Katalog tidak butuh akun pelanggan. |
| Minggu 4–5: akun customer, route group customer | Sama, tidak ada akun pelanggan. |
| Minggu 6, 9, 10: keranjang, checkout, manajemen pesanan | Pemesanan lewat WhatsApp. |
| Minggu 7: ongkos kirim RajaOngkir | Tidak ada pengiriman lewat sistem. |
| Minggu 11: grafik pesanan | Tidak ada data pesanan. |
| Minggu 12: pembayaran Midtrans | Tidak ada pembayaran online. |
| Modul II Minggu 12: laporan | Opsional. Bisa ditambahkan nanti sebagai laporan daftar produk (PDF). |

Jika dosen mewajibkan salah satu bagian tersebut, beri tahu saya dan kita tambahkan sebagai tahap tambahan.

---

## G. Ringkasan struktur folder yang diharapkan

```
TokoPrayogaJaya/
├── app/
│   ├── Http/Controllers/   BerandaController, ProdukController, TentangController,
│   │                       KontakController, KategoriController, LoginController
│   └── Models/             User, Kategori, Produk, FotoProduk
├── config/toko.php         data toko (dibaca dari .env)
├── database/
│   ├── migrations/
│   └── seeders/DatabaseSeeder.php
├── docs/                   TAHAP-01 ... TAHAP-14 (catatan belajar)
├── public/
│   ├── frontend/           template E-Shop (css, fonts, img, js)
│   ├── backend/            template admin
│   └── images/             placeholder logo, hero, foto toko
├── resources/views/
│   ├── v_layouts/app.blade.php
│   ├── v_beranda/index.blade.php
│   ├── v_produk/index.blade.php, detail.blade.php
│   ├── v_tentang/index.blade.php
│   ├── v_kontak/index.blade.php
│   └── backend/ (v_layouts, v_login, v_beranda, v_kategori, v_produk)
├── routes/web.php
└── README.md
```
