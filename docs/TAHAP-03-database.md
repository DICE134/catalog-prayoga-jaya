# Tahap 3 — Database: Migration, Model, Seeder

*Acuan: Modul Web Programming II, Minggu 3 dan Minggu 9–10 (data master dan seeder).*

## Tujuan

Membuat tabel untuk menyimpan **akun admin, kategori, produk, dan foto produk**, menghubungkannya ke Laravel lewat **model**, lalu mengisinya dengan **data contoh** supaya halaman nanti tidak kosong.

---

## Konsep: migration vs model vs seeder

| | Migration | Model | Seeder |
|---|---|---|---|
| **Letak** | `database/migrations/` | `app/Models/` | `database/seeders/` |
| **Fungsi** | Cetak biru **struktur** tabel (kolom dan tipe) | Penghubung **kode PHP ↔ tabel** (huruf **M** di MVC) | Pengisi **data awal** |
| **Analogi** | Denah gedung | Resepsionis yang tahu isi setiap ruangan | Perabot pertama yang dimasukkan |
| **Dijalankan** | `php artisan migrate` | Dipanggil dari controller, misal `Produk::all()` | `php artisan db:seed` |
| **Contoh isi** | `$table->string('nama_produk')` | `belongsTo(Kategori::class)` | `Produk::create([...])` |

**Kenapa struktur tabel tidak dibuat langsung di phpMyAdmin?** Migration adalah **kode**, jadi:
- bisa dijalankan ulang di komputer lain (komputer dosen, teman, atau server hosting) dengan satu perintah;
- setiap perubahan struktur tercatat rapi.

---

## Rancangan tabel dan relasi

```
┌──────────────┐        ┌──────────────────────┐        ┌───────────────┐
│  kategori    │        │       produk         │        │  foto_produk  │
├──────────────┤        ├──────────────────────┤        ├───────────────┤
│ id        PK │◄──┐    │ id                PK │◄──┐    │ id         PK │
│ nama_kategori│   └────│ kategori_id       FK │   └────│ produk_id  FK │
└──────────────┘  1 : N │ nama_produk          │  1 : N │ foto          │
                        │ merek, spesifikasi   │        └───────────────┘
                        │ satuan, harga        │
                        │ detail, foto         │        ┌───────────────┐
                        │ status, unggulan     │        │    users      │
                        │ created_at/updated_at│        ├───────────────┤
                        └──────────────────────┘        │ id, nama,     │
                                                        │ email,password│
                                                        │ role, status  │
                                                        └───────────────┘
```

- **PK** (*primary key*): kolom `id`, nomor unik setiap baris.
- **FK** (*foreign key*): kolom yang menyimpan `id` dari tabel lain, misalnya `produk.kategori_id` → `kategori.id`.
- **1 : N** (*one to many*): satu kategori punya banyak produk, tetapi satu produk hanya masuk satu kategori.

---

## Daftar file yang dibuat atau diubah

| File | Status | Isi |
|---|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | diubah | Kolom `users` disesuaikan: `nama`, `role`, `status`. `email_verified_at` dihapus. |
| `database/migrations/2026_11_22_215406_create_kategori_table.php` | baru | Tabel `kategori` |
| `database/migrations/2026_11_22_215408_create_produk_table.php` | baru | Tabel `produk` |
| `database/migrations/2026_11_22_215409_create_foto_produk_table.php` | baru | Tabel `foto_produk` |
| `app/Models/User.php` | diubah | `$guarded`, `$hidden`, `casts()` |
| `app/Models/Kategori.php` | baru | `$table`, `$guarded`, relasi `produk()` |
| `app/Models/Produk.php` | baru | `$table`, `$guarded`, `casts()`, relasi `kategori()` dan `fotoProduk()` |
| `app/Models/FotoProduk.php` | baru | `$table`, `$guarded`, relasi `produk()` |
| `database/factories/UserFactory.php` | diubah | Disesuaikan dengan kolom baru (dipakai saat testing) |
| `database/seeders/DatabaseSeeder.php` | diubah | 1 admin, 8 kategori, 12 produk contoh |

## Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:model Kategori -m` | Membuat model `Kategori` **dan** (`-m`) file migration-nya sekaligus. Diulang untuk `Produk` dan `FotoProduk`. |
| `php artisan migrate:fresh --seed` | **Menghapus semua tabel**, menjalankan ulang semua migration, lalu menjalankan seeder (`--seed`) |
| `php artisan tinker` | Konsol PHP interaktif untuk mencoba model, misal `App\Models\Produk::count()` |

> ⚠️ **`migrate:fresh` menghapus semua data di database yang aktif.** Aman dipakai selama pengembangan, tetapi **jangan** dijalankan saat website sudah berisi data asli. Untuk menambah tabel atau kolom baru, cukup buat migration baru lalu jalankan `php artisan migrate` (tanpa `fresh`).

