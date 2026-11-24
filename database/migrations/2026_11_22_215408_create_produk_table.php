<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel produk.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel kategori: kategori_id harus berisi id yang ADA di tabel kategori.
            // Kategori yang masih punya produk tidak bisa dihapus (database menolaknya).
            $table->foreignId('kategori_id')->constrained('kategori');

            $table->string('nama_produk', 150);
            $table->string('merek', 100)->nullable();       // nullable = boleh kosong (ada material tanpa merek)
            $table->string('spesifikasi', 150)->nullable(); // ukuran atau tipe, misal "50 kg", "1/2 inch"
            $table->string('satuan', 30);                    // sak, batang, kaleng, lembar, dll.
            $table->unsignedInteger('harga');                // rupiah, bilangan bulat tanpa koma
            $table->text('detail')->nullable();              // deskripsi panjang
            $table->string('foto')->nullable();              // nama file foto utama (diisi di Tahap 7)
            $table->tinyInteger('status')->default(1);       // 1 = Publis (tampil), 0 = Blok (disembunyikan)
            $table->boolean('unggulan')->default(false);     // true = tampil di "Produk Unggulan" beranda
            $table->timestamps();                            // created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel produk.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
