<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji halaman Detail Produk (Tahap 11).
 */
class DetailProdukTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_menampilkan_informasi_lengkap(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Semen & Perekat']);
        $produk = Produk::factory()->for($kategori)->create([
            'nama_produk' => 'Semen Portland',
            'merek' => 'Merek Uji',
            'spesifikasi' => '50 kg',
            'satuan' => 'sak',
            'harga' => 68000,
            'detail' => "Baris pertama\nBaris kedua",
        ]);

        $this->get(route('produk.detail', $produk->id))
            ->assertOk()
            ->assertSee('Semen Portland')
            ->assertSee('Merek Uji')
            ->assertSee('50 kg')
            ->assertSee('Rp. 68.000')
            ->assertSee('per sak')
            ->assertSee('Semen &amp; Perekat', false)
            ->assertSee('Baris pertama<br />', false);
    }

    public function test_produk_blok_menghasilkan_404(): void
    {
        $produk = Produk::factory()->blok()->create();

        $this->get(route('produk.detail', $produk->id))->assertNotFound();
    }

    public function test_produk_tidak_ada_menghasilkan_404(): void
    {
        $this->get(route('produk.detail', 999))->assertNotFound();
    }

    public function test_tombol_whatsapp_berisi_pesan_nama_produk(): void
    {
        config(['toko.wa' => '6281234567890']);
        $produk = Produk::factory()->create(['nama_produk' => 'Cat Tembok & Kayu']);

        // rawurlencode: spasi → %20, & → %26
        $this->get(route('produk.detail', $produk->id))
            ->assertSee(
                'https://wa.me/6281234567890?text=Halo%20Prayoga%20Jaya%2C%20saya%20ingin%20bertanya%20tentang%20Cat%20Tembok%20%26%20Kayu',
                false
            );
    }

    public function test_foto_tambahan_tampil_sebagai_galeri(): void
    {
        $produk = Produk::factory()->create(['foto' => 'utama.jpg']);
        $produk->fotoProduk()->create(['foto' => 'tambahan.jpg']);

        $this->get(route('produk.detail', $produk->id))
            ->assertSee('storage/img-produk/utama.jpg', false)
            ->assertSee('data-foto="'.asset('storage/img-produk/tambahan.jpg').'"', false);
    }

    public function test_produk_serupa_dari_kategori_sama_maksimal_empat(): void
    {
        $kategori = Kategori::factory()->create();
        $produk = Produk::factory()->for($kategori)->create(['nama_produk' => 'Produk Utama']);
        Produk::factory()->count(5)->for($kategori)->create();
        Produk::factory()->blok()->for($kategori)->create(['nama_produk' => 'Serupa Tapi Blok']);
        Produk::factory()->create(['nama_produk' => 'Kategori Lain']);

        $this->get(route('produk.detail', $produk->id))
            ->assertSee('Produk Serupa')
            ->assertDontSee('Serupa Tapi Blok')
            ->assertDontSee('Kategori Lain')
            ->assertViewHas('produkSerupa', fn ($serupa) => $serupa->count() === 4
                && ! $serupa->contains('id', $produk->id));
    }

    public function test_bagian_produk_serupa_disembunyikan_jika_kosong(): void
    {
        $produk = Produk::factory()->create();

        $this->get(route('produk.detail', $produk->id))->assertDontSee('Produk Serupa');
    }
}
