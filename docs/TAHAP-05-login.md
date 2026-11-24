# Tahap 5 — Autentikasi Login Admin

*Acuan: Modul Web Programming II, Minggu 5.*

## Tujuan

Mengunci semua halaman admin (`/backend/*`) supaya **hanya pemilik toko yang sudah login** yang bisa membukanya. Isinya: halaman login, proses login dengan `Auth::attempt`, logout, middleware `auth`, dan dashboard sederhana.

---

## Konsep

### Autentikasi dan session

- **Autentikasi** adalah proses memastikan "kamu benar-benar admin". Caranya dengan mencocokkan email dan password.
- **Session** adalah "ingatan" server tentang seorang pengunjung. Setelah login berhasil, Laravel menyimpan **id user** di session (di project ini session disimpan di tabel `sessions`). Browser hanya menyimpan **cookie** berisi kode acak penanda session. Karena itu admin tidak perlu login ulang setiap pindah halaman.

### Middleware

**Middleware** adalah lapisan pemeriksaan yang dilewati request **sebelum** sampai ke controller, mirip satpam di pintu masuk.

```
Request /backend/kategori
   │
   ▼
[middleware auth] ── belum login? ──► redirect ke /backend/login
   │ sudah login
   ▼
KategoriController@index
```

| Middleware | Untuk siapa | Jika tidak memenuhi |
|---|---|---|
| `auth` | yang **sudah** login | dialihkan ke `/backend/login` |
| `guest` | yang **belum** login | dialihkan ke `/backend/beranda` |
| `throttle:5,1` | semua | percobaan ke-6 dalam 1 menit ditolak (**429 Too Many Requests**) |

### Password disimpan ter-hash (bcrypt), bukan teks biasa

Di database, kolom `password` admin **tidak** berisi `admin12345`, melainkan semacam ini:

```
$2y$12$Kq0m3o3...(60 karakter acak)
```

Ini adalah **hash bcrypt**:
- **Satu arah:** hash **tidak bisa** diubah kembali menjadi password asli. Jika database bocor, password admin tetap tidak terbaca.
- **Selalu berbeda:** password yang sama menghasilkan hash yang berbeda setiap kali dibuat, karena ada "garam" (*salt*) acak. Dua admin dengan password sama tidak akan terlihat sama.
- **Sengaja lambat:** angka `12` pada `$2y$12$` adalah tingkat kesulitan. Membuat satu hash butuh beberapa milidetik, sehingga menebak jutaan password menjadi sangat lama.

**Lalu bagaimana login bisa mencocokkan?** `Auth::attempt` mengambil hash dari database, lalu memakai fungsi `Hash::check('password yang diketik', $hash)`. Fungsi ini meng-hash ketikan dengan *salt* yang sama lalu membandingkan hasilnya. Password asli **tidak pernah** disimpan atau dibandingkan secara langsung.

Di project ini password di-hash di dua tempat:
- `Hash::make('admin12345')` di seeder (Tahap 3). `Hash::make` memakai bcrypt, sama dengan fungsi `bcrypt()` di modul.
- `'password' => 'hashed'` di `casts()` model `User`, sehingga password otomatis di-hash setiap kali disimpan.

> **Konsekuensi:** jika admin lupa password, password **tidak bisa dilihat**. Yang bisa dilakukan hanyalah **mengganti** dengan password baru (lihat bagian "Mengganti password admin" di bawah).

---

## Daftar file yang dibuat atau diubah

| File | Status | Isi |
|---|---|---|
| `app/Http/Controllers/LoginController.php` | baru | `loginBackend`, `authenticateBackend`, `logoutBackend` |
| `app/Http/Controllers/BerandaController.php` | diubah | method baru `berandaBackend` (dashboard) |
| `resources/views/backend/v_login/login.blade.php` | baru | Form login |
| `resources/views/backend/v_beranda/index.blade.php` | baru | Dashboard: sapaan, 4 kartu angka, tombol logout |
| `resources/views/backend/v_kategori/index.blade.php` | diubah | Tombol "← Dashboard". Konfirmasi hapus memakai `data-konfirmasi`. |
| `routes/web.php` | diubah | Grup `guest` dan `auth` di dalam `backend` |
| `bootstrap/app.php` | diubah | Tujuan redirect tamu dan user yang sudah login |
| `tests/Feature/LoginTest.php` | baru | 10 test login/logout/perlindungan |

