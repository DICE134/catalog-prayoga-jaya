<?php

namespace Tests\Feature;

use App\Models\FotoProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Menguji kelola produk dan foto di halaman admin (Tahap 7).
 */
class ProdukCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Penyimpanan palsu: file hasil unggah di test TIDAK masuk ke folder storage asli.
        Storage::fake('public');

        $this->actingAs(User::factory()->create());
    }

    /**
     * Data form produk yang valid.
     *
     * @return array<string, mixed>
     */
    private function dataValid(Kategori $kategori, array $timpa = []): array
    {
        return array_merge([
            'kategori_id' => $kategori->id,
            'nama_produk' => 'Semen Portland',
            'merek' => 'Merek Uji',
            'spesifikasi' => '50 kg',
            'satuan' => 'sak',
            'harga' => 68000,
            'detail' => 'Deskripsi uji.',
            'status' => 1,
            'unggulan' => 1,
        ], $timpa);
    }

    public function test_daftar_produk_menampilkan_nama_kategori_dan_harga_rupiah(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Besi & Baja']);
        Produk::factory()->for($kategori)->create(['nama_produk' => 'Besi Beton', 'harga' => 85000]);

        $this->get(route('backend.produk.index'))
            ->assertOk()
            ->assertSee('Besi Beton')
            ->assertSee('Besi &amp; Baja', false)
            ->assertSee('Rp. 85.000');
    }

    public function test_form_tambah_berisi_dropdown_kategori(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Pipa & Sanitasi']);

        $this->get(route('backend.produk.create'))
            ->assertOk()
            ->assertSee('Pipa &amp; Sanitasi', false)
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_produk_baru_tersimpan_dengan_foto(): void
    {
        $kategori = Kategori::factory()->create();

        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'foto' => UploadedFile::fake()->image('semen.jpg', 600, 600),
        ]))->assertRedirect(route('backend.produk.index'));

        $produk = Produk::first();
        $this->assertSame('Semen Portland', $produk->nama_produk);
        $this->assertTrue($produk->unggulan);
        $this->assertNotNull($produk->foto);
        Storage::disk('public')->assertExists('img-produk/'.$produk->foto);
    }

    public function test_produk_tanpa_foto_memakai_placeholder_dan_unggulan_tidak_dicentang(): void
    {
        $kategori = Kategori::factory()->create();
        $data = $this->dataValid($kategori);
        unset($data['unggulan']); // checkbox tidak dicentang = tidak terkirim

        $this->post(route('backend.produk.store'), $data)->assertSessionHasNoErrors();

        $produk = Produk::first();
        $this->assertNull($produk->foto);
        $this->assertFalse($produk->unggulan);
        $this->assertStringEndsWith('images/produk-placeholder.svg', $produk->url_foto);
    }

    public function test_validasi_kolom_wajib_berbahasa_indonesia(): void
    {
        $this->post(route('backend.produk.store'), [])
            ->assertSessionHasErrors([
                'kategori_id' => 'Kategori wajib diisi.',
                'nama_produk' => 'Nama produk wajib diisi.',
                'satuan' => 'Satuan wajib diisi.',
                'harga' => 'Harga wajib diisi.',
                'status' => 'Status wajib diisi.',
            ]);

        $this->assertDatabaseCount('produk', 0);
    }

    public function test_harga_harus_angka_bulat_dan_kategori_harus_ada(): void
    {
        $kategori = Kategori::factory()->create();

        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'harga' => '68.000,50',
            'kategori_id' => 999,
        ]))->assertSessionHasErrors(['harga', 'kategori_id' => 'Kategori yang dipilih tidak valid.']);
    }

    public function test_foto_harus_gambar_dan_maksimal_2mb(): void
    {
        $kategori = Kategori::factory()->create();

        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'foto' => UploadedFile::fake()->create('virus.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors(['foto' => 'Foto harus berupa gambar.']);

        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'foto' => UploadedFile::fake()->image('besar.jpg')->size(3000), // 3000 KB
        ]))->assertSessionHasErrors(['foto' => 'Ukuran foto maksimal 2048 kilobyte.']);
    }

    public function test_ubah_produk_dan_ganti_foto_menghapus_foto_lama(): void
    {
        $kategori = Kategori::factory()->create();
        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'foto' => UploadedFile::fake()->image('lama.jpg'),
        ]));
        $produk = Produk::first();
        $fotoLama = $produk->foto;

        $this->get(route('backend.produk.edit', $produk))->assertOk()->assertSee('Semen Portland');

        $this->put(route('backend.produk.update', $produk), $this->dataValid($kategori, [
            'nama_produk' => 'Semen Putih',
            'foto' => UploadedFile::fake()->image('baru.jpg'),
        ]))->assertRedirect(route('backend.produk.index'));

        $produk->refresh();
        $this->assertSame('Semen Putih', $produk->nama_produk);
        $this->assertNotSame($fotoLama, $produk->foto);
        Storage::disk('public')->assertMissing('img-produk/'.$fotoLama);
        Storage::disk('public')->assertExists('img-produk/'.$produk->foto);
    }

    public function test_ubah_tanpa_foto_baru_mempertahankan_foto_lama(): void
    {
        $kategori = Kategori::factory()->create();
        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'foto' => UploadedFile::fake()->image('lama.jpg'),
        ]));
        $produk = Produk::first();
        $fotoLama = $produk->foto;

        $this->put(route('backend.produk.update', $produk), $this->dataValid($kategori, ['harga' => 70000]));

        $this->assertSame($fotoLama, $produk->refresh()->foto);
        $this->assertSame(70000, $produk->harga);
    }

    public function test_unggah_dan_hapus_foto_tambahan(): void
    {
        $produk = Produk::factory()->create();

        $this->post(route('backend.produk.foto.store', $produk), [
            'foto_tambahan' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.png'),
            ],
        ])->assertRedirect(route('backend.produk.edit', $produk));

        $this->assertCount(2, $produk->fotoProduk);
        $foto = $produk->fotoProduk->first();
        Storage::disk('public')->assertExists('img-produk/'.$foto->foto);

        $this->delete(route('backend.produk.foto.destroy', $foto))
            ->assertRedirect(route('backend.produk.edit', $produk));

        $this->assertDatabaseMissing('foto_produk', ['id' => $foto->id]);
        Storage::disk('public')->assertMissing('img-produk/'.$foto->foto);
    }

    public function test_unggah_foto_tambahan_wajib_memilih_file(): void
    {
        $produk = Produk::factory()->create();

        $this->post(route('backend.produk.foto.store', $produk), [])
            ->assertSessionHasErrors(['foto_tambahan' => 'Foto tambahan wajib diisi.']);
    }

    public function test_hapus_produk_menghapus_semua_file_dan_foto_tambahan(): void
    {
        $kategori = Kategori::factory()->create();
        $this->post(route('backend.produk.store'), $this->dataValid($kategori, [
            'foto' => UploadedFile::fake()->image('utama.jpg'),
        ]));
        $produk = Produk::first();
        $this->post(route('backend.produk.foto.store', $produk), [
            'foto_tambahan' => [UploadedFile::fake()->image('tambahan.jpg')],
        ]);
        $fotoUtama = $produk->foto;
        $fotoTambahan = FotoProduk::first()->foto;

        $this->delete(route('backend.produk.destroy', $produk))
            ->assertRedirect(route('backend.produk.index'));

        $this->assertDatabaseCount('produk', 0);
        $this->assertDatabaseCount('foto_produk', 0);
        Storage::disk('public')->assertMissing('img-produk/'.$fotoUtama);
        Storage::disk('public')->assertMissing('img-produk/'.$fotoTambahan);
    }

    public function test_tamu_tidak_bisa_membuka_admin_produk(): void
    {
        auth()->logout();

        $this->get(route('backend.produk.index'))->assertRedirect(route('backend.login'));
        $this->post(route('backend.produk.store'), [])->assertRedirect(route('backend.login'));
    }
}
