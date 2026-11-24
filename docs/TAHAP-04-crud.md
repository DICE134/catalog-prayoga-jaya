# Tahap 4 — Konsep CRUD

*Acuan: Modul Web Programming II, Minggu 4.*

## Tujuan

Memahami pola **CRUD** (Create, Read, Update, Delete) dan `Route::resource` lewat contoh nyata: **mengelola kategori** di halaman admin. Pola yang sama akan dipakai lagi untuk produk di Tahap 7.

> ⚠️ Halaman `/backend/kategori` **belum dilindungi login**. Siapa pun yang tahu alamatnya bisa membuka. Ini diperbaiki di Tahap 5.

---

## Konsep

### CRUD dan 7 method resource controller

| Method | HTTP + URL | Nama route | Tugas | CRUD |
|---|---|---|---|---|
| `index` | GET `/backend/kategori` | `backend.kategori.index` | Daftar semua data | **R**ead |
| `create` | GET `/backend/kategori/create` | `backend.kategori.create` | Tampilkan form tambah | – |
| `store` | POST `/backend/kategori` | `backend.kategori.store` | Simpan data baru | **C**reate |
| `show` | GET `/backend/kategori/{id}` | – | Detail satu data | *tidak dipakai* |
| `edit` | GET `/backend/kategori/{id}/edit` | `backend.kategori.edit` | Tampilkan form ubah | – |
| `update` | PUT `/backend/kategori/{id}` | `backend.kategori.update` | Simpan perubahan | **U**pdate |
| `destroy` | DELETE `/backend/kategori/{id}` | `backend.kategori.destroy` | Hapus data | **D**elete |

Perhatikan bahwa **satu URL bisa punya beberapa arti** tergantung *method HTTP*-nya. `/backend/kategori` dengan GET berarti "tampilkan daftar", sedangkan dengan POST berarti "simpan data baru".

### Alur tambah data

```
[index] klik "+ Tambah" → GET create → form tampil
   → isi form, klik Simpan → POST store → validasi
        ├─ GAGAL  → kembali ke form + pesan merah + ketikan lama tetap ada
        └─ BERHASIL → simpan ke database → redirect ke index + pesan hijau
```

Pola **"POST lalu redirect"** dipakai supaya data tidak tersimpan dua kali jika pengguna menekan F5 (refresh) setelah menyimpan.

---

## Daftar file yang dibuat atau diubah

| File | Status | Isi |
|---|---|---|
| `app/Http/Controllers/KategoriController.php` | baru | 6 method CRUD + pesan validasi |
| `resources/views/backend/v_kategori/index.blade.php` | baru | Tabel daftar kategori, tombol ubah/hapus, pesan flash |
| `resources/views/backend/v_kategori/create.blade.php` | baru | Form tambah |
| `resources/views/backend/v_kategori/edit.blade.php` | baru | Form ubah |
| `routes/web.php` | diubah | Grup `backend` + `Route::resource('kategori', ...)` |
| `tests/Feature/KategoriCrudTest.php` | baru | 10 test CRUD kategori |

## Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:controller KategoriController --resource --model=Kategori` | Membuat controller dengan kerangka 7 method. `--model` menyiapkan parameter `Kategori $kategori`. |
| `php artisan make:view backend.v_kategori.index` (juga `create`, `edit`) | Membuat file view |
| `php artisan make:test KategoriCrudTest --phpunit` | Membuat file test di `tests/Feature/` |
| `php artisan route:list --path=backend` | Melihat route yang dibuat `Route::resource` |
| `php artisan test` | Menjalankan semua test |

Method `show` dihapus dari controller karena kategori tidak butuh halaman detail. Route-nya juga dikecualikan dengan `->except(['show'])`.

---

## Penjelasan kode penting

### 1. Route

```php
Route::prefix('backend')->name('backend.')->group(function () {
    Route::resource('kategori', KategoriController::class)->except(['show']);
});
```

- `prefix('backend')`: semua URL di dalam grup diawali `/backend`.
- `name('backend.')`: semua nama route diawali `backend.`, sehingga `kategori.index` menjadi `backend.kategori.index`. Ini mencegah bentrok dengan route publik bernama `produk.*` nanti.
- `Route::resource(...)`: **satu baris** setara 6 baris `Route::get/post/put/delete`.

