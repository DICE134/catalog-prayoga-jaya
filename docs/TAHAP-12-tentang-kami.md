# Tahap 12 — Halaman Tentang Kami

*Halaman ini tidak ada di modul. Polanya sama dengan halaman lain: route → controller → view dengan `@extends`.*

## 1. Tujuan

Halaman ini membangun kepercayaan pelanggan dengan menunjukkan bahwa toko itu nyata dan jelas. Isinya:

| No | Bagian | Sumber isi |
|---|---|---|
| 1 | Sejarah singkat | Teks **PLACEHOLDER**, diganti pemilik toko |
| 2 | Profil toko | Teks **PLACEHOLDER** + nama, alamat, dan jam buka dari `config('toko')` |
| 3 | Jenis material yang dijual | **Otomatis** dari tabel `kategori` |
| 4 | Foto toko | `public/images/foto-toko.svg` (placeholder) |
| 5 | Informasi pelayanan | Jam buka dan WhatsApp dari config, info pengiriman berupa **PLACEHOLDER** |

Teks sejarah dan profil **sengaja tidak dikarang**, karena datanya belum diberikan. Semua teks yang harus diganti ditandai `[PLACEHOLDER — ...]`, ditulis miring, dan berwarna abu-abu supaya mudah ditemukan.

## 2. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `app/Http/Controllers/TentangController.php` | Mengirim `$daftarKategori` ke view. |
| `resources/views/v_tentang/index.blade.php` | Halaman lengkap dengan 5 bagian. Class tombol dan judul disesuaikan dengan desain ulang. |
| `tests/Feature/TentangTest.php` | **Baru.** 3 pengujian. |

## 3. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:test TentangTest --phpunit` | Membuat file pengujian. |
| `php artisan test` | Menjalankan semua pengujian. |

## 4. Penjelasan kode

### 4.1 Jenis material diambil otomatis

```php
'daftarKategori' => Kategori::orderBy('nama_kategori')->get(),
```

Daftar material **tidak ditulis manual** di view. Jika admin menambah kategori, misalnya "Atap & Genteng", halaman Tentang Kami langsung ikut menampilkannya. Setiap item juga menaut ke katalog kategori tersebut.

### 4.2 Data toko dari satu sumber

Alamat dan jam operasional memakai `config('toko.alamat')` dan `config('toko.jam_buka')`, sama seperti di footer. Jika data di `.env` diubah, semua halaman ikut berubah.

### 4.3 Tata letak

- `order-md-2` dan `order-md-1` menukar urutan kolom di layar sedang ke atas: foto di kanan, teks di kiri. Di HP, foto tampil lebih dulu di atas teks.
- `<figure>` dan `<figcaption>` adalah tag HTML yang tepat untuk gambar beserta keterangannya.
- `<dl>`, `<dt>`, dan `<dd>` dipakai untuk pasangan "label : isi" pada profil toko.

### 4.4 Tampilan (desain ulang)

Halaman ini memakai gaya umum dari `public/css/frontend.css` (lihat Tahap 8 bagian 4.7). Di view, yang diubah hanya **nama class**:

| Bagian | Class | Tampilan |
|---|---|---|
| Judul halaman dan judul bagian | `judul-bagian` | Huruf Inter tebal, rata kiri |
| Daftar jenis material | `btn btn-garis` | Tombol bergaris biru tua, berubah biru penuh saat disorot |
| Tombol "Chat WhatsApp" dan "Lihat Katalog Produk" | `btn btn-toko` | Tombol oranye |
| Kotak "Jenis Material" | `bg-light` | Latar abu muda `#F4F6F9` |
| Tiga kartu informasi pelayanan | `card` (tanpa `shadow-sm`) | Bergaris tipis, tanpa bayangan |

Ikon WhatsApp di kartu pelayanan tetap hijau (`text-success`), karena hanya berupa ikon pengenal, bukan tombol.

## 5. Cara menguji di browser

1. Buka `http://localhost:8000/tentang`. Kelima bagian tampil.
2. Bagian "Jenis Material" menampilkan 8 kategori. Klik salah satu, dan katalog kategori itu terbuka.
3. Login admin, lalu tambah kategori baru. Muat ulang halaman Tentang Kami, dan kategori baru langsung muncul.
4. Alamat yang Anda isi di `.env` tampil di Profil Toko.

## 6. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| `Undefined variable $daftarKategori` | Variabel tidak dikirim dari controller. | Periksa `TentangController@index`. |
| Data alamat lama masih tampil | Config masih ter-cache. | `php artisan config:clear` |
| Teks `[PLACEHOLDER ...]` terlihat di website | Placeholder belum diganti. | Ganti di `v_tentang/index.blade.php`. |

## 7. Yang harus Anda ganti

Semua teks di bawah ada di `resources/views/v_tentang/index.blade.php`. Cari kata `PLACEHOLDER`.

- [ ] **Sejarah singkat**: tahun berdiri, pendiri, dan perkembangan toko.
- [ ] **Profil toko**: fokus usaha dan pelanggan yang dilayani.
- [ ] **Pengambilan & pengiriman**: apakah bisa diantar, ke area mana, dan apakah ada biaya.
- [ ] **Foto toko**: `public/images/foto-toko.svg` (dipakai juga di beranda).
- [ ] **`TOKO_JAM_BUKA`** di `.env`, lalu jalankan `php artisan config:clear`.
