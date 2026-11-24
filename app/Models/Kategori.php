<?php

namespace App\Models;

use Database\Factories\KategoriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model kategori produk (Semen & Perekat, Cat & Pelapis, dst.).
 */
class Kategori extends Model
{
    /** @use HasFactory<KategoriFactory> */
    use HasFactory;

    /**
     * Nama tabel ditulis manual, karena tanpa ini Laravel menebak
     * nama tabel bahasa Inggris "kategoris".
     *
     * @var string
     */
    protected $table = 'kategori';

    /**
     * Tabel kategori tidak punya kolom created_at/updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Semua kolom boleh diisi massal kecuali 'id'.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * Relasi: satu kategori MEMILIKI BANYAK produk (one to many).
     * Pemakaian: $kategori->produk → daftar produk di kategori itu.
     *
     * @return HasMany<Produk, $this>
     */
    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
