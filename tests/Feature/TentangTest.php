<?php

namespace Tests\Feature;

use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji halaman Tentang Kami (Tahap 12).
 */
class TentangTest extends TestCase
{
    use RefreshDatabase;

    public function test_tentang_memuat_semua_bagian(): void
    {
        $this->get(route('tentang'))
            ->assertOk()
            ->assertSeeInOrder([
                'Sejarah Singkat',
                'Profil Toko',
                'Jenis Material yang Kami Jual',
                'Informasi Pelayanan',
            ])
            ->assertSee('images/foto-toko.svg', false);
    }

    public function test_jenis_material_diambil_dari_tabel_kategori(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Kategori Baru Dari Admin']);

        $this->get(route('tentang'))
            ->assertSee('Kategori Baru Dari Admin')
            ->assertSee(route('produk.kategori', $kategori->id), false);
    }

    public function test_data_toko_diambil_dari_config(): void
    {
        config(['toko.alamat' => 'Jl. Uji No. 7', 'toko.jam_buka' => 'Setiap hari 07.00–17.00']);

        $this->get(route('tentang'))
            ->assertSee('Jl. Uji No. 7')
            ->assertSee('Setiap hari 07.00–17.00');
    }
}
