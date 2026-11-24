# Tahap 7 — Kelola Produk dan Foto (Data Master dan Join)

*Acuan: Modul Web Programming II, Minggu 9–11.*

## Tujuan

Membuat halaman admin untuk **menambah, mengubah, dan menghapus produk** beserta **foto utama** dan **foto tambahan**, sehingga pemilik toko bisa mengelola katalog tanpa mengedit kode. Di tahap ini juga semua pesan validasi diatur ke **Bahasa Indonesia** lewat folder `lang/id`.

---

## Konsep

### 1. Data master

**Data master** adalah data acuan yang dipakai data lain. Di sini `kategori` adalah data master bagi `produk`. Dropdown "Kategori" di form produk **diambil dari tabel `kategori`**, sehingga admin tidak bisa salah ketik nama kategori, dan kategori baru langsung muncul di dropdown.

### 2. Relasi dan "join" dengan Eloquent

Daftar produk perlu menampilkan **nama kategori**, padahal tabel `produk` hanya menyimpan `kategori_id`. Di SQL biasa, kita menulis `JOIN`:

```sql
SELECT produk.*, kategori.nama_kategori
FROM produk JOIN kategori ON kategori.id = produk.kategori_id
```

Di Laravel cukup:

```php
$produk = Produk::with('kategori')->latest()->get();   // controller
{{ $row->kategori->nama_kategori }}                    // view
```

`with('kategori')` disebut **eager loading**:

| Tanpa `with` | Dengan `with('kategori')` |
|---|---|
| 1 query ambil 12 produk + **12 query** ambil kategori (satu per baris) = **13 query** | 1 query produk + **1 query** semua kategorinya = **2 query** |

Masalah pada kolom kiri disebut **N+1 query**. Dengan 1.000 produk, halaman bisa menjalankan 1.001 query dan menjadi lambat.

### 3. Unggah file dan `storage:link`

```
Browser ──(form multipart)──► ProdukController@store
                                │  $file->store('img-produk', 'public')
                                ▼
           storage/app/public/img-produk/aB3x9...jpg   ← file disimpan di sini
                                ▲
public/storage ──(symlink)──────┘                       ← dibuat oleh php artisan storage:link
       │
       ▼
http://localhost:8000/storage/img-produk/aB3x9...jpg     ← alamat yang dibuka browser
```

- Browser **hanya** bisa membuka file di folder `public/`. Folder `storage/` sengaja tertutup.
- `php artisan storage:link` membuat **symlink** (jalan pintas) `public/storage` → `storage/app/public`. Dengan begitu, file di `storage/app/public` bisa dibuka lewat `/storage/...`.
- Database hanya menyimpan **nama file** (`aB3x9...jpg`), bukan isi gambarnya.

---

## Daftar file yang dibuat atau diubah

| File | Status | Isi |
|---|---|---|
| `app/Http/Controllers/ProdukController.php` | diubah | +8 method admin: `index`, `create`, `store`, `edit`, `update`, `destroy`, `storeFoto`, `destroyFoto` + fungsi bantu |
| `app/Http/Controllers/KategoriController.php` | diubah | Pesan validasi manual dihapus, diganti `lang/id` |
| `app/Http/Controllers/LoginController.php` | diubah | Sama seperti di atas |
| `app/Models/Produk.php` | diubah | `HasFactory`, konstanta `FOLDER_FOTO`, accessor `url_foto` |
| `app/Models/FotoProduk.php` | diubah | Accessor `url_foto` |
| `app/Models/Kategori.php` | diubah | `HasFactory` |
| `database/factories/KategoriFactory.php` | baru | Data kategori palsu untuk test |
| `database/factories/ProdukFactory.php` | baru | Data produk palsu untuk test (+ state `blok()`, `unggulan()`) |
| `resources/views/backend/v_produk/index.blade.php` | baru | Tabel produk (DataTables) |
| `resources/views/backend/v_produk/form.blade.php` | baru | Isian form yang dipakai bersama |
| `resources/views/backend/v_produk/create.blade.php` | baru | Halaman tambah |
| `resources/views/backend/v_produk/edit.blade.php` | baru | Halaman ubah + kelola foto tambahan |
| `resources/views/backend/v_layouts/app.blade.php` | diubah | Menu **Produk** di sidebar |
| `routes/web.php` | diubah | `Route::resource('produk', ...)` + 2 route foto tambahan |
| `lang/en/*.php` | baru | Hasil `lang:publish` (bahasa Inggris, cadangan) |
| `lang/id/validation.php` | baru | Pesan validasi Bahasa Indonesia + nama kolom |
| `lang/id/pagination.php` | baru | Teks tombol pagination (dipakai Tahap 10) |
| `lang/id/auth.php` | baru | Pesan login |
| `public/images/produk-placeholder.svg` | baru | Gambar pengganti jika produk belum punya foto |
| `public/storage` | baru (symlink) | Hasil `storage:link` |
| `tests/Feature/ProdukCrudTest.php` | baru | 13 test produk dan foto |

## Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan lang:publish` | Menerbitkan file bahasa bawaan Laravel ke folder `lang/en` supaya bisa dilihat dan disalin |
| `php artisan storage:link` | Membuat symlink `public/storage` → `storage/app/public` |
| `php artisan make:factory KategoriFactory` / `ProdukFactory` | Membuat pembuat data palsu untuk test |
| `php artisan make:view backend.v_produk.index` (juga `create`, `edit`, `form`) | Membuat view |
| `php artisan make:test ProdukCrudTest --phpunit` | Membuat file test |
| `php artisan test` | Menjalankan semua test |

> **Kenapa tidak `make:controller ProdukController --resource`?** File `ProdukController` sudah ada sejak Tahap 2 (untuk katalog publik). Artisan menolak menimpa file yang sudah ada, jadi method admin **ditambahkan langsung** ke controller yang sama, seperti pola di modul. Route admin tetap terpisah dengan nama `backend.produk.*`, sedangkan route publik bernama `produk.*`.

> **Kenapa `lang/id` ditulis manual?** Tidak ada perintah Artisan untuk membuat bahasa baru. `lang:publish` hanya menerbitkan `lang/en`. Isi `lang/id` disalin dari `lang/en` lalu diterjemahkan.

---

## Penjelasan kode penting

### 1. Route

```php
Route::middleware('auth')->group(function () {
    ...
    Route::resource('produk', ProdukController::class)->except(['show']);
    Route::post('/produk/{produk}/foto', [ProdukController::class, 'storeFoto'])->name('produk.foto.store');
    Route::delete('/foto-produk/{fotoProduk}', [ProdukController::class, 'destroyFoto'])->name('produk.foto.destroy');
});
```

Semua route ada di grup `auth` dari Tahap 5, jadi otomatis hanya bisa dibuka admin yang sudah login.

### 2. Validasi (`aturanValidasi()`)

```php
'kategori_id' => ['required', 'exists:kategori,id'],
'harga'       => ['required', 'integer', 'min:0', 'max:999999999'],
'status'      => ['required', Rule::in([0, 1])],
'foto'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
```

| Aturan | Arti |
|---|---|
| `exists:kategori,id` | Nilai harus ada di kolom `id` tabel `kategori`, sehingga orang tidak bisa mengirim kategori palsu lewat alat seperti Postman |
| `integer` | Bilangan bulat. "68.000" atau "68000,50" ditolak. |
| `Rule::in([0, 1])` | Hanya 0 (Blok) atau 1 (Publis) |
| `nullable` | Boleh kosong. Jika diisi, aturan berikutnya berlaku. |
| `image` | Isi file benar-benar gambar. File `.pdf` yang diganti namanya menjadi `.jpg` tetap ditolak. |
| `mimes:jpg,jpeg,png,webp` | Jenis gambar yang diizinkan |
| `max:2048` | Untuk file, satuannya **kilobyte**, jadi maksimal 2 MB |

Aturan ini disimpan di satu method dan dipakai oleh `store` **dan** `update`, supaya tidak ditulis dua kali.

### 3. Menyimpan dan menghapus foto

```php
private function simpanFoto(UploadedFile $file): string
{
    $path = $file->store(Produk::FOLDER_FOTO, 'public');  // "img-produk/aB3x9...jpg"
    return basename($path);                              // "aB3x9...jpg"
}

private function hapusFoto(?string $namaFile): void
{
    if ($namaFile) {
        Storage::disk('public')->delete(Produk::FOLDER_FOTO.'/'.$namaFile);
    }
}
```

- `store()` memberi **nama acak unik**, sehingga dua foto bernama `semen.jpg` tidak saling menimpa. Nama acak juga mencegah nama file aneh seperti `../../index.php`.
- **Saat update dengan foto baru**, file lama dihapus dulu supaya folder tidak penuh oleh foto yang tidak terpakai.
- **Saat produk dihapus**, baris `foto_produk` ikut terhapus otomatis (`cascadeOnDelete` dari Tahap 3), tetapi **file**-nya tidak. Karena itu `destroy()` menghapus file foto utama dan semua foto tambahan secara manual.

### 4. Checkbox "unggulan"

```php
$data['unggulan'] = $request->boolean('unggulan');
```

