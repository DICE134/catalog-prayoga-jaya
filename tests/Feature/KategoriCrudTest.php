<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji CRUD kategori (Tahap 4).
 * RefreshDatabase: database test (SQLite di memori, lihat phpunit.xml) dikosongkan
 * sebelum setiap test. Database MySQL asli TIDAK tersentuh.
 */
class KategoriCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Masuk sebagai admin, supaya test tetap lulus setelah login dipasang di Tahap 5.
        $this->actingAs(User::factory()->create());
    }

    public function test_daftar_kategori_tampil(): void
    {
        Kategori::create(['nama_kategori' => 'Semen & Perekat']);

        $this->get(route('backend.kategori.index'))
            ->assertOk()
            ->assertSee('Semen &amp; Perekat', false);
    }

    public function test_daftar_kategori_memakai_layout_admin_dan_datatables(): void
    {
        $this->get(route('backend.kategori.index'))
            ->assertOk()
            ->assertSee('id="sidebarAdmin"', false)  // sidebar dari layout
            ->assertSee('Lihat Website')
            ->assertSee('datatable', false);        // tabel diaktifkan DataTables
    }

    public function test_form_tambah_terbuka(): void
    {
        $this->get(route('backend.kategori.create'))->assertOk();
    }

    public function test_kategori_baru_tersimpan(): void
    {
        $this->post(route('backend.kategori.store'), ['nama_kategori' => 'Atap & Genteng'])
            ->assertRedirect(route('backend.kategori.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Atap & Genteng']);
    }

    public function test_nama_kategori_wajib_diisi(): void
    {
        $this->post(route('backend.kategori.store'), ['nama_kategori' => ''])
            ->assertSessionHasErrors(['nama_kategori' => 'Nama kategori wajib diisi.']);

        $this->assertDatabaseCount('kategori', 0);
    }

    public function test_nama_kategori_tidak_boleh_kembar(): void
    {
        Kategori::create(['nama_kategori' => 'Listrik']);

        $this->post(route('backend.kategori.store'), ['nama_kategori' => 'Listrik'])
            ->assertSessionHasErrors(['nama_kategori' => 'Nama kategori sudah ada, gunakan nama lain.']);
    }

    public function test_kategori_bisa_diubah(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Listrik']);

        $this->get(route('backend.kategori.edit', $kategori))->assertOk()->assertSee('Listrik');

        $this->put(route('backend.kategori.update', $kategori), ['nama_kategori' => 'Listrik & Lampu'])
            ->assertRedirect(route('backend.kategori.index'));

        $this->assertDatabaseHas('kategori', ['id' => $kategori->id, 'nama_kategori' => 'Listrik & Lampu']);
    }

    public function test_simpan_ubah_tanpa_mengganti_nama_tidak_dianggap_kembar(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Listrik']);

        $this->put(route('backend.kategori.update', $kategori), ['nama_kategori' => 'Listrik'])
            ->assertSessionHasNoErrors();
    }

    public function test_ubah_kategori_yang_tidak_ada_menghasilkan_404(): void
    {
        $this->get(route('backend.kategori.edit', 999))->assertNotFound();
    }

    public function test_kategori_kosong_bisa_dihapus(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Perkakas']);

        $this->delete(route('backend.kategori.destroy', $kategori))
            ->assertRedirect(route('backend.kategori.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
    }

    public function test_kategori_yang_dipakai_produk_tidak_bisa_dihapus(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Semen & Perekat']);
        Produk::create([
            'kategori_id' => $kategori->id,
            'nama_produk' => 'Semen Portland',
            'satuan' => 'sak',
            'harga' => 68000,
        ]);

        $this->delete(route('backend.kategori.destroy', $kategori))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('kategori', ['id' => $kategori->id]);
    }
}