### Catatan: nama tabel bahasa Indonesia

`make:model Kategori -m` otomatis membuat migration `create_kategoris_table` dengan tabel **`kategoris`**. Laravel mengira semua nama memakai bahasa Inggris, sehingga ia menambahkan "s" untuk bentuk jamak. Karena spesifikasi kita memakai `kategori`, ada **dua** hal yang harus diubah:

1. Di migration: `Schema::create('kategori', ...)`. Nama filenya juga diganti menjadi `create_kategori_table.php` supaya tidak membingungkan.
2. Di model: `protected $table = 'kategori';`. **Tanpa baris ini**, model akan mencari tabel `kategoris` dan muncul error *Table 'kategoris' doesn't exist*.

### Catatan: urutan migration

Migration dijalankan **berurutan sesuai waktu di nama file**. `produk` harus dibuat **setelah** `kategori`, karena `produk.kategori_id` merujuk ke `kategori.id`. Begitu juga `foto_produk` setelah `produk`. Karena itu ketiga model dibuat berurutan: Kategori → Produk → FotoProduk.

---

## Penjelasan kode penting

### Migration produk

```php
$table->foreignId('kategori_id')->constrained('kategori');
```

Baris ini membuat kolom `kategori_id` dan **foreign key** ke `kategori.id`. Database menolak dua hal:
- produk dengan `kategori_id` yang tidak ada di tabel `kategori`;
- penghapusan kategori yang masih punya produk. Ini akan kita tangani di Tahap 4.

```php
$table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();   // di foto_produk
```

`cascadeOnDelete` berarti jika sebuah produk dihapus, semua baris foto tambahannya ikut terhapus otomatis.

| Tipe kolom | Dipakai untuk | Alasan |
|---|---|---|
| `string('nama_produk', 150)` | teks pendek | maksimal 150 karakter |
| `text('detail')` | deskripsi panjang | bisa ribuan karakter |
| `unsignedInteger('harga')` | harga rupiah | bilangan bulat dan tidak bisa negatif. Rupiah tidak memakai sen, jadi tidak perlu desimal. |
| `tinyInteger('status')->default(1)` | 1/0 | angka kecil, otomatis Publis jika tidak diisi |
| `boolean('unggulan')->default(false)` | ya/tidak | otomatis bukan unggulan |
| `->nullable()` | `merek`, `spesifikasi`, `detail`, `foto` | boleh kosong |

### Model

```php
class Produk extends Model
{
    protected $table = 'produk';      // nama tabel
    protected $guarded = ['id'];      // kolom yang TIDAK boleh diisi massal

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function fotoProduk(): HasMany
    {
        return $this->hasMany(FotoProduk::class, 'produk_id');
    }
}
```

- **`$guarded = ['id']`** berarti semua kolom boleh diisi lewat `Produk::create([...])` **kecuali** `id`, karena `id` diatur otomatis oleh database. Kebalikannya adalah `$fillable`, yaitu daftar kolom yang **boleh** diisi.
- **`belongsTo`** artinya "produk ini **milik** satu kategori". Dengan relasi ini kita bisa menulis `$produk->kategori->nama_kategori` tanpa SQL `JOIN`.
- **`hasMany`** artinya "kategori ini **punya banyak** produk". Contohnya, `$kategori->produk` menghasilkan daftar produknya.
- **`casts()`** mengubah tipe data otomatis. Contohnya, `unggulan` dari database berupa `0`/`1` diubah menjadi `false`/`true`.
- **`public $timestamps = false`** (di `Kategori` dan `FotoProduk`) memberi tahu Laravel bahwa tabel ini tidak punya `created_at`/`updated_at`. Tanpa baris ini, `Kategori::create()` error *Unknown column 'updated_at'*.

### Model User

```php
protected function casts(): array
{
    return ['password' => 'hashed', ...];
}
```

Password **tidak pernah disimpan sebagai teks asli**. Yang tersimpan adalah hasil *hash* bcrypt, misalnya `$2y$12$...`. Hash tidak bisa dikembalikan ke password aslinya. Saat login, Laravel meng-hash password yang diketik lalu membandingkan hasilnya (dibahas lagi di Tahap 5).

### Seeder

```php
User::create([
    'email' => 'admin@prayogajaya.test',
    'password' => Hash::make('admin12345'),   // Hash::make = bcrypt
    'role' => 1,
]);
```

Produk contoh dihubungkan ke kategori **berdasarkan nama**: seeder menyimpan pasangan `['Listrik' => 8, ...]`, lalu setiap produk mengambil id kategorinya dari situ.

Setiap merek dan harga diberi komentar `// CONTOH — ganti dengan data asli`.

---

## Data yang terisi

**Akun admin (SEMENTARA, wajib diganti):**