### 2. Controller: READ (`index`)

```php
$kategori = Kategori::withCount('produk')->orderBy('nama_kategori')->get();
```

- `withCount('produk')` memakai relasi `produk()` dari Tahap 3 untuk menambahkan atribut `produk_count`.
- `orderBy` mengurutkan berdasarkan nama (A–Z), lalu `get()` mengambil semua baris.

### 3. Controller: CREATE (`store`) dan validasi

```php
$data = $request->validate([
    'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
], $this->pesanValidasi());

Kategori::create($data);

return redirect()->route('backend.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
```

| Aturan | Arti |
|---|---|
| `required` | wajib diisi |
| `string` | harus berupa teks |
| `max:100` | maksimal 100 karakter (sesuai panjang kolom di migration) |
| `unique:kategori,nama_kategori` | belum ada di tabel `kategori` kolom `nama_kategori` |

- Jika validasi **gagal**, Laravel otomatis kembali ke form dan baris di bawahnya **tidak dijalankan**.
- `$data` hanya berisi kolom yang lolos validasi, sehingga aman dipakai di `Kategori::create($data)`.
- `->with('success', ...)` adalah **flash message**: pesan yang tersimpan di session untuk **satu kali** tampil saja.
- Pesan validasi ditulis dalam Bahasa Indonesia di method `pesanValidasi()`. Di Tahap 7, pesan umum dipindah ke `lang/id/validation.php`.

### 4. Controller: UPDATE dan aturan `unique` yang mengabaikan diri sendiri

```php
Rule::unique('kategori', 'nama_kategori')->ignore($kategori->id),
```

Tanpa `ignore`, menyimpan form ubah **tanpa mengganti nama** akan gagal dengan pesan "sudah ada", karena nama itu memang sudah ada, yaitu milik kategori itu sendiri. `ignore($kategori->id)` artinya "abaikan baris milik kategori ini saat mengecek kembar".

### 5. Route model binding

```php
public function edit(Kategori $kategori): View
```

Nama parameter `$kategori` cocok dengan `{kategori}` di URL. Laravel otomatis menjalankan `Kategori::findOrFail($id)`. Jika id tidak ada, misalnya `/backend/kategori/999/edit`, hasilnya **404** tanpa perlu kode tambahan.

### 6. Controller: DELETE (`destroy`)

```php
if ($kategori->produk()->exists()) {
    return redirect()->route('backend.kategori.index')
        ->with('error', 'Kategori "..." tidak bisa dihapus karena masih dipakai oleh produk.');
}
$kategori->delete();
```

Di Tahap 3, foreign key `produk.kategori_id` membuat database **menolak** penghapusan kategori yang masih punya produk. Tanpa pengecekan ini, admin akan melihat halaman error SQL yang menakutkan. Dengan pengecekan ini, admin melihat pesan merah yang jelas.

### 7. View: form, `@csrf`, `@method`, dan `invalid-feedback`

```blade
<form action="{{ route('backend.kategori.update', $kategori->id) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="nama_kategori"
           class="form-control @error('nama_kategori') is-invalid @enderror"
           value="{{ old('nama_kategori', $kategori->nama_kategori) }}">
    @error('nama_kategori')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</form>
```

| Kode | Fungsi |
|---|---|
| `@csrf` | Menyisipkan token rahasia. Laravel menolak form tanpa token (**error 419**). Ini melindungi dari serangan *CSRF*, yaitu situs lain yang diam-diam mengirim form atas nama admin. |
| `@method('PUT')` / `@method('DELETE')` | Form HTML hanya bisa GET/POST. Baris ini menyisipkan `<input name="_method" value="PUT">` agar Laravel memperlakukannya sebagai PUT/DELETE. |
| `@error('nama_kategori') is-invalid @enderror` | Menambah class Bootstrap `is-invalid` (garis merah) **hanya jika** kolom ini gagal validasi |
| `<div class="invalid-feedback">{{ $message }}</div>` | Teks merah di bawah kolom. `$message` berisi pesan dari `pesanValidasi()`. |
| `old('nama_kategori', $kategori->nama_kategori)` | Setelah validasi gagal, tampilkan **ketikan terakhir**. Jika tidak, tampilkan nilai dari database. |
| `@forelse ... @empty ... @endforelse` | Perulangan dengan bagian khusus jika data kosong |
| `$loop->iteration` | Nomor urut 1, 2, 3, … di dalam perulangan |
| `confirm(@js(...))` | Kotak konfirmasi sebelum menghapus. `@js()` mengamankan teks agar tanda petik di nama tidak merusak JavaScript. |

