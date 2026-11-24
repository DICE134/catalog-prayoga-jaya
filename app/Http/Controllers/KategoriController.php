<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD kategori di halaman admin (backend).
 * Dibuat dengan: php artisan make:controller KategoriController --resource --model=Kategori
 * Route-nya didaftarkan sekaligus lewat Route::resource() di routes/web.php.
 */
class KategoriController extends Controller
{
    /**
     * READ — menampilkan daftar semua kategori.
     * GET /backend/kategori
     */
    public function index(): View
    {
        // withCount('produk') menambah kolom produk_count = jumlah produk di setiap kategori,
        // dihitung lewat relasi produk() di model Kategori.
        $kategori = Kategori::withCount('produk')
            ->orderBy('nama_kategori')
            ->get();

        return view('backend.v_kategori.index', [
            'judul' => 'Data Kategori',
            'kategori' => $kategori,
        ]);
    }

    /**
     * Menampilkan form tambah kategori.
     * GET /backend/kategori/create
     */
    public function create(): View
    {
        return view('backend.v_kategori.create', [
            'judul' => 'Tambah Kategori',
        ]);
    }

    /**
     * CREATE — menyimpan kategori baru dari form tambah.
     * POST /backend/kategori
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi: jika gagal, Laravel otomatis kembali ke form beserta pesan error
        // dan isian sebelumnya (old input). Kode di bawahnya TIDAK dijalankan.
        // Pesan error otomatis Bahasa Indonesia dari lang/id/validation.php
        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori,nama_kategori'],
        ]);

        Kategori::create($data);

        // with('success', ...) = pesan sekali tampil (flash message) di halaman tujuan.
        return redirect()->route('backend.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan form ubah kategori.
     * GET /backend/kategori/{kategori}/edit
     *
     * Route model binding: Laravel otomatis mencari Kategori berdasarkan id di URL.
     * Jika id tidak ada di database → halaman 404.
     */
    public function edit(Kategori $kategori): View
    {
        return view('backend.v_kategori.edit', [
            'judul' => 'Ubah Kategori',
            'kategori' => $kategori,
        ]);
    }

    /**
     * UPDATE — menyimpan perubahan kategori.
     * PUT /backend/kategori/{kategori}
     */
    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $data = $request->validate([
            // ignore($kategori->id): nama boleh sama dengan nama kategori ITU SENDIRI
            // (misalnya disimpan tanpa diubah), tapi tidak boleh sama dengan kategori lain.
            'nama_kategori' => [
                'required', 'string', 'max:100',
                Rule::unique('kategori', 'nama_kategori')->ignore($kategori->id),
            ],
        ]);

        $kategori->update($data);

        return redirect()->route('backend.kategori.index')
            ->with('success', 'Kategori berhasil diubah.');
    }

    /**
     * DELETE — menghapus kategori.
     * DELETE /backend/kategori/{kategori}
     */
    public function destroy(Kategori $kategori): RedirectResponse
    {
        // Kategori yang masih dipakai produk tidak boleh dihapus.
        // (Database juga akan menolak karena foreign key produk.kategori_id.)
        if ($kategori->produk()->exists()) {
            return redirect()->route('backend.kategori.index')
                ->with('error', 'Kategori "'.$kategori->nama_kategori.'" tidak bisa dihapus karena masih dipakai oleh produk.');
        }

        $kategori->delete();

        return redirect()->route('backend.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