Checkbox yang **tidak dicentang tidak terkirim sama sekali** ke server. `boolean('unggulan')` menghasilkan `true` jika dicentang dan `false` jika tidak ada.

### 5. Accessor `url_foto` di model

```php
protected function urlFoto(): Attribute
{
    return Attribute::get(fn (): string => $this->foto
        ? asset('storage/'.self::FOLDER_FOTO.'/'.$this->foto)
        : asset('images/produk-placeholder.svg'));
}
```

**Accessor** adalah kolom "buatan" yang dihitung saat dibaca. Method `urlFoto()` bisa dipakai di view sebagai `$produk->url_foto`. Logika "foto asli atau placeholder" ditulis **sekali** di model, lalu dipakai di admin dan nanti di halaman publik (Tahap 9–11).

### 6. View: form yang dipakai bersama

```blade
{{-- create.blade.php --}}
<form action="{{ route('backend.produk.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('backend.v_produk.form')
</form>

{{-- edit.blade.php --}}
<form action="{{ route('backend.produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('backend.v_produk.form', ['produk' => $produk])
</form>
```

- **`enctype="multipart/form-data"` wajib** untuk form yang mengirim file. Tanpa ini, file tidak ikut terkirim dan `hasFile('foto')` selalu `false`.
- Di `form.blade.php`, setiap kolom memakai pola `old('kolom', $produk?->kolom)`. Saat tambah, `$produk` bernilai `null`, sehingga `$produk?->kolom` juga `null` (tanda `?->` mencegah error). Saat ubah, kolom berisi nilai dari database.
- `@selected(kondisi)` dan `@checked(kondisi)` menulis atribut `selected`/`checked` hanya jika kondisinya benar.

### 7. Foto tambahan: form terpisah dan `multiple`

```blade
<input type="file" name="foto_tambahan[]" multiple>
```

```php
'foto_tambahan'   => ['required', 'array', 'max:5'],                       // daftar file, maksimal 5
'foto_tambahan.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],    // aturan untuk SETIAP file
```

- `name="foto_tambahan[]"` + `multiple` membuat beberapa file terkirim sebagai array.
- `.*` artinya "setiap isi array".
- Form foto tambahan **terpisah** dari form produk, karena HTML tidak mengizinkan form di dalam form.

```php
$produk->fotoProduk()->create(['foto' => $this->simpanFoto($file)]);
```

Menyimpan **lewat relasi** membuat `produk_id` terisi otomatis.

### 8. Tabel produk: harga rupiah dan pengurutan

```blade
<td data-order="{{ $row->harga }}">Rp. {{ number_format($row->harga, 0, ',', '.') }}</td>
```

- `number_format(68000, 0, ',', '.')` menghasilkan `68.000` (0 desimal, koma untuk desimal, titik untuk ribuan).
- `data-order` membuat DataTables mengurutkan berdasarkan angka asli (`68000`), bukan teks `Rp. 68.000`.

### 9. Pesan validasi Bahasa Indonesia (`lang/id/validation.php`)

```php
'required' => ':Attribute wajib diisi.',
'max' => ['file' => 'Ukuran :attribute maksimal :max kilobyte.', ...],

'custom' => [
    'nama_kategori' => ['unique' => 'Nama kategori sudah ada, gunakan nama lain.'],
],

'attributes' => [
    'kategori_id' => 'kategori',
    'nama_produk' => 'nama produk',
    ...
],
```

- Laravel memakai `lang/id` karena `APP_LOCALE=id` (Tahap 1).
- `:attribute` diganti nama kolom. `:Attribute` (huruf A besar) diganti nama kolom dengan huruf depan kapital.
- Tanpa `attributes`, pesannya berbunyi *"Kategori id wajib diisi."*. Dengan `attributes`, pesannya menjadi *"Kategori wajib diisi."*
- `custom` dipakai untuk pesan khusus satu kolom dan satu aturan.
- Aturan yang tidak diterjemahkan memakai bahasa Inggris dari `lang/en` (`APP_FALLBACK_LOCALE=en`).
- Karena pesan sudah terpusat, array pesan manual di `KategoriController` dan `LoginController` dihapus. Kalimat yang tampil tetap sama.

---

## Cara menguji di browser

