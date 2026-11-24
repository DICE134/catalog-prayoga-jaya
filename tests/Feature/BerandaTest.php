<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji halaman Beranda (Tahap 9).
 */
class BerandaTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_terbuka_dan_memuat_semua_bagian(): void
    {
        $this->get(route('beranda'))
            ->assertOk()
            ->assertSeeInOrder([
                'class="hero',            // 2. hero
                'Selamat Datang di',      // 3. pengenalan
                'Kategori Produk',        // 4. kategori
                'Produk Unggulan',        // 5. unggulan
                'Kenapa Belanja di Sini?', // 6. keunggulan
                'Lihat Katalog',          // 7. tombol katalog
                'Hubungi Kami',           // 8. footer
            ], false);
    }

    public function test_kategori_tampil_dengan_tautan_dan_jumlah_produk_publis(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Cat & Pelapis']);
        Produk::factory()->count(2)->for($kategori)->create();
        Produk::factory()->blok()->for($kategori)->create(); // tidak ikut dihitung

        $this->get(route('beranda'))
            ->assertSee('Cat &amp; Pelapis', false)
            ->assertSee(route('produk.kategori', $kategori->id), false)
            ->assertSee('2 produk');
    }

    public function test_produk_unggulan_hanya_yang_publis_dan_unggulan(): void
    {
        Produk::factory()->unggulan()->create(['nama_produk' => 'Semen Unggulan', 'harga' => 68000]);
        Produk::factory()->unggulan()->blok()->create(['nama_produk' => 'Unggulan Tapi Blok']);
        Produk::factory()->create(['nama_produk' => 'Produk Biasa', 'unggulan' => false]);

        $this->get(route('beranda'))
            ->assertSee('Semen Unggulan')
            ->assertSee('Rp. 68.000')
            ->assertDontSee('Unggulan Tapi Blok')
            ->assertDontSee('Produk Biasa');
    }

    public function test_produk_unggulan_maksimal_enam(): void
    {
        Produk::factory()->count(8)->unggulan()->create();

        $this->get(route('beranda'))
            ->assertViewHas('produkUnggulan', fn ($produk) => $produk->count() === 6);
    }

    public function test_pesan_ramah_jika_belum_ada_produk_unggulan(): void
    {
        $this->get(route('beranda'))->assertSee('Belum ada produk unggulan.');
    }
}
