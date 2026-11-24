<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel foto_produk (foto tambahan untuk setiap produk).
     */
    public function up(): void
    {
        Schema::create('foto_produk', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel produk. cascadeOnDelete: jika produk dihapus,
            // semua baris foto tambahannya ikut terhapus otomatis.
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();

            $table->string('foto'); // nama file foto
        });
    }

    /**
     * Menghapus tabel foto_produk.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_produk');
    }
};
