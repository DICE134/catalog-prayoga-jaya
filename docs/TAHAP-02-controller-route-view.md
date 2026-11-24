# Tahap 2 — Controller, Route, dan View

*Acuan: Modul Web Programming II, Minggu 2 (nama method produk mengikuti Modul III).*

## Tujuan

Membuat "jalur" untuk semua halaman publik: **Beranda, Katalog, Katalog per Kategori, Detail Produk, Tentang Kami, Kontak**. Setiap halaman baru menampilkan tulisan *"Halaman ... berhasil terbuka"*. Tujuannya membuktikan bahwa alur **route → controller → view** sudah tersambung sebelum kita mengisinya dengan data asli.

---

## Konsep: siklus sebuah request

*Request* (permintaan) adalah pesan dari browser ke server: "tolong kirimkan halaman `/tentang`". Berikut perjalanannya di Laravel:

```
Browser: GET http://localhost:8000/tentang
   │
   ▼
1. public/index.php        ← pintu masuk SEMUA request
   │
   ▼
2. bootstrap/app.php       ← menyalakan Laravel, memuat daftar route dan middleware
   │
   ▼
3. routes/web.php          ← mencari baris yang cocok dengan URL "/tentang"
   │   Route::get('/tentang', [TentangController::class, 'index'])
   ▼
4. TentangController@index ← CONTROLLER: menyiapkan data ($judul = 'Tentang Kami')
   │   return view('v_tentang.index', ['judul' => ...]);
   ▼
5. resources/views/v_tentang/index.blade.php  ← VIEW: Blade diubah menjadi HTML
   │
   ▼
Browser menerima HTML → halaman tampil
```

Kalau di langkah 3 tidak ada route yang cocok, Laravel langsung membalas **404 Not Found**.

Di MVC:
- **Route** bukan bagian dari M, V, atau C. Ia adalah **peta alamat** yang menentukan controller mana yang dipanggil.
- **Controller (C)** adalah "otak" setiap halaman. Mulai Tahap 3, controller akan mengambil data dari **Model (M)**.
- **View (V)** hanya bertugas menampilkan. View tidak boleh mengambil data langsung dari database.

---

## Daftar file yang dibuat atau diubah

| File | Status | Isi |
|---|---|---|
| `app/Http/Controllers/BerandaController.php` | baru | method `index` |
| `app/Http/Controllers/ProdukController.php` | baru | method `produkAll`, `produkKategori`, `detail` |
| `app/Http/Controllers/TentangController.php` | baru | method `index` |
| `app/Http/Controllers/KontakController.php` | baru | method `index` |
| `resources/views/v_beranda/index.blade.php` | baru | view sementara |
| `resources/views/v_produk/index.blade.php` | baru | view sementara (katalog semua dan per kategori) |
| `resources/views/v_produk/detail.blade.php` | baru | view sementara |
| `resources/views/v_tentang/index.blade.php` | baru | view sementara |
| `resources/views/v_kontak/index.blade.php` | baru | view sementara |
| `resources/views/v_tahap2_menu.blade.php` | baru, **sementara** | menu tautan untuk mencoba route. Dihapus di Tahap 8. |
| `routes/web.php` | diubah | 7 route |
| `tests/Feature/ExampleTest.php` | diubah | test `/` disesuaikan: sekarang dicek dialihkan ke beranda, bukan lagi 200 |

## Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:controller BerandaController` | Membuat file controller kosong di `app/Http/Controllers/` (diulang untuk `ProdukController`, `TentangController`, `KontakController`) |
| `php artisan make:view v_beranda.index` | Membuat file view `resources/views/v_beranda/index.blade.php`. Titik berarti folder. |
| `php artisan route:list --except-vendor` | Menampilkan semua route buatan kita (URL, nama, controller) |
| `vendor/bin/pint` | Merapikan format kode PHP secara otomatis |
| `php artisan test` | Menjalankan test otomatis |

> **Beda dengan modul:** `php artisan make:view` baru ada sejak Laravel 11. Di modul (Laravel 10), file view dibuat manual (klik kanan → New File). Hasilnya sama.

---

## Penjelasan kode penting

### 1. Route (`routes/web.php`)

```php
// Alamat utama "/" langsung dialihkan (redirect) ke halaman beranda.
Route::get('/', function () {
    return redirect()->route('beranda');
});

Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
Route::get('/produk/all', [ProdukController::class, 'produkAll'])->name('produk.all');
Route::get('/produk/kategori/{id}', [ProdukController::class, 'produkKategori'])->name('produk.kategori');
Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])->name('produk.detail');
Route::get('/tentang', [TentangController::class, 'index'])->name('tentang');
Route::get('/kontak', [KontakController::class, 'index'])->name('kontak');
```

- `Route::get(...)` artinya route ini hanya melayani permintaan **GET**, yaitu membuka halaman lewat browser. Nanti untuk form ada `post`, `put`, dan `delete`.
- `[BerandaController::class, 'index']` artinya "panggil method `index` di kelas `BerandaController`". Baris `use App\Http\Controllers\...;` di atas file wajib ada supaya PHP tahu letak kelasnya.
- `{id}` adalah **parameter route**, yaitu bagian URL yang nilainya berubah-ubah. Pada `/produk/detail/5`, nilai `5` dikirim ke method sebagai `$id`.
- `->name('produk.detail')` memberi nama pada route. **Selalu gunakan nama route di view**, misalnya `route('produk.detail', 5)`, bukan menulis `/produk/detail/5` secara manual. Dengan begitu, kalau suatu hari URL diganti (misalnya menjadi `/barang/5`), cukup ubah `web.php` dan semua tautan ikut berubah.
- `redirect()->route('beranda')` membalas dengan kode **302** ("pindah ke alamat lain"), lalu browser otomatis membuka `/beranda`.