| Email | Password | Role |
|---|---|---|
| `admin@prayogajaya.test` | `admin12345` | 1 (super admin) |

**8 kategori:** Semen & Perekat, Cat & Pelapis, Besi & Baja, Pipa & Sanitasi, Keramik & Lantai, Kayu & Triplek, Perkakas, Listrik.

**12 produk contoh:**
- **11 Publis + 1 Blok** (Gergaji Kayu). Spesifikasi meminta minimal 9 Publis, tetapi katalog menampilkan 9 produk per halaman, jadi 11 Publis diperlukan supaya halaman ke-2 muncul dan **pagination bisa diuji**.
- **6 unggulan:** Semen Portland, Cat Tembok, Besi Beton, Pipa PVC, Keramik, Kabel NYM.
- Kolom `foto` masih kosong (`null`). Foto diunggah lewat admin di Tahap 7, dan sementara halaman akan menampilkan gambar placeholder.

---

## Cara menguji

### Lewat phpMyAdmin

1. Di Laragon klik **Start All** (Apache dan MySQL harus menyala).
2. Klik tombol **Database** di Laragon, atau buka `http://localhost/phpmyadmin`. Login dengan user `root` dan password kosong.
3. Di panel kiri pilih **`db_prayogajaya_katalog`**.
4. Periksa:
   - **`kategori`** → tab *Browse* → 8 baris.
   - **`produk`** → tab *Browse* → 12 baris. Satu baris (Gergaji Kayu) ber-`status` 0.
   - **`produk`** → tab *Structure* → pada `kategori_id` terlihat ikon kunci (foreign key). Klik *Relation view* untuk melihat relasinya ke `kategori`.
   - **`users`** → kolom `password` berisi teks acak `$2y$12$...`, bukan `admin12345`.

### Lewat terminal (tinker)

```bash
php artisan tinker
```

```php
App\Models\Produk::count();                                  // 12
App\Models\Produk::where('status', 1)->count();              // 11
App\Models\Produk::first()->kategori->nama_kategori;         // "Semen & Perekat"
App\Models\Kategori::where('nama_kategori', 'Listrik')->first()->produk->pluck('nama_produk');
                                                             // ["Kabel Listrik NYM", "Saklar Tunggal"]
exit
```

Hasil saat tahap ini dikerjakan: users 1, kategori 8, produk 12 (11 Publis, 1 Blok, 6 unggulan). Kedua relasi berfungsi, password ter-hash bcrypt, `created_at` tercatat dalam WIB, dan halaman `/beranda` tetap HTTP 200.

---

## Kesalahan umum dan cara memperbaikinya

| Pesan error | Penyebab | Solusi |
|---|---|---|
| `Table 'db_prayogajaya_katalog.kategoris' doesn't exist` | Model tidak punya `$table = 'kategori'` | Tambahkan `protected $table = 'kategori';` |
| `Unknown column 'updated_at'` | Tabel tanpa timestamps tetapi model tidak diberi tahu | `public $timestamps = false;` di model |
| `Failed to open the referenced table 'kategori'` saat migrate | Migration `produk` berjalan **sebelum** `kategori` | Pastikan waktu di nama file `kategori` lebih awal dari `produk` |
| `Cannot add or update a child row: a foreign key constraint fails` | `kategori_id` berisi id yang tidak ada | Buat kategori dulu, lalu pakai id-nya |
| `Add [nama_produk] to fillable property...` (MassAssignmentException) | Model belum punya `$guarded`/`$fillable` | Tambahkan `protected $guarded = ['id'];` |
| `Unknown column 'name'` | Kode lama masih memakai `name`, padahal kolomnya sekarang `nama` | Ganti `name` → `nama` |
| `SQLSTATE[42S01]: Table already exists` | Migrate dijalankan pada database yang sudah berisi tabel | `php artisan migrate:fresh --seed` (**hati-hati: menghapus data**) |
| Seeder dijalankan dua kali → `Duplicate entry ... for key 'email'` | `php artisan db:seed` diulang tanpa mengosongkan tabel | Pakai `migrate:fresh --seed` |

---

## Perbedaan dengan modul (Laravel 10)

| Modul (Laravel 10) | Project ini (Laravel 13) |
|---|---|
| Model `User` memakai properti `$fillable`/`$guarded` | Bawaan Laravel 13 memakai atribut `#[Fillable([...])]`, tetapi **diganti ke `$guarded`** agar sama dengan modul. Keduanya sah. |
| Migration users bawaan punya kolom `name` | Sama, lalu kita ubah menjadi `nama` |
| Migration users terpisah dari sessions | Di Laravel 13, satu file migration users juga membuat tabel `password_reset_tokens` dan `sessions`. Bagian itu tidak diubah. |
| `make:model`, `-m`, `Schema::create`, `belongsTo`, `hasMany`, `migrate:fresh --seed` | **Sama persis** |
