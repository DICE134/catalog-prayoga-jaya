# Tahap 11 — Halaman Detail Produk

*Acuan: Modul Web Programming III, Minggu 2 (`ProdukController@detail`, `v_produk/detail.blade.php`).*

## 1. Tujuan

Halaman detail memberi informasi lengkap tentang satu produk:
- foto utama dan foto tambahan;
- nama, merek, spesifikasi atau ukuran, satuan, harga, dan deskripsi;
- **tombol WhatsApp** dengan pesan otomatis berisi nama produk, sebagai pengganti tombol "Pesan"/keranjang di template modul;
- bagian **Produk Serupa** dari kategori yang sama, maksimal 4.

Produk berstatus **Blok** atau id yang tidak ada menghasilkan halaman **404**.

## 2. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `app/Http/Controllers/ProdukController.php` | Method `detail()`. |
| `resources/views/v_produk/detail.blade.php` | Halaman detail lengkap. |
| `resources/views/errors/404.blade.php` | **Baru.** Halaman 404 berbahasa Indonesia yang memakai layout website. |
| `public/css/frontend.css` | Gaya foto detail (`foto-detail`) dan tabel spesifikasi (`tabel-spek`) (desain ulang, lihat Tahap 8 bagian 4.7). |
| `tests/Feature/DetailProdukTest.php` | **Baru.** 7 pengujian. |

## 3. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:test DetailProdukTest --phpunit` | Membuat file pengujian. |
| `php artisan make:view errors.404` | Membuat halaman 404 kustom. |
| `php artisan test` | Menjalankan semua pengujian. |

## 4. Penjelasan kode

### 4.1 Mengambil produk dengan `firstOrFail`

```php
$produk = Produk::with(['kategori', 'fotoProduk'])
    ->where('status', 1)
    ->where('id', $id)
    ->firstOrFail();
```

- `firstOrFail()` mengambil satu baris. Jika **tidak ada**, Laravel langsung menghentikan proses dan menampilkan halaman 404.
- Syarat `where('status', 1)` ikut dicek, jadi produk Blok juga dianggap "tidak ada". Pelanggan tidak bisa membuka produk Blok walaupun menebak URL-nya, misalnya `/produk/detail/12`.
- `with(['kategori', 'fotoProduk'])` mengambil nama kategori dan semua foto tambahan sekaligus.

Kenapa tidak memakai `Produk::findOrFail($id)`? Karena `findOrFail` hanya mencari berdasarkan id, tanpa memeriksa status.

### 4.2 Tautan WhatsApp

```php
$pesan = 'Halo Prayoga Jaya, saya ingin bertanya tentang '.$produk->nama_produk;
$linkWhatsapp = 'https://wa.me/'.config('toko.wa').'?text='.rawurlencode($pesan);
```

- Formatnya `https://wa.me/{nomor}?text={pesan}`. Nomor ditulis dengan format internasional tanpa `+`, dan diambil dari `TOKO_WA` di `.env`.
- `rawurlencode()` mengubah karakter yang tidak boleh ada di URL:

  | Karakter | Menjadi |
  |---|---|
  | spasi | `%20` |
  | `,` | `%2C` |
  | `&` | `%26` |

  Tanpa ini, produk bernama "Cat Tembok & Kayu" membuat pesan terpotong di "Cat Tembok ", karena `&` dianggap pemisah parameter URL.
- Saat diklik, WhatsApp Web (di komputer) atau aplikasi WhatsApp (di HP) terbuka dengan pesan yang sudah terisi.

### 4.3 Produk serupa

```php
Produk::with('kategori')
    ->where('status', 1)
    ->where('kategori_id', $produk->kategori_id)   // kategori sama
    ->where('id', '!=', $produk->id)               // bukan produk ini sendiri
    ->latest()->take(4)->get();
```

Bagian ini memakai partial kartu yang sama dengan beranda dan katalog. Jika tidak ada produk serupa, bagiannya **tidak ditampilkan** sama sekali.

### 4.4 Galeri foto

- Foto besar memakai `url_foto`: foto asli, atau placeholder jika belum ada foto.
- Jika produk memiliki foto tambahan, deretan thumbnail tampil di bawah foto besar. Thumbnail pertama adalah foto utama.
- JavaScript kecil di `@push('scripts')` mengganti `src` foto besar ketika thumbnail diklik. Thumbnail memakai `<button>` (bukan `<img>` biasa), sehingga bisa dipilih dengan keyboard (Tab lalu Enter).
- Foto besar (class `foto-detail`) berbentuk persegi dengan sudut membulat di atas latar abu muda, sama seperti kartu produk.

