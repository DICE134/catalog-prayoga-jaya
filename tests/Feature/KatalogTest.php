<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji halaman Katalog Produk (Tahap 10): Publis saja, pencarian, filter kategori, pagination.
 */
class KatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_katalog_hanya_menampilkan_produk_publis(): void
    {
        Produk::factory()->create(['nama_produk' => 'Semen Portland', 'harga' => 68000]);
        Produk::factory()->blok()->create(['nama_produk' => 'Gergaji Rahasia']);

        $this->get(route('produk.all'))
            ->assertOk()
            ->assertSee('Semen Portland')
            ->assertSee('Rp. 68.000')
            ->assertSee(route('produk.detail', Produk::first()->id), false)
            ->assertDontSee('Gergaji Rahasia');
    }

    public function test_pencarian_di_nama_produk_dan_merek(): void
    {
        Produk::factory()->create(['nama_produk' => 'Semen Portland', 'merek' => 'Merek A']);
        Produk::factory()->create(['nama_produk' => 'Cat Tembok', 'merek' => 'Semenindo']);
        Produk::factory()->create(['nama_produk' => 'Pipa PVC', 'merek' => 'Merek B']);

        $this->get(route('produk.all', ['q' => 'semen']))
            ->assertSee('Semen Portland') // cocok di nama
            ->assertSee('Cat Tembok')     // cocok di merek
            ->assertDontSee('Pipa PVC');
    }

    public function test_pencarian_tidak_menampilkan_produk_blok(): void
    {
        // Membuktikan orWhere sudah dibungkus kurung: merek cocok, tapi status Blok → tetap tersembunyi
        Produk::factory()->blok()->create(['nama_produk' => 'Barang Lain', 'merek' => 'Semen Blok']);

        $this->get(route('produk.all', ['q' => 'semen']))
            ->assertDontSee('Barang Lain')
            ->assertSee('Produk tidak ditemukan');
    }

    public function test_filter_kategori(): void
    {
        $cat = Kategori::factory()->create(['nama_kategori' => 'Cat & Pelapis']);
        $besi = Kategori::factory()->create(['nama_kategori' => 'Besi & Baja']);
        Produk::factory()->for($cat)->create(['nama_produk' => 'Cat Tembok']);
        Produk::factory()->for($besi)->create(['nama_produk' => 'Besi Beton']);

        $this->get(route('produk.kategori', $cat->id))
            ->assertOk()
            ->assertSee('Cat Tembok')
            ->assertDontSee('Besi Beton');
    }

    public function test_pencarian_dan_filter_kategori_bisa_digabung(): void
    {
        $semen = Kategori::factory()->create(['nama_kategori' => 'Semen & Perekat']);
        $lain = Kategori::factory()->create(['nama_kategori' => 'Lain-lain']);
        Produk::factory()->for($semen)->create(['nama_produk' => 'Semen Putih']);
        Produk::factory()->for($semen)->create(['nama_produk' => 'Lem Keramik']);
        Produk::factory()->for($lain)->create(['nama_produk' => 'Semen Instan Kategori Lain']);

        $this->get(route('produk.kategori', ['id' => $semen->id, 'q' => 'semen']))
            ->assertSee('Semen Putih')
            ->assertDontSee('Lem Keramik')
            ->assertDontSee('Semen Instan Kategori Lain');
    }

    public function test_tautan_kategori_di_sidebar_mempertahankan_kata_kunci(): void
    {
        $kategori = Kategori::factory()->create();

        $this->get(route('produk.all', ['q' => 'semen']))
            ->assertSee(route('produk.kategori', ['id' => $kategori->id, 'q' => 'semen']), false);
    }

    public function test_kategori_tidak_ada_menghasilkan_404(): void
    {
        $this->get(route('produk.kategori', 999))->assertNotFound();
    }

    public function test_pagination_9_per_halaman_dan_mempertahankan_pencarian(): void
    {
        Produk::factory()->count(11)->create(['nama_produk' => 'Semen Uji']);

        $this->get(route('produk.all', ['q' => 'semen']))
            ->assertViewHas('produk', fn ($produk) => $produk->count() === 9 && $produk->total() === 11)
            ->assertSee('Menampilkan 1–9 dari 11 produk')
            // Tautan halaman 2 tetap membawa ?q=semen (hasil ->appends())
            ->assertSee('q=semen&amp;page=2', false);

        $this->get(route('produk.all', ['q' => 'semen', 'page' => 2]))
            ->assertViewHas('produk', fn ($produk) => $produk->count() === 2);
    }

    public function test_pesan_ramah_jika_hasil_kosong(): void
    {
        $this->get(route('produk.all', ['q' => 'tidakadabarangini']))
            ->assertOk()
            ->assertSee('Produk tidak ditemukan')
            ->assertSee('tidakadabarangini');
    }
}
