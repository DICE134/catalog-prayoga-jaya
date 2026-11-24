<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Controller halaman Kontak.
 */
class KontakController extends Controller
{
    /**
     * Menampilkan halaman Kontak.
     * Route: 'kontak' (URL: /kontak).
     */
    public function index(): View
    {
        // Pesan pembuka WhatsApp. rawurlencode() agar spasi dan koma aman di URL.
        $pesan = 'Halo Prayoga Jaya, saya ingin bertanya';
        $linkWhatsapp = 'https://wa.me/'.config('toko.wa').'?text='.rawurlencode($pesan);

        return view('v_kontak.index', [
            'judul' => 'Kontak',
            'linkWhatsapp' => $linkWhatsapp,
            'mapsEmbed' => $this->alamatPetaTertanam((string) config('toko.maps_embed')),
            'mapsUrl' => (string) config('toko.maps_url'),
        ]);
    }

    /**
     * Mengambil alamat (src) peta tertanam Google Maps dari TOKO_MAPS_EMBED.
     *
     * Menerima dua bentuk isian:
     *   1. alamatnya saja:  https://www.google.com/maps/embed?pb=...
     *   2. seluruh kode <iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>
     *      (yang biasanya disalin dari menu "Bagikan → Sematkan peta")
     *
     * Hanya alamat dari google.com/maps/embed yang diterima, supaya halaman tidak bisa
     * dipakai menampilkan situs lain. Jika tidak valid atau kosong → null (peta tidak tampil).
     */
    private function alamatPetaTertanam(string $isian): ?string
    {
        // Bentuk 2: ambil isi atribut src="..." dari kode iframe
        if (preg_match('/src="([^"]+)"/', $isian, $cocok)) {
            $isian = html_entity_decode($cocok[1]);
        }

        $isian = trim($isian);

        return Str::startsWith($isian, 'https://www.google.com/maps/embed') ? $isian : null;
    }
}
