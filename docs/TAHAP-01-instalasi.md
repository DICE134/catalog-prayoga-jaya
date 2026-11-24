# Tahap 1 — Instalasi dan Konfigurasi Laravel

*Acuan: Modul Web Programming II, Minggu 1.*

## Tujuan

Menyiapkan fondasi project: Laravel terhubung ke database MySQL, nama aplikasi sudah benar, bahasa diatur ke Indonesia, dan zona waktu ke WIB (Asia/Jakarta). Di tahap ini belum ada halaman atau fitur baru. Yang diubah hanya **konfigurasi**.

> **Catatan versi:** project ini memakai **Laravel 13**, sedangkan modul memakai **Laravel 10**. Perbedaan yang berpengaruh ke tahap-tahap berikutnya dirangkum di bagian [Perbedaan Laravel 10 (modul) dan Laravel 13 (project ini)](#perbedaan-laravel-10-modul-dan-laravel-13-project-ini).

---

## 1. Memeriksa alat yang dibutuhkan

| Perintah | Hasil di komputer ini | Fungsinya |
|---|---|---|
| `php -v` | PHP 8.3.30 | PHP adalah bahasa pemrograman yang menjalankan Laravel. Laravel 13 butuh PHP 8.3 atau lebih baru. |
| `composer -V` | Composer 2.10.2 | Composer adalah "pengelola paket" PHP. Composer mengunduh Laravel dan library lain ke folder `vendor/`, mirip toko aplikasi untuk kode. |
| MySQL di Laragon | MySQL 8.4.3, menyala | MySQL adalah server database tempat menyimpan data (produk, kategori, admin). Nyalakan lewat tombol **Start All** di Laragon. |

## 2. Membuat project

Modul memakai perintah:

```bash
composer create-project laravel/laravel:^10.0 TokoPrayogaJaya
```

Langkah ini **dilewati**, karena folder `C:\Laravel\file-starter` sudah berisi project Laravel 13 yang dibuat dengan perintah serupa (`composer create-project laravel/laravel`). Kita memutuskan untuk melanjutkan di Laravel 13.

## 3. Membuat database dan mengatur `.env`

### Membuat database

Nama database diubah dari modul menjadi **`db_prayogajaya_katalog`**. Alasannya, database `db_prayogajaya` sudah dipakai project Anda yang lain (berisi tabel `input_barang`, `stok`, `laporan`, dan lainnya). Kalau database itu dipakai juga, perintah `migrate:fresh` di Tahap 3 akan menghapus data lama tersebut.

Database dibuat lewat terminal:

```bash
mysql -uroot -e "CREATE DATABASE db_prayogajaya_katalog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

Cara lain lewat phpMyAdmin: Laragon → **Database** (atau buka `http://localhost/phpmyadmin`) → **New** → isi nama `db_prayogajaya_katalog` → collation `utf8mb4_unicode_ci` → **Create**.

> `utf8mb4` artinya database bisa menyimpan semua karakter, termasuk huruf beraksen dan emoji.

### Mengatur `.env`

`.env` adalah file **pengaturan rahasia per komputer** (password database, kunci enkripsi). File ini tidak ikut dibagikan ke orang lain. Yang dibagikan adalah `.env.example` sebagai contoh.

Bagian yang diubah:

```env
APP_NAME="Toko Material Prayoga Jaya"   # nama aplikasi, muncul di <title> dan email

APP_LOCALE=id          # bahasa utama: Indonesia
APP_FALLBACK_LOCALE=en # jika terjemahan Indonesia belum ada, pakai bahasa Inggris
APP_FAKER_LOCALE=id_ID # data palsu (untuk seeder) bergaya Indonesia

DB_CONNECTION=mysql              # jenis database (sebelumnya sqlite)
DB_HOST=127.0.0.1                # alamat server MySQL (komputer sendiri)
DB_PORT=3306                     # port bawaan MySQL
DB_DATABASE=db_prayogajaya_katalog
DB_USERNAME=root                 # user bawaan Laragon
DB_PASSWORD=                     # Laragon: root tanpa password
```

> Nama `APP_NAME` mengandung spasi, jadi **wajib diapit tanda kutip**. Tanpa kutip, Laravel akan error saat membaca `.env`.

`.env.example` juga disamakan (tanpa data rahasia) supaya teman atau dosen yang menyalin project tahu pengaturannya.

## 4. Kunci aplikasi, migrasi, dan menjalankan server

```bash
php artisan key:generate
```

Perintah ini membuat `APP_KEY` di `.env`, yaitu kunci acak untuk mengenkripsi *cookie* dan *session*. Tanpa kunci ini Laravel menampilkan error *"No application encryption key has been specified"*.

```bash
php artisan migrate
```

**Langkah tambahan, tidak ada di Tahap 1 modul.** Laravel 13 menyimpan *session* (data sementara pengunjung, misalnya status login) dan *cache* di **database**, sedangkan Laravel 10 menyimpannya di file. Karena itu tabel `sessions`, `cache`, dan `jobs` harus dibuat dulu. Kalau tidak, halaman langsung error. Perintah ini juga membuat tabel `users` bawaan, yang nanti diubah di Tahap 3.

```bash
php artisan serve
```

Perintah ini menjalankan server web kecil untuk pengembangan di `http://localhost:8000`. Biarkan terminalnya terbuka selama bekerja, dan tekan `Ctrl+C` untuk menghentikannya.

## 5. Bahasa dan zona waktu di `config/app.php`

```php
// Zona waktu Indonesia bagian Barat (WIB), dipakai untuk created_at, now(), dll.
'timezone' => 'Asia/Jakarta',

// Bahasa utama aplikasi: Indonesia. Nilainya dibaca dari APP_LOCALE di .env.
'locale' => env('APP_LOCALE', 'id'),

// Faker membuat data palsu (nama, alamat) bergaya Indonesia untuk seeder/test.
'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),
```

- **`timezone`:** tanpa pengaturan ini, waktu tersimpan dalam UTC (7 jam lebih lambat dari WIB). Akibatnya produk yang ditambah pukul 14.00 akan tercatat pukul 07.00.
- **`locale`:** di modul (Laravel 10) ditulis langsung `'locale' => 'id'`. Di Laravel 13, nilainya dibaca dari `.env` lewat `env('APP_LOCALE', 'id')`. Artinya: "ambil `APP_LOCALE` dari `.env`; kalau tidak ada, pakai `'id'`". Hasilnya sama.
- File terjemahan pesan validasi Bahasa Indonesia (folder `lang/id`) baru dibuat di **Tahap 7**.

---

## Struktur folder Laravel

Laravel memakai pola **MVC**:

- **Model:** mengurus data di database.
- **View:** tampilan yang dilihat pengunjung.
- **Controller:** "pengatur lalu lintas" yang menerima permintaan, mengambil data dari Model, lalu mengirimnya ke View.

```
file-starter/
├── app/                     ← Kode PHP utama aplikasi
│   ├── Http/Controllers/    ← CONTROLLER (contoh nanti: BerandaController, ProdukController)
│   ├── Models/              ← MODEL (User; nanti Kategori, Produk, FotoProduk)
│   └── Providers/           ← Pengaturan awal aplikasi (jarang diubah)
├── bootstrap/app.php        ← "Saklar utama": mendaftarkan route, middleware, penanganan error
├── config/                  ← File konfigurasi (app.php, database.php, ...; nanti toko.php)
├── database/
│   ├── migrations/          ← "Cetak biru" tabel database (Tahap 3)
│   ├── seeders/             ← Data awal/contoh yang dimasukkan ke tabel (Tahap 3)
│   └── factories/           ← Pembuat data palsu untuk testing
├── public/                  ← Satu-satunya folder yang bisa diakses langsung dari browser
│   └── index.php            ← Pintu masuk semua request
│                              (nanti: images/, frontend/, backend/ untuk template)
├── resources/
│   ├── views/               ← VIEW: file tampilan .blade.php (HTML + sintaks Blade)
│   ├── css/, js/            ← Sumber CSS/JS yang diolah Vite
├── routes/
│   ├── web.php              ← Daftar URL website → controller mana yang menanganinya
│   └── console.php          ← Perintah artisan buatan sendiri dan jadwal tugas
├── storage/                 ← Log error (storage/logs/laravel.log), cache, file upload
├── tests/                   ← Pengujian otomatis (Tahap 14)
├── vendor/                  ← Library dari Composer. JANGAN diedit.
├── .env                     ← Pengaturan rahasia per komputer
├── artisan                  ← Alat baris perintah Laravel (php artisan ...)
└── composer.json            ← Daftar library PHP yang dipakai
```

---

## Daftar file yang dibuat atau diubah

| File | Perubahan |
|---|---|
| `.env` | `APP_NAME`, `APP_KEY` (baru), `APP_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`, koneksi MySQL |
| `.env.example` | Disamakan dengan `.env` (tanpa `APP_KEY`) |
| `config/app.php` | `timezone`, `locale`, `faker_locale` + komentar |
| `docs/TAHAP-01-instalasi.md` | File ini |
| Database `db_prayogajaya_katalog` | Dibuat baru. Berisi tabel `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations` |

## Ringkasan perintah

| Perintah | Arti |
|---|---|
| `php -v` / `composer -V` | Cek versi PHP dan Composer |
| `CREATE DATABASE ...` (MySQL/phpMyAdmin) | Membuat database kosong |
| `php artisan key:generate` | Membuat kunci enkripsi aplikasi |
| `php artisan config:clear` | Menghapus cache konfigurasi supaya perubahan `.env` langsung terbaca |
| `php artisan migrate` | Membuat tabel-tabel dari folder `database/migrations` |
| `php artisan serve` | Menjalankan server pengembangan di port 8000 |
| `php artisan config:show app.timezone` | Melihat nilai konfigurasi yang sedang aktif |

---

## Cara menguji

1. Pastikan MySQL di Laragon menyala.
2. Di terminal VS Code jalankan `php artisan serve`.
3. Buka `http://localhost:8000`. Halaman sambutan Laravel harus muncul **tanpa error**, dan judul tab browser bertuliskan **Toko Material Prayoga Jaya**.
4. Cek konfigurasi lewat terminal:
   ```bash
   php artisan config:show app.timezone     # Asia/Jakarta
   php artisan config:show app.locale       # id
   php artisan db:show                      # Database: db_prayogajaya_katalog
   ```
5. Di phpMyAdmin, buka database `db_prayogajaya_katalog`. Harus ada tabel `users`, `sessions`, `cache`, dan `migrations`.

Hasil uji saat tahap ini dikerjakan: `GET /` → HTTP 200, judul halaman benar, zona waktu `Asia/Jakarta`, locale `id`, terhubung ke `db_prayogajaya_katalog`.

---

## Kesalahan umum dan cara memperbaikinya

| Pesan error | Penyebab | Solusi |
|---|---|---|
| `SQLSTATE[HY000] [2002] No connection could be made...` | MySQL belum menyala | Klik **Start All** di Laragon |
| `SQLSTATE[HY000] [1049] Unknown database` | Database belum dibuat atau namanya salah ketik | Buat database, lalu cocokkan dengan `DB_DATABASE` |
| `SQLSTATE[HY000] [1045] Access denied for user 'root'` | Password MySQL salah | Kosongkan `DB_PASSWORD=` (bawaan Laragon) atau isi password yang benar |
| `Base table or view already exists` saat `migrate` | Database sudah berisi tabel dari project lain | Pakai database baru (ini yang terjadi di tahap ini) |
| `Table '...sessions' doesn't exist` | Belum menjalankan `php artisan migrate` | Jalankan `php artisan migrate` |
| `No application encryption key has been specified` | `APP_KEY` kosong | `php artisan key:generate` |
| Perubahan `.env` tidak berpengaruh | Konfigurasi masih ter-cache | `php artisan config:clear` lalu restart `php artisan serve` |
| `Failed to listen on 127.0.0.1:8000` | Port 8000 sudah dipakai (server lama masih jalan) | Tutup terminal lama, atau `php artisan serve --port=8001` |

---

## Perbedaan Laravel 10 (modul) dan Laravel 13 (project ini)

Daftar ini dipakai sebagai acuan di tahap-tahap berikutnya.

| Di modul (Laravel 10) | Di project ini (Laravel 13) | Berpengaruh di |
|---|---|---|
| `composer create-project laravel/laravel:^10.0` | Project sudah ada (Laravel 13) | Tahap 1 |
| `'locale' => 'id'` ditulis langsung | `env('APP_LOCALE', 'id')` + `APP_LOCALE=id` di `.env` | Tahap 1 |
| Session/cache disimpan di file | Disimpan di database, perlu `php artisan migrate` sejak awal | Tahap 1 |
| `app/Http/Kernel.php` (daftar middleware) | Tidak ada. Middleware diatur di `bootstrap/app.php` | Tahap 5 (middleware `auth`) |
| `app/Http/Middleware/Authenticate.php` (atur redirect login) | Tidak ada. Redirect login diatur di `bootstrap/app.php` | Tahap 5 |
| `app/Console/Kernel.php` | Tidak ada. Diganti `routes/console.php` | Tidak dipakai |
| `routes/api.php` | Tidak ada (dibuat dengan `php artisan install:api` jika perlu) | Tidak dipakai |
| Folder `lang/` sudah ada | Tidak ada sampai `php artisan lang:publish` dijalankan (perintahnya sama dengan modul) | Tahap 7 |
| PHP 8.1+ | PHP 8.3+ | — |

Perintah `make:controller`, `make:model -m`, `make:migration`, `Route::resource`, Blade (`@extends`, `@section`, `@yield`), Eloquent, dan `Auth::attempt` **sama persis** dengan modul.
