<?php

namespace App\Models;

use Database\Factories\ProdukFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model produk material bangunan.
 */
class Produk extends Model
{
    /** @use HasFactory<ProdukFactory> */
    use HasFactory;

    /**
     * Folder penyimpanan foto di disk 'public' (storage/app/public/img-produk).
     */
    public const FOLDER_FOTO = 'img-produk';

    /**
     * @var string
     */
    protected $table = 'produk';

    /**
     * Semua kolom boleh diisi massal kecuali 'id'.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * Konversi tipe data otomatis saat dibaca dari database.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'status' => 'integer',   // 1 = Publis, 0 = Blok
            'unggulan' => 'boolean', // true / false
        ];
    }

    /**
     * Relasi: produk DIMILIKI OLEH satu kategori (kebalikan dari hasMany).
     * Pemakaian: $produk->kategori->nama_kategori
     *
     * @return BelongsTo<Kategori, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi: satu produk MEMILIKI BANYAK foto tambahan.
     * Pemakaian: $produk->fotoProduk → daftar foto tambahan.
     *
     * @return HasMany<FotoProduk, $this>
     */
    public function fotoProduk(): HasMany
    {
        return $this->hasMany(FotoProduk::class, 'produk_id');
    }

    /**
     * Accessor: alamat lengkap foto utama untuk dipakai di <img src="...">.
     * Pemakaian di view: {{ $produk->url_foto }}
     * Jika produk belum punya foto, dipakai gambar placeholder.
     *
     * @return Attribute<string, never>
     */
    protected function urlFoto(): Attribute
    {
        return Attribute::get(fn (): string => $this->foto
            ? asset('storage/'.self::FOLDER_FOTO.'/'.$this->foto)
            : asset('images/produk-placeholder.svg'));
    }
}
