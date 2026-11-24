<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Mengisi data awal: akun admin, kategori, dan produk CONTOH.
 * Jalankan: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Isi database dengan data awal.
     */
    public function run(): void
    {
        $this->buatAdmin();
        $idKategori = $this->buatKategori();
        $this->buatProduk($idKategori);
    }

    /**
     * Akun admin SEMENTARA. Ganti email dan password setelah website dipakai.
     */
    private function buatAdmin(): void
    {
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@prayogajaya.test',     // SEMENTARA — ganti
            'password' => Hash::make('admin12345'),  // SEMENTARA — ganti. Hash::make = bcrypt
            'role' => 1,                             // super admin
            'status' => 1,                           // aktif
        ]);
    }

    /**
     * Kategori contoh. Mengembalikan pasangan [nama_kategori => id]
     * supaya produk bisa dihubungkan ke kategori lewat namanya.
     *
     * @return array<string, int>
     */
    private function buatKategori(): array
    {
        $daftarKategori = [
            'Semen & Perekat',
            'Cat & Pelapis',
            'Besi & Baja',
            'Pipa & Sanitasi',
            'Keramik & Lantai',
            'Kayu & Triplek',
            'Perkakas',
            'Listrik',
        ];

        $idKategori = [];
        foreach ($daftarKategori as $nama) {
            $kategori = Kategori::create(['nama_kategori' => $nama]);
            $idKategori[$nama] = $kategori->id;
        }

        return $idKategori;
    }

    /**
     * Produk CONTOH: 11 Publis + 1 Blok, 6 di antaranya unggulan.
     * Merek dan harga di bawah ini HANYA CONTOH — ganti dengan data asli lewat halaman admin.
     *
     * @param  array<string, int>  $idKategori
     */
    private function buatProduk(array $idKategori): void
    {
        $daftarProduk = [
            [
                'kategori' => 'Semen & Perekat',
                'nama_produk' => 'Semen Portland',
                'merek' => 'Tiga Roda',          // CONTOH — ganti dengan data asli
                'spesifikasi' => '50 kg',
                'satuan' => 'sak',
                'harga' => 68000,                // CONTOH — ganti dengan data asli
                'unggulan' => true,
            ],
            [
                'kategori' => 'Semen & Perekat',
                'nama_produk' => 'Semen Instan Perekat Bata Ringan',
                'merek' => 'MU',                 // CONTOH — ganti dengan data asli
                'spesifikasi' => '40 kg',
                'satuan' => 'sak',
                'harga' => 95000,                // CONTOH — ganti dengan data asli
                'unggulan' => false,
            ],
            [
                'kategori' => 'Cat & Pelapis',
                'nama_produk' => 'Cat Tembok Interior Putih',
                'merek' => 'Avitex',             // CONTOH — ganti dengan data asli
                'spesifikasi' => '5 kg',
                'satuan' => 'galon',
                'harga' => 120000,               // CONTOH — ganti dengan data asli
                'unggulan' => true,
            ],
            [
                'kategori' => 'Cat & Pelapis',
                'nama_produk' => 'Cat Kayu dan Besi',
                'merek' => 'Avian',              // CONTOH — ganti dengan data asli
                'spesifikasi' => '1 kg',
                'satuan' => 'kaleng',
                'harga' => 75000,                // CONTOH — ganti dengan data asli
                'unggulan' => false,
            ],
            [
                'kategori' => 'Besi & Baja',
                'nama_produk' => 'Besi Beton Polos',
                'merek' => 'Master Steel',       // CONTOH — ganti dengan data asli
                'spesifikasi' => 'Diameter 10 mm, panjang 12 m',
                'satuan' => 'batang',
                'harga' => 85000,                // CONTOH — ganti dengan data asli
                'unggulan' => true,
            ],
            [
                'kategori' => 'Pipa & Sanitasi',
                'nama_produk' => 'Pipa PVC AW',
                'merek' => 'Rucika',             // CONTOH — ganti dengan data asli
                'spesifikasi' => '1/2 inch, panjang 4 m',
                'satuan' => 'batang',
                'harga' => 35000,                // CONTOH — ganti dengan data asli
                'unggulan' => true,
            ],
            [
                'kategori' => 'Keramik & Lantai',
                'nama_produk' => 'Keramik Lantai Putih Polos',
                'merek' => 'Roman',              // CONTOH — ganti dengan data asli
                'spesifikasi' => '40 x 40 cm, isi 6 keping',
                'satuan' => 'dus',
                'harga' => 65000,                // CONTOH — ganti dengan data asli
                'unggulan' => true,
            ],
            [
                'kategori' => 'Kayu & Triplek',
                'nama_produk' => 'Triplek',
                'merek' => null,                 // CONTOH — ganti dengan data asli (boleh kosong)
                'spesifikasi' => 'Tebal 9 mm, 122 x 244 cm',
                'satuan' => 'lembar',
                'harga' => 110000,               // CONTOH — ganti dengan data asli
                'unggulan' => false,
            ],
            [
                'kategori' => 'Perkakas',
                'nama_produk' => 'Palu Kambing',
                'merek' => 'Krisbow',            // CONTOH — ganti dengan data asli
                'spesifikasi' => '16 oz, gagang fiber',
                'satuan' => 'buah',
                'harga' => 55000,                // CONTOH — ganti dengan data asli
                'unggulan' => false,
            ],
            [
                'kategori' => 'Listrik',
                'nama_produk' => 'Kabel Listrik NYM',
                'merek' => 'Eterna',             // CONTOH — ganti dengan data asli
                'spesifikasi' => '2 x 1,5 mm, panjang 50 m',
                'satuan' => 'roll',
                'harga' => 450000,               // CONTOH — ganti dengan data asli
                'unggulan' => true,
            ],
            [
                'kategori' => 'Listrik',
                'nama_produk' => 'Saklar Tunggal',
                'merek' => 'Broco',              // CONTOH — ganti dengan data asli
                'spesifikasi' => 'Inbow, putih',
                'satuan' => 'buah',
                'harga' => 18000,                // CONTOH — ganti dengan data asli
                'unggulan' => false,
            ],
            [
                // Produk ini sengaja berstatus Blok (0) untuk menguji bahwa
                // produk Blok TIDAK tampil di katalog dan detailnya 404.
                'kategori' => 'Perkakas',
                'nama_produk' => 'Gergaji Kayu',
                'merek' => 'Tekiro',             // CONTOH — ganti dengan data asli
                'spesifikasi' => '18 inch',
                'satuan' => 'buah',
                'harga' => 70000,                // CONTOH — ganti dengan data asli
                'unggulan' => false,
                'status' => 0,
            ],
        ];

        foreach ($daftarProduk as $produk) {
            Produk::create([
                'kategori_id' => $idKategori[$produk['kategori']],
                'nama_produk' => $produk['nama_produk'],
                'merek' => $produk['merek'],
                'spesifikasi' => $produk['spesifikasi'],
                'satuan' => $produk['satuan'],
                'harga' => $produk['harga'],
                'detail' => 'Deskripsi contoh untuk '.$produk['nama_produk'].'. Ganti dengan deskripsi asli lewat halaman admin.',
                'foto' => null, // foto diunggah lewat admin di Tahap 7; sementara tampil gambar placeholder
                'status' => $produk['status'] ?? 1,
                'unggulan' => $produk['unggulan'],
            ]);
        }
    }
}
