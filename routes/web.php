<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TentangController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route halaman publik (frontend)
|--------------------------------------------------------------------------
| Pola: Route::get('URL', [NamaController::class, 'namaMethod'])->name('nama.route');
| - 'URL'        : alamat yang diketik di browser
| - Controller   : kelas dan method yang menangani permintaan
| - ->name(...)  : nama route, dipakai di view lewat route('nama.route')
*/

// Alamat utama "/" langsung dialihkan (redirect) ke halaman beranda.
Route::get('/', function () {
    return redirect()->route('beranda');
});

// Beranda
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

// Katalog produk: semua produk
Route::get('/produk/all', [ProdukController::class, 'produkAll'])->name('produk.all');

// Katalog produk per kategori. {id} = parameter, yaitu id kategori dari URL.
Route::get('/produk/kategori/{id}', [ProdukController::class, 'produkKategori'])->name('produk.kategori');

// Detail satu produk. {id} = id produk.
Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])->name('produk.detail');

// Tentang Kami
Route::get('/tentang', [TentangController::class, 'index'])->name('tentang');

// Kontak
Route::get('/kontak', [KontakController::class, 'index'])->name('kontak');

/*
|--------------------------------------------------------------------------
| Route halaman admin (backend)
|--------------------------------------------------------------------------
| prefix('backend')  : semua URL di dalam grup diawali /backend
| name('backend.')   : semua nama route di dalam grup diawali "backend."
*/
Route::prefix('backend')->name('backend.')->group(function () {

    // Middleware 'guest': hanya untuk yang BELUM login.
    // Admin yang sudah login lalu membuka /backend/login dialihkan ke dashboard.
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'loginBackend'])->name('login');

        // throttle:5,1 = maksimal 5 percobaan login per menit,
        // untuk menghambat orang yang mencoba-coba menebak password.
        Route::post('/login', [LoginController::class, 'authenticateBackend'])
            ->middleware('throttle:5,1')
            ->name('login.proses');
    });

    // Middleware 'auth': hanya untuk yang SUDAH login.
    // Tamu yang membuka halaman di bawah ini dialihkan ke /backend/login
    // (tujuan pengalihan diatur di bootstrap/app.php).
    Route::middleware('auth')->group(function () {
        // /backend langsung ke dashboard
        Route::get('/', function () {
            return redirect()->route('backend.beranda');
        });

        Route::get('/beranda', [BerandaController::class, 'berandaBackend'])->name('beranda');

        // Logout memakai POST (lewat form + @csrf), bukan link biasa.
        Route::post('/logout', [LoginController::class, 'logoutBackend'])->name('logout');

        // Satu baris ini mendaftarkan 6 route CRUD kategori:
        // index, create, store, edit, update, destroy (show tidak dipakai).
        Route::resource('kategori', KategoriController::class)->except(['show']);

        // CRUD produk: 6 route backend.produk.* (show tidak dipakai)
        Route::resource('produk', ProdukController::class)->except(['show']);

        // Foto tambahan produk (tabel foto_produk)
        Route::post('/produk/{produk}/foto', [ProdukController::class, 'storeFoto'])->name('produk.foto.store');
        Route::delete('/foto-produk/{fotoProduk}', [ProdukController::class, 'destroyFoto'])->name('produk.foto.destroy');
    });
});