**Kenapa tombol hapus berupa form, bukan link `<a href>`?** Link selalu memakai GET. Jika hapus bisa lewat GET, maka mesin pencari, *preview* tautan, atau sekadar membuka URL bisa tidak sengaja menghapus data.

---

## Cara menguji di browser

1. `php artisan serve`, lalu buka **`http://localhost:8000/backend/kategori`**. Tabel 8 kategori muncul beserta jumlah produknya.
2. **Tambah:** klik **+ Tambah Kategori**, isi `Atap & Genteng`, lalu Simpan. Kembali ke daftar dan muncul pesan hijau.
3. **Validasi kosong:** Tambah → kosongkan → Simpan. Kolom bergaris merah dengan pesan *"Nama kategori wajib diisi."*
4. **Validasi kembar:** Tambah → isi `Listrik` → Simpan. Muncul *"Nama kategori sudah ada, gunakan nama lain."* dan ketikan tetap ada.
5. **Ubah:** klik **Ubah** pada `Atap & Genteng`, ganti menjadi `Atap, Genteng & Plafon`, lalu Simpan.
6. **Hapus (berhasil):** klik **Hapus** pada kategori baru tadi, lalu OK. Data hilang dan muncul pesan hijau.
7. **Hapus (ditolak):** klik **Hapus** pada `Listrik`, lalu OK. Muncul pesan merah karena kategori itu masih dipakai produk.
8. **404:** buka `http://localhost:8000/backend/kategori/999/edit`, maka muncul halaman 404.

Dengan test otomatis:

```bash
php artisan test --filter=KategoriCrudTest
```

Hasil saat tahap ini dikerjakan: **10 test kategori lulus** (total 12 test termasuk test sebelumnya). Halaman index, create, dan edit membalas 200 dengan data MySQL, dan id tak dikenal membalas 404.

> Test memakai database **SQLite sementara di memori** (lihat `phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`). Jadi `RefreshDatabase` **tidak** menghapus data di MySQL Anda.

---

## Kesalahan umum dan cara memperbaikinya

| Pesan error | Penyebab | Solusi |
|---|---|---|
| **419 Page Expired** | Lupa `@csrf` di form, atau halaman dibiarkan terbuka terlalu lama | Tambahkan `@csrf`, lalu refresh halaman |
| **405 Method Not Allowed** | Form ubah/hapus tanpa `@method('PUT')`/`@method('DELETE')` | Tambahkan `@method(...)` |
| Pesan error validasi tidak muncul | Lupa `@error(...)` atau class `invalid-feedback` tidak berada **tepat setelah** input `is-invalid` | Ikuti struktur di atas |
| Isian hilang setelah validasi gagal | `value` tidak memakai `old('...')` | `value="{{ old('nama_kategori') }}"` |
| Ubah tanpa ganti nama → "sudah ada" | Aturan `unique` tanpa `ignore` | `Rule::unique(...)->ignore($kategori->id)` |
| `Integrity constraint violation: 1451 Cannot delete or update a parent row` | Menghapus kategori yang dipakai produk tanpa pengecekan | Cek `$kategori->produk()->exists()` sebelum `delete()` |
| `Route [kategori.index] not defined` | Nama route kurang awalan `backend.` | `route('backend.kategori.index')`, cek dengan `php artisan route:list --path=backend` |
| `Missing required parameter for [Route: backend.kategori.edit]` | `route(...)` tanpa id | `route('backend.kategori.edit', $row->id)` |

---

## Perbedaan dengan modul (Laravel 10)

| Modul (Laravel 10) | Project ini (Laravel 13) |
|---|---|
| View dibuat manual | `php artisan make:view` |
| `make:controller --resource`, `Route::resource`, `validate()`, `@csrf`, `@method`, `@error`, `old()` | **Sama persis** |
| Pesan validasi Indonesia via `lang/id` | Ditulis sementara di controller. Folder `lang/id` dibuat di Tahap 7 sesuai urutan file panduan. |
