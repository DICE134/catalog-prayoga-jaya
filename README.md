# Website Katalog — Toko Material Prayoga Jaya

Website katalog produk untuk toko material bangunan. Pelanggan melihat produk dan harganya, lalu bertanya atau memesan langsung ke toko lewat **WhatsApp**. Pemilik toko mengelola kategori dan produk (beserta fotonya) dari halaman admin.

Proyek ini mengikuti urutan Modul Web Programming II (backend) dan III (frontend), tetapi dibuat dengan **Laravel 13**, bukan Laravel 10. Perbedaannya dicatat di setiap file `docs/TAHAP-XX-*.md`.

## Ruang lingkup

Website ini adalah **katalog digital, bukan aplikasi e-commerce**. Fitur berikut **sengaja tidak dibuat**:

| Tidak ada | Penggantinya |
|---|---|
| Keranjang belanja, checkout, transaksi penjualan | Tombol WhatsApp berisi nama produk |
| Pembayaran online (Midtrans) | Pembayaran langsung di toko |
| Pengiriman otomatis dan ongkir (RajaOngkir) | Ditanyakan lewat WhatsApp |
| Manajemen stok | Pelanggan menanyakan ketersediaan lewat WhatsApp |
| Akun atau login pelanggan | Tidak diperlukan, karena katalog terbuka untuk umum |

**Halaman publik:** Beranda, Katalog Produk (cari, filter kategori, pagination), Detail Produk, Tentang Kami, Kontak.

**Halaman admin** (`/backend/login`): dashboard, kelola kategori, dan kelola produk beserta foto utama dan foto tambahan.

## Teknologi

- Laravel 13, PHP 8.3 atau lebih baru, MySQL 8 (Laragon)
- Blade, Eloquent ORM, validasi berbahasa Indonesia (`lang/id`)
- Bootstrap 5.3, Bootstrap Icons, dan DataTables (halaman admin), dimuat lewat CDN
- Huruf Inter dari Google Fonts

Halaman memuat Bootstrap dan huruf dari CDN, jadi tampilannya membutuhkan koneksi internet.

## Tampilan

Website publik memakai desain **minimalis, bersih, dan mudah dibaca**. Semua gaya ada di satu file, `public/css/frontend.css`.

| Peran | Warna |
|---|---|
| Latar utama | Putih `#FFFFFF` |
| Area pendukung, latar foto produk | Abu muda `#F4F6F9` |
| Identitas: navbar, judul, harga, tautan | Biru tua `#0F2C59` |
| Tombol aksi: Lihat Katalog, Cari, WhatsApp | Oranye `#C2410C` |

- **Huruf:** Inter. Judul tebal, isi 16px.
- **Kontras:** semua pasangan warna memenuhi standar WCAG minimal 4,5 : 1.
- **Ganti warna:** ubah variabel `--biru` atau `--oranye` di bagian atas `frontend.css`.

Penjelasan lengkapnya ada di `docs/TAHAP-08-layout-frontend.md` bagian 4.7. Halaman admin memakai gaya Bootstrap bawaan.

## Cara menjalankan

1. **Pasang dependensi**
   ```bash
   composer install
   ```
2. **Siapkan file `.env`**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Buat database** `db_prayogajaya_katalog` di phpMyAdmin (`http://localhost/phpmyadmin`), lalu sesuaikan `DB_USERNAME` dan `DB_PASSWORD` di `.env`.
4. **Buat tabel dan isi data contoh**
   ```bash
   php artisan migrate --seed
   ```
5. **Tautkan folder foto produk**, supaya foto yang diunggah bisa dibuka di browser:
   ```bash
   php artisan storage:link
   ```
6. **Jalankan server**
   ```bash
   php artisan serve
   ```
   Buka `http://localhost:8000`.

**Menjalankan pengujian:** `php artisan test`. Pengujian memakai database SQLite sementara di memori, sehingga data MySQL tidak tersentuh.

## Akun admin sementara

| Email | Password |
|---|---|
| `admin@prayogajaya.test` | `admin12345` |

**Ganti password ini sebelum website dipakai.** Lihat checklist di `docs/TAHAP-14-pengujian.md`.

## Data toko (placeholder)

Semua data toko ada di `.env` dan dibaca oleh `config/toko.php`. Setelah mengubah `.env`, jalankan `php artisan config:clear`.

| Kunci `.env` | Isi | Dipakai di |
|---|---|---|
| `TOKO_NAMA` | Nama toko | Navbar, footer, judul halaman |
| `TOKO_SLOGAN` | Slogan | Hero beranda, footer |
| `TOKO_WA` | Nomor WhatsApp, format `62...` tanpa `+` | Semua tombol WhatsApp |
| `TOKO_ALAMAT` | Alamat lengkap | Footer, Tentang Kami, Kontak |
| `TOKO_JAM_BUKA` | Jam operasional | Footer, Tentang Kami, Kontak |
| `TOKO_MAPS_URL` | Tautan Google Maps | Tombol "Buka di Google Maps" |
| `TOKO_MAPS_EMBED` | Kode "Sematkan peta" Google Maps (opsional) | Peta di halaman Kontak |

Daftar lengkap yang harus diganti (teks, gambar, data contoh) ada di **`docs/TAHAP-14-pengujian.md`**, bagian *Yang harus saya ganti sebelum website dipakai*.

## Struktur folder penting

```
app/
├── Http/Controllers/   BerandaController, ProdukController, TentangController,
│                       KontakController, KategoriController, LoginController
├── Models/             User, Kategori, Produk, FotoProduk
└── Providers/          AppServiceProvider (pagination Bootstrap 5)
bootstrap/app.php       Pengalihan login (pengganti Http/Kernel.php di Laravel 10)
config/toko.php         Data toko, dibaca dari .env
database/
├── migrations/         Tabel users, kategori, produk, foto_produk
├── factories/          Data palsu untuk pengujian
└── seeders/            Akun admin, 8 kategori, dan 12 produk contoh
docs/                   Catatan belajar TAHAP-01 sampai TAHAP-14
lang/id/, lang/id.json  Teks Bahasa Indonesia (validasi, pagination)
public/css/frontend.css Gaya website publik (warna, huruf, tata letak)
public/images/          Placeholder: logo, hero, foto toko, foto produk
resources/views/
├── v_layouts/app.blade.php          Layout website publik
├── v_beranda/, v_produk/, v_tentang/, v_kontak/
├── errors/404.blade.php             Halaman "tidak ditemukan"
└── backend/                         Layout dan halaman admin
routes/web.php          Semua route (publik dan /backend)
storage/app/public/img-produk/       Foto produk yang diunggah admin
tests/Feature/          Pengujian otomatis
```

## Database

| Tabel | Isi |
|---|---|
| `users` | Akun admin (`role`: 1 super admin, 0 admin; `status`: 1 aktif, 0 nonaktif) |
| `kategori` | Nama kategori |
| `produk` | Kategori, nama, merek, spesifikasi, satuan, harga (Rupiah), deskripsi, foto, status (1 Publis, 0 Blok), unggulan |
| `foto_produk` | Foto tambahan per produk |

Relasinya: `Kategori` hasMany `Produk`, `Produk` belongsTo `Kategori`, dan `Produk` hasMany `FotoProduk`.
