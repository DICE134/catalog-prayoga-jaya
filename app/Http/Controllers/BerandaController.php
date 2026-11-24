<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\View\View;

/**
 * Controller halaman Beranda (halaman utama website).
 */
class BerandaController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     * Dipanggil oleh route bernama 'beranda' (URL: /beranda).
     */
    public function index(): View
    {
        // Kategori + jumlah produk PUBLIS di tiap kategori.
        // withCount(['produk' => ...]) menambah kolom "produk_count" tanpa mengambil semua data produk.
        $kategori = Kategori::withCount(['produk' => fn ($query) => $query->where('status', 1)])
            ->orderBy('nama_kategori')
            ->get();

        // Produk unggulan: hanya yang Publis (status 1) DAN ditandai unggulan, maksimal 6.
        // with('kategori') mengambil nama kategori sekaligus (menghindari N+1 query).
        $produkUnggulan = Produk::with('kategori')
            ->where('status', 1)
            ->where('unggulan', 1)
            ->latest()
            ->take(6)
            ->get();

        // view('v_beranda.index') = file resources/views/v_beranda/index.blade.php
        // Isi array menjadi variabel di view: $judul, $kategori, $produkUnggulan.
        return view('v_beranda.index', [
            'judul' => 'Beranda',
            'kategori' => $kategori,
            'produkUnggulan' => $produkUnggulan,
        ]);
    }

    /**
     * Dashboard admin setelah login.
     * Route: 'backend.beranda' (URL: /backend/beranda), dilindungi middleware 'auth'.
     */
    public function berandaBackend(): View
    {
        return view('backend.v_beranda.index', [
            'judul' => 'Dashboard',
            // Ringkasan angka untuk kartu di dashboard
            'jumlahKategori' => Kategori::count(),
            'jumlahPublis' => Produk::where('status', 1)->count(),
            'jumlahBlok' => Produk::where('status', 0)->count(),
            'jumlahUnggulan' => Produk::where('unggulan', true)->count(),
        ]);
    }
}
