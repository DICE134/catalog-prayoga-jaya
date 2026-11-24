<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Menguji halaman Kontak (Tahap 13). Halaman ini tidak memakai database.
 */
class KontakTest extends TestCase
{
    private const EMBED = 'https://www.google.com/maps/embed?pb=!1m18!uji';

    public function test_kontak_menampilkan_data_toko_dari_config(): void
    {
        config([
            'toko.wa' => '6281111111111',
            'toko.alamat' => 'Jl. Kontak Uji No. 3',
            'toko.jam_buka' => 'Senin–Sabtu 07.00–17.00',
        ]);

        $this->get(route('kontak'))
            ->assertOk()
            ->assertSee('+6281111111111')
            ->assertSee('Jl. Kontak Uji No. 3')
            ->assertSee('Senin–Sabtu 07.00–17.00')
            ->assertSee('https://wa.me/6281111111111?text=Halo%20Prayoga%20Jaya%2C%20saya%20ingin%20bertanya', false)
            ->assertSee('Chat via WhatsApp');
    }

    public function test_peta_tertanam_tampil_jika_embed_diisi(): void
    {
        config(['toko.maps_embed' => self::EMBED, 'toko.maps_url' => '']);

        $this->get(route('kontak'))
            ->assertSee('<iframe src="'.self::EMBED.'"', false)
            ->assertDontSee('Buka di Google Maps');
    }

    public function test_embed_berupa_kode_iframe_lengkap_juga_diterima(): void
    {
        config(['toko.maps_embed' => '<iframe src="'.self::EMBED.'" width="600" height="450"></iframe>']);

        $this->get(route('kontak'))->assertSee('<iframe src="'.self::EMBED.'"', false);
    }

    public function test_embed_selain_google_maps_ditolak(): void
    {
        config(['toko.maps_embed' => 'https://situs-lain.example/halaman']);

        $this->get(route('kontak'))
            ->assertDontSee('<iframe', false)
            ->assertDontSee('situs-lain.example');
    }

    public function test_tombol_tautan_lokasi_jika_embed_kosong(): void
    {
        config(['toko.maps_embed' => '', 'toko.maps_url' => 'https://maps.app.goo.gl/contoh']);

        $this->get(route('kontak'))
            ->assertDontSee('<iframe', false)
            ->assertSee('href="https://maps.app.goo.gl/contoh"', false)
            ->assertSee('Buka di Google Maps');
    }

    public function test_pesan_placeholder_jika_lokasi_belum_diisi(): void
    {
        config(['toko.maps_embed' => '', 'toko.maps_url' => '']);

        $this->get(route('kontak'))
            ->assertSee('(Lokasi Google Maps belum diisi)')
            ->assertDontSee('Buka di Google Maps');
    }
}
