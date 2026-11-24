<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Pembuat data produk palsu untuk TEST (bukan data asli toko).
 *
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kategori_id' => Kategori::factory(), // otomatis membuat kategori baru jika tidak ditentukan
            'nama_produk' => 'Produk '.fake()->unique()->words(2, true),
            'merek' => fake()->company(),
            'spesifikasi' => fake()->numberBetween(1, 50).' kg',
            'satuan' => fake()->randomElement(['sak', 'batang', 'kaleng', 'lembar', 'buah']),
            'harga' => fake()->numberBetween(10, 500) * 1000,
            'detail' => fake()->sentence(),
            'foto' => null,
            'status' => 1,
            'unggulan' => false,
        ];
    }

    /**
     * Produk berstatus Blok (tidak tampil di website).
     */
    public function blok(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 0]);
    }

    /**
     * Produk unggulan.
     */
    public function unggulan(): static
    {
        return $this->state(fn (array $attributes) => ['unggulan' => true]);
    }
}