### 2. Controller

```php
public function index(): View
{
    return view('v_beranda.index', [
        'judul' => 'Beranda',
    ]);
}
```

- `view('v_beranda.index', [...])` artinya "tampilkan file `resources/views/v_beranda/index.blade.php`".
- Array `['judul' => 'Beranda']` adalah **data yang dikirim ke view**. Di view, kunci `'judul'` menjadi variabel `$judul`.
- `: View` adalah **tipe kembalian**, penanda bahwa method ini pasti mengembalikan tampilan. Ini membantu editor mendeteksi kesalahan. Di modul biasanya tidak ditulis, dan tidak menulisnya juga tetap jalan.

Method dengan parameter route:

```php
public function detail(string $id): View
{
    return view('v_produk.detail', [
        'judul' => 'Detail Produk ID '.$id,
    ]);
}
```

Nama `$id` harus **sama** dengan `{id}` di route. Di Tahap 11, `$id` ini dipakai untuk mencari produk di database.

`produkAll` dan `produkKategori` sengaja memakai **view yang sama** (`v_produk.index`). Tampilannya sama-sama daftar produk; bedanya hanya produk mana yang diambil.

### 3. View (Blade)

```blade
<title>{{ $judul }} — {{ config('app.name') }}</title>
...
<h1>Halaman {{ $judul }} berhasil terbuka</h1>

@include('v_tahap2_menu')
```

- `{{ $judul }}` menampilkan isi variabel. Blade otomatis **mengamankan** teks ini (*escape*), sehingga jika isinya berupa kode `<script>`, kode itu tidak akan dijalankan browser.
- `{{ config('app.name') }}` membaca `APP_NAME` dari `.env` yang diatur di Tahap 1.
- `{{-- ... --}}` adalah komentar Blade, dan tidak ikut terkirim ke browser.
- `@include('v_tahap2_menu')` menyisipkan isi file view lain. Ini mencegah menu yang sama ditulis ulang di 5 file. Di Tahap 8, cara ini diganti dengan **layout** (`@extends` dan `@yield`) yang lebih rapi.

---

## Cara menguji di browser

Jalankan `php artisan serve`, lalu buka:

| URL | Hasil yang diharapkan |
|---|---|
| `http://localhost:8000/` | Otomatis pindah ke `/beranda` |
| `http://localhost:8000/beranda` | **Halaman Beranda berhasil terbuka** |
| `http://localhost:8000/produk/all` | **Halaman Katalog Produk berhasil terbuka** |
| `http://localhost:8000/produk/kategori/1` | **Halaman Katalog Produk — Kategori ID 1 berhasil terbuka** |
| `http://localhost:8000/produk/detail/5` | **Halaman Detail Produk ID 5 berhasil terbuka** |
| `http://localhost:8000/tentang` | **Halaman Tentang Kami berhasil terbuka** |
| `http://localhost:8000/kontak` | **Halaman Kontak berhasil terbuka** |
| `http://localhost:8000/sembarang` | Halaman **404 Not Found** |

Coba juga klik setiap tautan di menu. Ganti angka di URL (misalnya `/produk/detail/99`) dan perhatikan bahwa angkanya ikut berubah di judul.

Lewat terminal:

```bash
php artisan route:list --except-vendor   # harus ada 7 route
php artisan test                          # 2 test lulus
```

Hasil saat tahap ini dikerjakan: semua URL di atas sesuai harapan, `route:list` menampilkan 7 route, dan 2 test lulus.

---

## Kesalahan umum dan cara memperbaikinya

| Pesan error | Penyebab | Solusi |
|---|---|---|
| `Target class [BerandaController] does not exist` | Baris `use App\Http\Controllers\BerandaController;` lupa ditulis di `web.php`, atau nama kelas salah ketik | Tambahkan `use ...` di atas `web.php`. Huruf besar/kecil harus sama persis. |
| `Call to undefined method ...Controller::index()` | Nama method di route berbeda dengan yang ada di controller | Samakan, misalnya `'produkAll'` ↔ `function produkAll()` |
| `View [v_beranda.index] not found` | Nama folder/file view salah, atau ekstensinya bukan `.blade.php` | Cek `resources/views/v_beranda/index.blade.php` |
| `Undefined variable $judul` | Controller tidak mengirim `'judul'` ke view | Tambahkan `'judul' => ...` di array `view()` |
| `Route [produk.all] not defined` | Nama di `route('...')` tidak sama dengan `->name('...')` | Cek ejaan nama route dengan `php artisan route:list` |
| `Missing required parameter for [Route: produk.detail]` | `route('produk.detail')` dipanggil tanpa id | Tulis `route('produk.detail', $id)` |
| Halaman 404 padahal route sudah ditulis | Route ter-cache | `php artisan route:clear` |

---

## Perbedaan dengan modul (Laravel 10)

| Modul (Laravel 10) | Project ini (Laravel 13) |
|---|---|
| File view dibuat manual | `php artisan make:view v_folder.nama` |
| Request melewati `app/Http/Kernel.php` | Kernel tidak ada. Semua diatur di `bootstrap/app.php`. Alurnya tetap sama. |
| Isi `routes/web.php`, `make:controller`, `view()`, `route()`, Blade | **Sama persis** |
