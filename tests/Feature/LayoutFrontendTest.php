<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Menguji layout frontend (Tahap 8): navbar, footer dari config('toko'), dan hero khusus beranda.
 */
class LayoutFrontendTest extends TestCase
{
    // Beranda sekarang mengambil data dari database (Tahap 9), jadi test butuh tabel.
    use RefreshDatabase;

    /**
     * Nama route halaman publik yang memakai layout frontend.
     *
     * @return array<string, array{0: string, 1: array<int, int>}>
     */
    public static function halamanPublik(): array
    {
        return [
            'beranda' => ['beranda', []],
            'katalog' => ['produk.all', []],
            'katalog per kategori' => ['produk.kategori', [1]],
            'detail' => ['produk.detail', [1]],
            'tentang' => ['tentang', []],
            'kontak' => ['kontak', []],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Kategori id 1 dan produk Publis id 1, supaya /produk/kategori/1 dan /produk/detail/1 tidak 404.
        Produk::factory()->create(['status' => 1]);
    }

    // #[DataProvider]: test ini dijalankan sekali untuk SETIAP halaman di halamanPublik()
    #[DataProvider('halamanPublik')]
    public function test_semua_halaman_publik_memakai_navbar_dan_footer(string $route, array $parameter): void
    {
        $this->get(route($route, $parameter))
            ->assertOk()
            ->assertSee('id="menuUtama"', false)
            ->assertSeeInOrder(['Beranda', 'Produk', 'Tentang Kami', 'Kontak'])
            ->assertSee('Hubungi Kami')
            ->assertSee('https://wa.me/'.config('toko.wa'), false);
    }

    public function test_footer_membaca_data_dari_config_toko(): void
    {
        // Ubah config sementara → footer ikut berubah, membuktikan data tidak ditulis langsung di view.
        config([
            'toko.alamat' => 'Jl. Uji Coba No. 1',
            'toko.jam_buka' => 'Senin–Sabtu 08.00–16.00',
            'toko.wa' => '6280000000000',
        ]);

        $this->get(route('tentang'))
            ->assertSee('Jl. Uji Coba No. 1')
            ->assertSee('Senin–Sabtu 08.00–16.00')
            ->assertSee('https://wa.me/6280000000000', false);
    }

    public function test_hero_hanya_tampil_di_beranda(): void
    {
        $this->get(route('beranda'))->assertSee('class="hero', false);

        $this->get(route('produk.all'))->assertDontSee('class="hero', false);
        $this->get(route('tentang'))->assertDontSee('class="hero', false);
        $this->get(route('kontak'))->assertDontSee('class="hero', false);
    }

    public function test_menu_aktif_sesuai_halaman(): void
    {
        // Pola: aria-current="page" lalu (boleh dipisah spasi/baris baru) href menu yang benar
        $polaAktif = fn (string $url) => '/aria-current="page"\s+href="'.preg_quote($url, '/').'"/';

        $html = $this->get(route('kontak'))->getContent();
        $this->assertMatchesRegularExpression($polaAktif(route('kontak')), $html);
        // Hanya SATU menu navbar yang aktif (breadcrumb juga memakai aria-current, jadi yang dihitung class-nya)
        $this->assertSame(1, preg_match_all('/class="nav-link\s+active/', $html));

        // Detail produk termasuk menu "Produk"
        $html = $this->get(route('produk.detail', 1))->getContent();
        $this->assertMatchesRegularExpression($polaAktif(route('produk.all')), $html);
    }

    public function test_tidak_ada_fitur_ecommerce_di_layout(): void
    {
        $this->get(route('beranda'))
            ->assertDontSee('Keranjang')
            ->assertDontSee('Wishlist')
            ->assertDontSee('Checkout');
    }
}
