<?php

/*
|--------------------------------------------------------------------------
| DATA TOKO
|--------------------------------------------------------------------------
| Semua data toko disimpan di SATU tempat ini. Nilainya dibaca dari file .env
| lewat env('KUNCI', 'nilai cadangan'). View memanggilnya dengan
| config('toko.nama'), config('toko.wa'), dan seterusnya.
|
| PLACEHOLDER: nilai di bawah BELUM data asli. Ganti di file .env, lalu jalankan
| "php artisan config:clear" supaya perubahan terbaca.
*/

return [

    'nama' => env('TOKO_NAMA', 'Toko Material Prayoga Jaya'),

    // PLACEHOLDER — slogan sementara, ganti dengan slogan asli toko
    'slogan' => env('TOKO_SLOGAN', '(Slogan toko belum diisi)'),

    // PLACEHOLDER — nomor WhatsApp format internasional TANPA tanda + (62 = Indonesia)
    'wa' => env('TOKO_WA', '6281234567890'),

    // PLACEHOLDER — alamat lengkap toko
    'alamat' => env('TOKO_ALAMAT', '(Alamat belum diisi)'),

    // PLACEHOLDER — contoh isi: "Senin–Sabtu 07.00–17.00"
    'jam_buka' => env('TOKO_JAM_BUKA', '(Jam operasional belum diisi)'),

    // PLACEHOLDER — tautan Google Maps (tombol "Buka di Google Maps")
    'maps_url' => env('TOKO_MAPS_URL', ''),

    // Opsional — alamat src dari kode "Sematkan peta" Google Maps. Boleh kosong.
    'maps_embed' => env('TOKO_MAPS_EMBED', ''),

];