### 4.4b Susunan informasi produk

Kolom kanan disusun **sesuai urutan yang dicari pembeli**:

1. Nama kategori (teks kecil abu), lalu nama produk sebagai judul.
2. **Harga** besar berwarna biru tua (class `harga`), dengan keterangan "per sak" di bawahnya.
3. **Tombol WhatsApp** oranye selebar kolom (`btn-toko btn-lg w-100`), diletakkan **tepat di bawah harga**. Pembeli yang sudah cocok dengan harga langsung bisa bertanya tanpa menggulir. Ikon WhatsApp tetap ada supaya tombolnya mudah dikenali.
4. **Tabel spesifikasi** (class `tabel-spek`): merek, ukuran, satuan, dan kategori, dipisah garis tipis. HTML-nya tetap `<dl>`; setiap pasangan `<dt>`/`<dd>` dibungkus `<div>`, yang memang diizinkan di dalam `<dl>`.
5. Deskripsi, lalu tautan "Kembali ke katalog".

Bagian **Produk Serupa** dipisahkan garis tipis di atasnya dan tampil 2 kolom di HP serta 4 kolom di desktop.

### 4.5 Deskripsi yang aman

```blade
{!! nl2br(e($produk->detail)) !!}
```

1. `e()` meng-escape HTML, sehingga tulisan seperti `<script>` di deskripsi tidak dijalankan.
2. `nl2br()` mengubah baris baru (Enter di textarea admin) menjadi `<br>`, sehingga paragraf tetap terpisah.
3. `{!! !!}` menampilkan hasil **tanpa** escape lagi. Ini aman karena langkah 1 sudah meng-escape isinya.

**Jangan pernah** menulis `{!! $produk->detail !!}` tanpa `e()`.

### 4.6 Halaman 404 kustom

Laravel otomatis memakai `resources/views/errors/404.blade.php` setiap kali terjadi 404, baik dari `firstOrFail()`, `findOrFail()`, maupun URL yang salah ketik. Halaman bawaan Laravel berbahasa Inggris ("Not Found"). Halaman kustom ini berbahasa Indonesia, memakai navbar dan footer website, dan memiliki tombol kembali ke katalog.

## 5. Cara menguji di browser

1. Dari katalog, klik **Detail Produk** pada "Semen Portland". Foto, harga `Rp. 68.000 per sak`, merek, spesifikasi, dan deskripsi tampil.
2. Klik tombol oranye **Tanya / Pesan via WhatsApp** tepat di bawah harga. WhatsApp terbuka dengan pesan "Halo Prayoga Jaya, saya ingin bertanya tentang Semen Portland".
3. Bagian **Produk Serupa** di bawah menampilkan produk lain dari kategori Semen & Perekat.
4. **Galeri:** login admin, buka **Ubah** pada salah satu produk, lalu unggah 2 foto tambahan. Buka detail produk itu dan klik thumbnail-nya; foto besar berganti.
5. **404:**
   - Buka `/produk/detail/999`. Halaman "Halaman tidak ditemukan" berbahasa Indonesia muncul.
   - Buka `/produk/detail/12` (Gergaji Kayu, berstatus Blok). Hasilnya juga 404.

## 6. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| Produk Blok masih bisa dibuka lewat URL | Memakai `findOrFail($id)` tanpa memeriksa status. | Pakai `where('status', 1)->where('id', $id)->firstOrFail()`. |
| Pesan WhatsApp terpotong | Pesan tidak di-encode. | Pakai `rawurlencode($pesan)`. |
| WhatsApp menolak nomor | Nomor diawali `0` atau `+`, atau berisi spasi/tanda hubung. | Tulis `62...` tanpa tanda apa pun di `TOKO_WA`. |
| Deskripsi tampil dalam satu baris panjang | Baris baru tidak diubah. | Pakai `nl2br(e(...))`. |
| Produk yang sedang dibuka ikut muncul di "Produk Serupa" | Lupa `where('id', '!=', $produk->id)`. | Tambahkan syarat tersebut. |
| Halaman 404 masih berbahasa Inggris | File berada di folder yang salah. | Harus di `resources/views/errors/404.blade.php`. |

## 7. Perbedaan dengan modul (Laravel 10)

| Modul | Project ini |
|---|---|
| Tombol "Pesan"/keranjang | Tombol **WhatsApp** dengan pesan berisi nama produk. |
| Halaman 404 bawaan | Halaman 404 kustom berbahasa Indonesia (`make:view errors.404`). |
| Detail mencari berdasarkan id saja | Ditambah syarat `status = 1`, sehingga produk Blok tidak bisa dibuka publik. |
