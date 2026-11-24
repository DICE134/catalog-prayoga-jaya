<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\View\View;

/**
 * Controller halaman Tentang Kami.
 */
class TentangController extends Controller
{
    /**
     * Menampilkan halaman Tentang Kami.
     * Route: 'tentang' (URL: /tentang).
     */
    public function index(): View
    {
        return view('v_tentang.index', [
            'judul' => 'Tentang Kami',
            // "Jenis material yang dijual" diambil OTOMATIS dari tabel kategori,
            // jadi jika admin menambah kategori baru, halaman ini ikut berubah.
            'daftarKategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }
}
