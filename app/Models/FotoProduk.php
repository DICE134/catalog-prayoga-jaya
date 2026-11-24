<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model foto tambahan produk (selain foto utama di tabel produk).
 */
class FotoProduk extends Model
{
    /**
     * @var string
     */
    protected $table = 'foto_produk';

    /**
     * Tabel foto_produk tidak punya kolom created_at/updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * Relasi: foto ini milik satu produk.
     *
     * @return BelongsTo<Produk, $this>
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Accessor: alamat lengkap foto tambahan. Pemakaian: {{ $foto->url_foto }}
     *
     * @return Attribute<string, never>
     */
    protected function urlFoto(): Attribute
    {
        return Attribute::get(fn (): string => asset('storage/'.Produk::FOLDER_FOTO.'/'.$this->foto));
    }
}
