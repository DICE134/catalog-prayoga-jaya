<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Pemeriksaan otomatis aksesibilitas, bahasa, dan ruang lingkup (Tahap 14)
 * pada SEMUA halaman publik, memakai data contoh dari DatabaseSeeder.
 */
class AksesibilitasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Isi data contoh dari DatabaseSeeder (8 kategori, 12 produk, 1 admin).
        // Dipanggil DI SINI (bukan "protected $seed = true") supaya berjalan di dalam transaksi
        // RefreshDatabase dan dibatalkan lagi setelah test → tidak mengotori test lain.
        $this->seed();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function halamanPublik(): array
    {
        return [
            'beranda' => ['/beranda'],
            'katalog' => ['/produk/all'],
            'katalog halaman 2' => ['/produk/all?page=2'],
            'katalog per kategori' => ['/produk/kategori/1'],
            'pencarian kosong' => ['/produk/all?q=tidakada'],
            'detail produk' => ['/produk/detail/1'],
            'tentang' => ['/tentang'],
            'kontak' => ['/kontak'],
        ];
    }

    /** Mengubah HTML menjadi objek DOM supaya bisa diperiksa per tag. */
    private function dom(string $url): DOMXPath
    {
        $html = $this->get($url)->assertOk()->getContent();

        $dom = new DOMDocument;
        // @ = abaikan peringatan untuk tag HTML5 (header, nav, section) yang tidak dikenal DOMDocument
        @$dom->loadHTML('<?xml encoding="utf-8">'.$html);

        return new DOMXPath($dom);
    }

    #[DataProvider('halamanPublik')]
    public function test_struktur_dasar_halaman(string $url): void
    {
        $xpath = $this->dom($url);

        $this->assertSame('id', $xpath->query('//html')->item(0)->getAttribute('lang'), 'Bahasa halaman harus id');
        $this->assertSame(1, $xpath->query('//meta[@name="viewport"]')->length, 'Harus ada meta viewport untuk HP');
        $this->assertSame(1, $xpath->query('//h1')->length, 'Setiap halaman tepat satu <h1>');
        $this->assertSame(1, $xpath->query('//main')->length, 'Harus ada satu <main>');
    }

    #[DataProvider('halamanPublik')]
    public function test_semua_gambar_punya_atribut_alt(string $url): void
    {
        foreach ($this->dom($url)->query('//img') as $img) {
            $this->assertTrue($img->hasAttribute('alt'), 'Gambar tanpa alt: '.$img->getAttribute('src'));

            // alt="" hanya boleh untuk gambar hiasan di dalam tombol yang sudah punya aria-label
            if ($img->getAttribute('alt') === '') {
                $this->assertNotSame('', $img->parentNode->getAttribute('aria-label'), 'Gambar dekoratif tanpa label tombol');
            }
        }
    }

    #[DataProvider('halamanPublik')]
    public function test_tautan_tab_baru_aman_dan_iframe_berjudul(string $url): void
    {
        $xpath = $this->dom($url);

        // target="_blank" wajib rel="noopener" (mencegah halaman baru mengendalikan tab asal)
        foreach ($xpath->query('//a[@target="_blank"]') as $a) {
            $this->assertStringContainsString('noopener', $a->getAttribute('rel'), 'Tautan tanpa noopener: '.$a->getAttribute('href'));
        }

        foreach ($xpath->query('//iframe') as $iframe) {
            $this->assertNotSame('', $iframe->getAttribute('title'), 'Iframe wajib punya title');
        }
    }

    #[DataProvider('halamanPublik')]
    public function test_tidak_ada_fitur_ecommerce(string $url): void
    {
        // Website ini KATALOG, bukan toko online: tidak ada keranjang, checkout, pembayaran, dsb.
        $teks = strtolower(strip_tags($this->get($url)->getContent()));

        foreach (['keranjang', 'checkout', 'wishlist', 'bayar sekarang', 'ongkir', 'stok tersedia'] as $kata) {
            $this->assertStringNotContainsString($kata, $teks, "Kata e-commerce ditemukan: {$kata}");
        }
    }

    public function test_harga_selalu_berformat_rupiah(): void
    {
        foreach (['/beranda', '/produk/all', '/produk/all?page=2', '/produk/detail/1'] as $url) {
            $html = $this->get($url)->getContent();

            // Harga tidak boleh tampil sebagai angka mentah (misalnya "Rp. 68000")
            $this->assertDoesNotMatchRegularExpression('/Rp\.\s*\d{4,}/', $html, "Ada harga tanpa pemisah ribuan di {$url}");
        }

        // Produk id 1 di seeder = Semen Portland, harga 68000 → "Rp. 68.000"
        $this->get('/produk/detail/1')->assertSee('Rp. 68.000');
    }

    public function test_harga_rupiah_juga_di_katalog(): void
    {
        $html = $this->get('/produk/all')->getContent();
        $this->assertMatchesRegularExpression('/Rp\. \d{1,3}(\.\d{3})+/', $html);
        $this->assertDoesNotMatchRegularExpression('/Rp\.\s*\d{4,}/', $html, 'Ada harga tanpa pemisah ribuan');
    }

    public function test_pagination_berbahasa_indonesia(): void
    {
        $this->get('/produk/all')
            // Angka di pagination dibungkus <span>, jadi dicek urutan katanya saja
            ->assertSeeInOrder(['Menampilkan', 'sampai', 'dari', 'hasil'])
            ->assertDontSee('Showing')
            ->assertDontSee('results');
    }

    public function test_halaman_404_berbahasa_indonesia(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertDontSee('Not Found');
    }
}