## Perintah yang dijalankan

| Perintah | Arti |
|---|---|
| `php artisan make:controller LoginController` | Membuat controller login |
| `php artisan make:view backend.v_login.login` | View form login |
| `php artisan make:view backend.v_beranda.index` | View dashboard |
| `php artisan make:test LoginTest --phpunit` | File test |
| `php artisan route:list --path=backend` | Melihat 11 route admin |
| `php artisan test` | Menjalankan semua test |

---

## Penjelasan kode penting

### 1. Route

```php
Route::prefix('backend')->name('backend.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'loginBackend'])->name('login');
        Route::post('/login', [LoginController::class, 'authenticateBackend'])
            ->middleware('throttle:5,1')
            ->name('login.proses');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('backend.beranda'));
        Route::get('/beranda', [BerandaController::class, 'berandaBackend'])->name('beranda');
        Route::post('/logout', [LoginController::class, 'logoutBackend'])->name('logout');
        Route::resource('kategori', KategoriController::class)->except(['show']);
    });
});
```

Semua route admin baru di tahap berikutnya, misalnya produk di Tahap 7, **cukup ditaruh di dalam grup `auth`**, maka otomatis terlindungi.

### 2. Tujuan pengalihan (`bootstrap/app.php`)

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->redirectGuestsTo(fn () => route('backend.login'));
    $middleware->redirectUsersTo(fn () => route('backend.beranda'));
})
```

Secara bawaan, Laravel mengalihkan tamu ke route bernama `login`. Route login kita bernama `backend.login`, jadi tujuannya perlu diberitahukan di sini.

### 3. Proses login (`authenticateBackend`)

```php
$kredensial = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
], [...pesan Indonesia...]);

$berhasil = Auth::attempt(
    [...$kredensial, 'status' => 1],   // email + password + HANYA akun aktif
    $request->boolean('ingat'),         // true jika "Ingat saya" dicentang
);

if (! $berhasil) {
    return back()
        ->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])
        ->onlyInput('email');
}

$request->session()->regenerate();
return redirect()->intended(route('backend.beranda'));
```

| Bagian | Penjelasan |
|---|---|
| `[...$kredensial, 'status' => 1]` | `...` menggabungkan array. Hasilnya `['email' => ..., 'password' => ..., 'status' => 1]`. Kolom selain `password` dicocokkan apa adanya, sehingga akun berstatus 0 (nonaktif) tidak bisa login. |
| Pesan error umum | Pesan sengaja **tidak** menyebut "email tidak terdaftar" atau "password salah", supaya orang tidak bisa menebak email mana yang terdaftar. |
| `onlyInput('email')` | Setelah gagal, email tetap terisi tetapi password dikosongkan. |
| `session()->regenerate()` | Mengganti kode session setelah login. Ini mencegah serangan *session fixation*, yaitu ketika penyerang menyiapkan kode session lebih dulu lalu ikut "login" bersama korban. |
| `redirect()->intended(...)` | Jika admin tadinya membuka `/backend/kategori` lalu diminta login, setelah login ia dikembalikan ke `/backend/kategori`, bukan ke dashboard. |

### 4. Logout (`logoutBackend`)

```php
Auth::logout();
$request->session()->invalidate();      // hapus seluruh isi session
$request->session()->regenerateToken(); // token CSRF baru
```

Logout memakai **POST + `@csrf`**, bukan link GET. Jika memakai GET, situs lain bisa memasang `<img src=".../backend/logout">` untuk memaksa admin keluar.

### 5. View

```blade
<p>Selamat datang, {{ Auth::user()->nama }}</p>

<form action="{{ route('backend.logout') }}" method="POST">
    @csrf
    <button type="submit">Logout</button>
