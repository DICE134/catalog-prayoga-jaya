<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Pembuat data kategori palsu untuk TEST (bukan data asli toko).
 *
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kategori' => 'Kategori '.fake()->unique()->word(),
        ];
    }
}
