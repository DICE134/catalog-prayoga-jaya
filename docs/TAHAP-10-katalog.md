# Tahap 10 — Halaman Katalog Produk

*Acuan: Modul Web Programming III, Minggu 2 (`produkAll`, `produkKategori`, filter kategori, pagination).*

## 1. Tujuan

Halaman katalog membantu pelanggan melihat semua produk yang tersedia. Isinya:

1. Judul halaman dan breadcrumb (jalur navigasi).
2. Kolom pencarian `?q=` yang mencari di nama produk **dan** merek.
3. Filter kategori di sidebar, pengganti "Filter By Brand" dari template E-Shop.
4. Daftar produk berupa kartu.
5. Harga dalam format `Rp. 68.000`.
6. Tautan "Detail Produk" (seluruh kartu bisa diklik).

Aturan yang dipakai:
- Hanya produk **Publis** yang tampil.
- Ditampilkan 9 produk per halaman.
- Pencarian dan filter kategori **bisa digabung**.
- Pesan ramah tampil jika hasilnya kosong.

Dua route memakai **view yang sama** (`v_produk/index.blade.php`):

| Route | URL | Method |
|---|---|---|
| `produk.all` | `/produk/all?q=...&page=...` | `ProdukController@produkAll` |
| `produk.kategori` | `/produk/kategori/{id}?q=...&page=...` | `ProdukController@produkKategori` |

## 2. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `app/Http/Controllers/ProdukController.php` | `produkAll`, `produkKategori`, dan method bantu `tampilkanKatalog`. |
| `app/Providers/AppServiceProvider.php` | `Paginator::useBootstrapFive()` untuk mengatur gaya tombol pagination. |
| `resources/views/v_produk/index.blade.php` | Halaman katalog lengkap. |
| `public/css/frontend.css` | Gaya daftar filter (`daftar-filter`) dan warna pagination (desain ulang, lihat Tahap 8 bagian 4.7). |
| `tests/Feature/KatalogTest.php` | **Baru.** 9 pengujian katalog. |
| `tests/Feature/LayoutFrontendTest.php` | Membuat 1 produk dulu, karena kategori yang tidak ada sekarang menghasilkan 404. |

## 3. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:test KatalogTest --phpunit` | Membuat file pengujian. |
| `php artisan test` | Menjalankan semua pengujian. |

## 4. Penjelasan kode

### 4.1 Satu logika untuk dua route

`produkAll()` dan `produkKategori()` sama-sama memanggil `tampilkanKatalog($request, $kategori)`. Bedanya hanya di parameter kategori: `null` berarti semua kategori. Aturan katalog cukup ditulis **sekali**, sehingga kedua halaman tidak mungkin berbeda perilaku.

```php
$kategoriAktif = Kategori::findOrFail($id);   // id tidak ada → halaman 404
```

### 4.2 Query katalog, langkah demi langkah

```php
Produk::with('kategori')
    ->where('status', 1)                                    // 1. hanya Publis
    ->when($kategoriAktif, fn ($q) => $q->where('kategori_id', $kategoriAktif->id))  // 2. filter kategori
    ->when($cari !== '', function ($q) use ($cari) {        // 3. pencarian
        $q->where(function ($q) use ($cari) {
            $q->where('nama_produk', 'like', '%'.$cari.'%')
              ->orWhere('merek', 'like', '%'.$cari.'%');
        });
    })
    ->orderBy('nama_produk')
    ->paginate(9)                                           // 4. 9 per halaman
    ->appends($request->query());                           // 5. pertahankan ?q= di tautan halaman
```

- **`when(kondisi, fungsi)`**: fungsi hanya dijalankan jika kondisinya *true*. Dengan cara ini, satu query bisa melayani empat keadaan: tanpa filter, hanya kategori, hanya pencarian, atau keduanya.
- **`like '%semen%'`**: mencari teks yang *mengandung* "semen" di posisi mana pun. Di MySQL, pencarian ini tidak membedakan huruf besar dan kecil.
- **Kenapa `orWhere` dibungkus `where(function ...)`?** Ini bagian **paling penting** di tahap ini.

  Tanpa pembungkus, SQL-nya menjadi:
  ```sql
  WHERE status = 1 AND nama_produk LIKE '%semen%' OR merek LIKE '%semen%'
  ```
  Karena `AND` dikerjakan lebih dulu daripada `OR`, produk **Blok** yang mereknya mengandung "semen" ikut tampil. Dengan pembungkus, SQL-nya menjadi:
  ```sql
  WHERE status = 1 AND (nama_produk LIKE '%semen%' OR merek LIKE '%semen%')
  ```
  Test `test_pencarian_tidak_menampilkan_produk_blok` memastikan kesalahan ini tidak terjadi.

- **`paginate(9)`**: membaca `?page=` dari URL lalu mengambil 9 baris yang sesuai.

  Hasilnya bukan `Collection` biasa. Objek ini juga membawa informasi halaman seperti `total()`, `firstItem()`, `lastItem()`, dan `links()`.

- **`appends($request->query())`**: tanpa ini, tautan "halaman 2" hanya berisi `?page=2`, sehingga kata kunci pencarian hilang. Dengan `appends`, tautannya menjadi `?q=semen&page=2`.

### 4.3 Pencarian dan filter yang saling mempertahankan

