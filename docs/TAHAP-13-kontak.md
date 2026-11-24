# Tahap 13 — Halaman Kontak

*Halaman ini tidak ada di modul. Polanya sama dengan halaman lain.*

## 1. Tujuan

Halaman Kontak memudahkan pelanggan menghubungi toko dan menemukan lokasinya. Isinya:
- nomor WhatsApp;
- alamat;
- jam operasional;
- Google Maps, berupa peta tertanam **atau** tombol tautan lokasi;
- tombol WhatsApp besar.

**Semua** data diambil dari `config('toko')`, yang membaca `.env`. Tidak ada data toko yang ditulis langsung di view.

## 2. File yang dibuat atau diubah

| File | Keterangan |
|---|---|
| `app/Http/Controllers/KontakController.php` | Menyiapkan tautan WhatsApp dan alamat peta. |
| `resources/views/v_kontak/index.blade.php` | Halaman kontak lengkap. Class tombol dan judul disesuaikan dengan desain ulang. |
| `tests/Feature/KontakTest.php` | **Baru.** 6 pengujian. |
| `tests/Feature/LayoutFrontendTest.php` | Cara menghitung menu aktif diperbaiki, karena breadcrumb juga memakai `aria-current`. |

## 3. Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:test KontakTest --phpunit` | Membuat file pengujian. |
| `php artisan test` | Menjalankan semua pengujian. |

## 4. Penjelasan kode

### 4.1 Tiga kemungkinan tampilan peta

| `TOKO_MAPS_EMBED` | `TOKO_MAPS_URL` | Yang tampil |
|---|---|---|
| diisi | (apa saja) | Peta tertanam (iframe), ditambah tombol tautan jika URL juga diisi |
| kosong | diisi | Tombol **"Buka di Google Maps"** |
| kosong | kosong | Pesan "(Lokasi Google Maps belum diisi)" |

Di view, urutan keputusan ini ditulis dengan `@if`, `@unless`, dan `@elseif`.

### 4.2 Mengambil kode peta tertanam dari Google Maps

Cara mendapatkan kodenya:
1. Buka lokasi toko di Google Maps.
2. Klik **Bagikan**, lalu tab **Sematkan peta**, lalu **Salin HTML**.
3. Hasilnya berupa `<iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>`.

Method `alamatPetaTertanam()` di controller menerima **dua bentuk** isian:
- alamatnya saja: `https://www.google.com/maps/embed?pb=...`
- seluruh kode `<iframe ...>`. Isi `src="..."` diambil dengan `preg_match`.

Setelah itu, alamat **hanya diterima** jika diawali `https://www.google.com/maps/embed`. Ini pengaman: walaupun `.env` salah isi, halaman tidak akan menampilkan situs lain di dalam iframe.

Cara mengisinya di `.env` (pakai tanda kutip tunggal, karena kodenya berisi tanda kutip ganda):

```
TOKO_MAPS_EMBED='<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" ...></iframe>'
```

### 4.3 Tautan WhatsApp

```php
$pesan = 'Halo Prayoga Jaya, saya ingin bertanya';
$linkWhatsapp = 'https://wa.me/'.config('toko.wa').'?text='.rawurlencode($pesan);
```

Polanya sama dengan halaman detail produk (Tahap 11), tetapi tanpa nama produk.

### 4.4 Tampilan dan aksesibilitas

- `<address>` adalah tag HTML khusus untuk informasi kontak.
- `ratio ratio-4x3` dari Bootstrap membuat peta mengecil di HP tanpa terpotong.
- `title="Peta lokasi ..."` pada iframe membuat pembaca layar menyebutkan isi bingkai tersebut.
- `loading="lazy"` membuat peta baru dimuat ketika terlihat di layar.
- Tombol WhatsApp dibuat besar dan selebar kartu (`btn-lg w-100`), sehingga mudah ditekan di HP.

### 4.5 Tampilan (desain ulang)

Halaman ini memakai gaya umum dari `public/css/frontend.css` (lihat Tahap 8 bagian 4.7). Di view, yang diubah hanya **nama class**:

| Bagian | Class | Tampilan |
|---|---|---|
| Tombol "Chat via WhatsApp" | `btn btn-toko btn-lg w-100` | Tombol **oranye** selebar kartu, dengan ikon WhatsApp |
| Tombol "Buka di Google Maps" | `btn btn-garis` | Tombol bergaris biru tua (tombol kedua) |
| Judul "Hubungi Kami" | `judul-bagian` | Huruf Inter tebal |
| Dua kartu (kontak dan lokasi) | `card` (tanpa `shadow-sm`) | Bergaris tipis, tanpa bayangan |
| Kotak "lokasi belum diisi" / ajakan buka peta | `bg-light` | Latar abu muda |

Hanya ada **satu tombol oranye** di halaman ini (WhatsApp), supaya pengunjung langsung tahu tindakan utamanya. Ikon WhatsApp di samping nomor tetap hijau, karena hanya berupa ikon pengenal.

## 5. Cara menguji di browser

1. Buka `http://localhost:8000/kontak`. Nomor WhatsApp, alamat Bogor, dan jam operasional tampil.
2. Saat ini `TOKO_MAPS_URL` sudah diisi dan `TOKO_MAPS_EMBED` masih kosong, jadi yang tampil tombol bergaris biru **Buka di Google Maps**. Klik tombol itu, dan lokasi toko terbuka di Google Maps.
3. **Coba peta tertanam:**
   1. Salin kode "Sematkan peta" dari Google Maps.
   2. Tempel ke `TOKO_MAPS_EMBED` di `.env` (dengan tanda kutip tunggal).
   3. Jalankan `php artisan config:clear`.
   4. Muat ulang halaman Kontak. Peta tampil langsung di halaman.
4. Klik **Chat via WhatsApp**. Pesan "Halo Prayoga Jaya, saya ingin bertanya" sudah terisi.

## 6. Kesalahan umum

| Masalah | Penyebab | Solusi |
|---|---|---|
| Peta tidak muncul walaupun `TOKO_MAPS_EMBED` sudah diisi | Config masih ter-cache, atau yang ditempel adalah tautan biasa (`maps.app.goo.gl`), bukan kode sematan. | `php artisan config:clear`. Pastikan alamatnya diawali `https://www.google.com/maps/embed`. |
| Isi `.env` terpotong atau error | Kode iframe berisi `"` tetapi dibungkus `"`. | Bungkus dengan tanda kutip tunggal `'...'`. |
| Peta tampil "Google Maps can't load / refused to connect" | Yang dipakai tautan biasa di dalam iframe. | Google hanya mengizinkan alamat `/maps/embed` di dalam iframe. |
| Nomor WhatsApp tidak valid | Diawali `0` atau `+`. | Tulis `62...` di `TOKO_WA`. |

## 7. Yang harus Anda ganti

- [ ] `TOKO_JAM_BUKA` masih placeholder.
- [ ] `TOKO_MAPS_EMBED` (opsional): kode sematan Google Maps.
- [x] `TOKO_WA`, `TOKO_ALAMAT`, dan `TOKO_MAPS_URL` sudah Anda isi.