1. `php artisan serve`, lalu login (`admin@prayogajaya.test` / `admin12345`).
2. Klik **Produk** di sidebar. Tabel menampilkan 12 produk lengkap dengan gambar placeholder, nama kategori, harga `Rp. 68.000 / sak`, label Publis/Blok, dan bintang untuk produk unggulan.
3. **Urut harga:** klik judul kolom **Harga**. Urutannya harus benar secara angka (Rp. 18.000 sebelum Rp. 450.000).
4. **Tambah:** klik **Tambah Produk** lalu langsung **Simpan** tanpa mengisi apa pun. Muncul pesan merah Bahasa Indonesia di setiap kolom wajib.
5. Isi lengkap dengan foto dari komputer Anda (JPG/PNG < 2 MB), lalu Simpan. Produk baru muncul di urutan teratas **dengan foto asli**.
6. **Validasi foto:** coba unggah file PDF. Muncul *"Foto harus berupa gambar."* Coba juga gambar > 2 MB.
7. **Ubah:** klik **Ubah** pada produk tadi. Foto saat ini tampil. Ganti harga tanpa memilih foto baru, lalu Simpan. Foto lama tetap ada.
8. **Foto tambahan:** di halaman Ubah, bagian **Foto Tambahan**, pilih 2–3 gambar sekaligus lalu klik **Unggah**. Foto tampil berjajar. Klik **Hapus** pada salah satunya.
9. **Cek file:** buka folder `storage/app/public/img-produk/` di VS Code. File tersimpan dengan nama acak.
10. **Hapus produk:** hapus produk uji tadi. Semua filenya ikut hilang dari folder `img-produk`.
11. **Ganti foto produk contoh:** di sini Anda bisa mulai mengunggah foto asli untuk 12 produk contoh.

```bash
php artisan test --filter=ProdukCrudTest
```

Hasil saat tahap ini dikerjakan: **36 test lulus** (13 di antaranya test produk). Uji di server sungguhan juga berhasil: produk uji dengan foto tersimpan, fotonya terbuka di `/storage/img-produk/...` sebagai `image/jpeg` dan tampil di daftar, lalu dihapus bersama filenya. Data kembali ke 12 produk.

---

## Kesalahan umum dan cara memperbaikinya

| Gejala / pesan | Penyebab | Solusi |
|---|---|---|
| Foto tersimpan tetapi di browser **404 / gambar rusak** | `storage:link` belum dijalankan, atau project dipindah ke komputer lain (symlink tidak ikut tersalin) | `php artisan storage:link` |
| `hasFile('foto')` selalu false, foto tidak tersimpan | Form tanpa `enctype="multipart/form-data"` | Tambahkan pada tag `<form>` |
| *"The foto failed to upload."* / gagal untuk file besar | Batas PHP `upload_max_filesize`/`post_max_size` lebih kecil dari file | Laragon → Menu → PHP → php.ini, naikkan nilainya, lalu restart. Di komputer ini sudah 2G. |
| Halaman error **413** / POST kosong untuk file sangat besar | Seperti di atas (`post_max_size`) | Sama seperti di atas |
| Pesan validasi masih bahasa Inggris | `APP_LOCALE` bukan `id`, file `lang/id/validation.php` tidak ada, atau aturan itu belum diterjemahkan | Cek `.env` dan isi file. Lalu `php artisan config:clear`. |
| Pesan berbunyi *"Kategori id wajib diisi."* | Nama kolom belum ada di `attributes` | Tambahkan `'kategori_id' => 'kategori'` |
| Status "Unggulan" tidak bisa dimatikan | Memakai `$request->unggulan` (bernilai null saat tidak dicentang) | `$request->boolean('unggulan')` |
| `Attempt to read property "nama_kategori" on null` | Produk menunjuk `kategori_id` yang sudah tidak ada | Pastikan foreign key ada (Tahap 3). Kategori yang dipakai produk tidak bisa dihapus (Tahap 4). |
| Halaman daftar lambat dengan banyak produk | Lupa `with('kategori')` (masalah N+1) | `Produk::with('kategori')->get()` |
| Folder `img-produk` penuh file tak terpakai | File lama tidak dihapus saat ganti foto atau hapus produk | Lihat `hapusFoto()` di `update()` dan `destroy()` |

---

## Perbedaan dengan modul (Laravel 10)

| Modul (Laravel 10) | Project ini (Laravel 13) |
|---|---|
| Folder `lang/` sudah ada sejak awal | Belum ada, lalu dibuat dengan `php artisan lang:publish` (perintah sama seperti modul) |
| `lang/id` biasanya diunduh dari paket terjemahan | Ditulis manual (terjemahan aturan yang dipakai + nama kolom). Aturan lain memakai cadangan bahasa Inggris. |
| Accessor gaya lama `getUrlFotoAttribute()` | Gaya baru `urlFoto(): Attribute`. Pemakaiannya sama: `$produk->url_foto`. |
| `make:controller ProdukController --resource` | Controller sudah ada sejak Tahap 2, jadi method resource ditulis langsung |
| `store()`, `Storage::disk('public')`, `storage:link`, `with()`, `exists:`, `image`, `mimes`, `max` | **Sama persis** |