- **Form pencarian**: `action`-nya adalah halaman yang sedang dibuka. Jika pelanggan berada di kategori "Cat", pencarian tetap dibatasi pada kategori "Cat".
- **Tautan kategori di sidebar** membawa kata kunci:
  ```blade
  route('produk.kategori', array_filter(['id' => $kat->id, 'q' => $cari]))
  ```
  Parameter yang tidak ada di pola URL route (`q`) otomatis menjadi query string: `/produk/kategori/3?q=semen`. `array_filter` membuang `q` jika kosong, supaya URL tetap bersih.
- `method="GET"` pada form membuat kata kunci masuk ke URL, sehingga hasil pencarian bisa di-bookmark atau dibagikan.

### 4.4 Pagination bergaya Bootstrap

Laravel 13 bawaannya memakai Tailwind CSS untuk tombol pagination. Website ini memakai Bootstrap, jadi di `AppServiceProvider::boot()` ditambahkan:

```php
Paginator::useBootstrapFive();
```

Teks "Sebelumnya" dan "Berikutnya" diambil dari `lang/id/pagination.php` (Tahap 7). Jika semua hasil muat di satu halaman, `{{ $produk->links() }}` tidak menampilkan apa pun.

### 4.5 Tampilan

- **Judul dan kolom pencarian** berada di pita berlatar abu muda di bagian atas. Kolom cari berukuran besar dengan ikon kaca pembesar dan tombol oranye "Cari".
- **Filter kategori** berupa daftar tautan sederhana (class `daftar-filter`), bukan kotak-kotak. Kategori yang aktif berlatar biru tua dengan teks putih, dan jumlah produk tampil di kanan.
- **Kartu produk** memakai partial yang sama dengan beranda (`v_produk/kartu.blade.php`): tanpa bayangan, foto di atas latar abu, dan seluruh kartu bisa diklik. Kartu tampil 2 kolom, dan 3 kolom di layar mulai 1200 px.
- Di HP, daftar kategori disembunyikan di balik tombol bergaris biru **"Filter Kategori"** (collapse Bootstrap) supaya produk langsung terlihat.
- Jika hasil kosong, tampil pesan "Produk tidak ditemukan" di kotak abu dengan dua tombol: **Tanya via WhatsApp** (oranye, tombol utama) dan **Lihat semua produk** (bergaris biru).
- **Pagination** memakai warna biru tua. Warnanya diatur lewat variabel Bootstrap (`--bs-pagination-active-bg`) di `frontend.css`, tanpa mengubah view pagination bawaan.
- Kata kunci ditampilkan kembali dengan `{{ $cari }}`. Tanda `{{ }}` meng-escape karakter HTML, sehingga kata kunci berisi `<script>` **tidak** dijalankan. Ini mencegah serangan XSS.

## 5. Cara menguji di browser

1. Buka `http://localhost:8000/produk/all`. Ada 9 produk, keterangan "Menampilkan 1–9 dari 11 produk", dan tombol halaman 2. Halaman 2 berisi 2 produk.
2. Produk **Gergaji Kayu** (Blok) tidak pernah tampil.
3. Ketik `semen` lalu klik **Cari**. Hasilnya produk yang nama atau mereknya mengandung "semen".
4. Sambil masih mencari, klik kategori lain di daftar kategori sebelah kiri (kategori aktif berlatar biru tua). URL berisi `?q=semen`, artinya pencarian tetap aktif.
5. Klik kategori dari beranda. Breadcrumb menunjukkan Beranda › Produk › nama kategori.
6. Ketik kata acak, misalnya `zzz`. Pesan "Produk tidak ditemukan" muncul.
7. Buka `/produk/kategori/999`. Hasilnya halaman 404.
8. Klik **Detail Produk**. Halaman detail dibuat di Tahap 11.

## 6. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| Produk Blok muncul saat mencari | `orWhere` tidak dibungkus `where(function ...)`. | Lihat 4.2. |
| Pencarian hilang saat klik halaman 2 | Lupa `->appends($request->query())`. | Tambahkan setelah `paginate()`. |
| Tombol pagination tampil aneh atau besar | Masih memakai gaya Tailwind. | `Paginator::useBootstrapFive()` di `AppServiceProvider`. |
| `Method links does not exist` | Memakai `get()`, bukan `paginate()`. | Ganti dengan `paginate(9)`. |
| Pindah kategori menghapus pencarian | Tautan kategori tidak membawa `q`. | Tambahkan `'q' => $cari` ke `route(...)`. |
| Kategori yang tidak ada menampilkan halaman kosong | Memakai `find()`. | Pakai `findOrFail()` agar hasilnya 404. |

## 7. Perbedaan dengan modul (Laravel 10)

| Modul | Project ini |
|---|---|
| "Filter By Brand" dari template E-Shop | Diganti filter **kategori**, karena merek di toko material sangat beragam. |
| Pagination kustom memakai view template E-Shop | Memakai pagination bawaan Laravel dengan gaya Bootstrap 5 (`useBootstrapFive`). |
| Laravel 10 bawaannya memakai pagination Bootstrap | Laravel 11 ke atas bawaannya Tailwind, jadi `useBootstrapFive()` wajib dipanggil. |
| `produkAll` dan `produkKategori` ditulis terpisah | Memakai satu method bantu `tampilkanKatalog`, supaya pencarian dan filter bisa digabung tanpa kode ganda. |