</form>
```

`Auth::user()` mengembalikan model `User` yang sedang login, sehingga semua kolomnya bisa dipakai (`nama`, `email`, `role`).

---

## Akun admin sementara

| Email | Password |
|---|---|
| `admin@prayogajaya.test` | `admin12345` |

### Mengganti password admin

Halaman "ubah password" belum ada. Untuk sementara, ganti lewat tinker:

```bash
php artisan tinker
```

```php
$admin = App\Models\User::where('email', 'admin@prayogajaya.test')->first();
$admin->update(['email' => 'email-asli@domain.com', 'password' => 'PasswordBaruYangKuat']);
exit
```

Password otomatis di-hash karena ada cast `'password' => 'hashed'`.

---

## Cara menguji di browser

1. `php artisan serve`.
2. **Tanpa login**, buka `http://localhost:8000/backend/kategori`. Browser dialihkan ke halaman login.
3. Login dengan **password salah**. Muncul pesan merah *"Email atau password salah, atau akun tidak aktif."* dan email tetap terisi.
4. Login dengan `admin@prayogajaya.test` / `admin12345`. Browser masuk ke **Dashboard** dengan sapaan *"Selamat datang, Administrator"* dan 4 kartu: 8 kategori, 11 publis, 1 blok, 6 unggulan.
5. Saat masih login, buka `http://localhost:8000/backend/login`. Browser langsung dialihkan ke dashboard.
6. Klik **Data Kategori**. Halaman CRUD dari Tahap 4 terbuka.
7. Klik **Logout**. Browser kembali ke halaman login dengan pesan *"Anda berhasil logout."*
8. Tekan tombol **Back** di browser lalu refresh. Browser dialihkan ke login lagi.
9. (Opsional) Login salah 6 kali dalam 1 menit. Muncul halaman **429 Too Many Requests**. Tunggu 1 menit.
10. Di phpMyAdmin buka tabel `users` dan lihat bahwa `password` berisi `$2y$12$...`, bukan teks asli.

```bash
php artisan test --filter=LoginTest
```

Hasil saat tahap ini dikerjakan: **22 test lulus** (10 di antaranya test login). Uji langsung di server juga sesuai: tamu dialihkan ke login, password salah ditolak, password benar masuk ke dashboard, logout berhasil, dan setelah logout dashboard tidak bisa dibuka.

---

## Kesalahan umum dan cara memperbaikinya

| Pesan error / gejala | Penyebab | Solusi |
|---|---|---|
| `Route [login] not defined` | Tamu dialihkan ke route bawaan `login` yang tidak ada | Tambahkan `redirectGuestsTo(...)` di `bootstrap/app.php` |
| Login selalu gagal walau password benar | Password di database tersimpan **tidak ter-hash** (diisi manual di phpMyAdmin) | Ganti password lewat tinker atau seeder (otomatis di-hash) |
| Login selalu gagal untuk satu akun | `status` akun = 0 (nonaktif) | Ubah `status` menjadi 1 |
| **419 Page Expired** saat login/logout | Lupa `@csrf`, atau halaman login dibiarkan terlalu lama | Tambahkan `@csrf`, lalu refresh halaman |
| **429 Too Many Requests** | Lebih dari 5 percobaan login per menit | Tunggu 1 menit |
| Redirect berputar-putar (*too many redirects*) | Route login ikut dimasukkan ke grup `auth` | Route login harus di grup `guest` |
| Logout lewat link `<a>` → 405 Method Not Allowed | Route logout hanya menerima POST | Gunakan form POST + `@csrf` |
| `Attempt to read property "nama" on null` | Memanggil `Auth::user()->nama` di halaman tanpa middleware `auth` | Pastikan route ada di grup `auth` |

---

## Perbedaan dengan modul (Laravel 10)

| Modul (Laravel 10) | Project ini (Laravel 13) |
|---|---|
| Tujuan redirect tamu diatur di `app/Http/Middleware/Authenticate.php` | File itu tidak ada. Diganti `$middleware->redirectGuestsTo(...)` di `bootstrap/app.php`. |
| Tujuan redirect user login diatur di `app/Http/Middleware/RedirectIfAuthenticated.php` (konstanta `RouteServiceProvider::HOME`) | Diganti `$middleware->redirectUsersTo(...)` di `bootstrap/app.php` |
| Middleware didaftarkan di `app/Http/Kernel.php` | Tidak ada Kernel. Nama middleware `auth`, `guest`, dan `throttle` sudah tersedia. |
| `bcrypt('...')` | `Hash::make('...')` (sama-sama bcrypt) + cast `'hashed'` di model |
| `Auth::attempt`, `Auth::logout`, `session()->regenerate()`, middleware `auth` | **Sama persis** |
