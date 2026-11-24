<?php

/*
|--------------------------------------------------------------------------
| Pesan validasi Bahasa Indonesia
|--------------------------------------------------------------------------
| Dipakai otomatis karena APP_LOCALE=id. Aturan yang tidak ada di file ini
| memakai pesan bahasa Inggris dari lang/en/validation.php (APP_FALLBACK_LOCALE=en).
|
| :attribute = nama kolom (huruf kecil), :Attribute = nama kolom dengan huruf depan kapital.
| Nama kolom bisa "dibahasakan" lewat array 'attributes' di bagian bawah.
*/

return [

    'accepted' => ':Attribute harus disetujui.',
    'active_url' => ':Attribute bukan URL yang valid.',
    'after' => ':Attribute harus tanggal setelah :date.',
    'alpha' => ':Attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':Attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => ':Attribute hanya boleh berisi huruf dan angka.',
    'array' => ':Attribute harus berupa daftar (array).',
    'before' => ':Attribute harus tanggal sebelum :date.',
    'between' => [
        'array' => ':Attribute harus berisi antara :min sampai :max item.',
        'file' => ':Attribute harus berukuran antara :min sampai :max kilobyte.',
        'numeric' => ':Attribute harus bernilai antara :min sampai :max.',
        'string' => ':Attribute harus berisi antara :min sampai :max karakter.',
    ],
    'boolean' => ':Attribute harus bernilai ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password salah.',
    'date' => ':Attribute bukan tanggal yang valid.',
    'decimal' => ':Attribute harus memiliki :decimal angka desimal.',
    'different' => ':Attribute dan :other harus berbeda.',
    'digits' => ':Attribute harus terdiri dari :digits angka.',
    'digits_between' => ':Attribute harus terdiri dari :min sampai :max angka.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'file' => ':Attribute harus berupa file.',
    'filled' => ':Attribute wajib diisi.',
    'gt' => [
        'numeric' => ':Attribute harus lebih besar dari :value.',
        'string' => ':Attribute harus lebih dari :value karakter.',
    ],
    'gte' => [
        'numeric' => ':Attribute harus lebih besar dari atau sama dengan :value.',
        'string' => ':Attribute minimal :value karakter.',
    ],
    'image' => ':Attribute harus berupa gambar.',
    'in' => ':Attribute yang dipilih tidak valid.',
    'integer' => ':Attribute harus berupa bilangan bulat (tanpa titik atau koma).',
    'lt' => [
        'numeric' => ':Attribute harus lebih kecil dari :value.',
        'string' => ':Attribute harus kurang dari :value karakter.',
    ],
    'lte' => [
        'numeric' => ':Attribute harus lebih kecil dari atau sama dengan :value.',
        'string' => ':Attribute maksimal :value karakter.',
    ],
    'max' => [
        'array' => ':Attribute maksimal berisi :max item.',
        'file' => 'Ukuran :attribute maksimal :max kilobyte.',
        'numeric' => ':Attribute maksimal :max.',
        'string' => ':Attribute maksimal :max karakter.',
    ],
    'mimes' => ':Attribute harus berupa file bertipe: :values.',
    'mimetypes' => ':Attribute harus berupa file bertipe: :values.',
    'min' => [
        'array' => ':Attribute minimal berisi :min item.',
        'file' => 'Ukuran :attribute minimal :min kilobyte.',
        'numeric' => ':Attribute minimal :min.',
        'string' => ':Attribute minimal :min karakter.',
    ],
    'not_in' => ':Attribute yang dipilih tidak valid.',
    'numeric' => ':Attribute harus berupa angka.',
    'present' => ':Attribute wajib ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':Attribute wajib diisi.',
    'required_if' => ':Attribute wajib diisi jika :other bernilai :value.',
    'required_with' => ':Attribute wajib diisi jika :values diisi.',
    'same' => ':Attribute dan :other harus sama.',
    'size' => [
        'array' => ':Attribute harus berisi :size item.',
        'file' => ':Attribute harus berukuran :size kilobyte.',
        'numeric' => ':Attribute harus bernilai :size.',
        'string' => ':Attribute harus berisi :size karakter.',
    ],
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah dipakai, gunakan yang lain.',
    'uploaded' => ':Attribute gagal diunggah.',
    'url' => 'Format :attribute tidak valid.',

    /*
    |--------------------------------------------------------------------------
    | Pesan khusus untuk kolom tertentu
    |--------------------------------------------------------------------------
    | Format: 'nama_kolom' => ['aturan' => 'pesan'].
    */

    'custom' => [
        'nama_kategori' => [
            'unique' => 'Nama kategori sudah ada, gunakan nama lain.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama kolom dalam Bahasa Indonesia
    |--------------------------------------------------------------------------
    | Mengganti :attribute pada pesan. Tanpa ini, pesan berbunyi
    | "Kategori id wajib diisi." — dengan ini menjadi "Kategori wajib diisi."
    */

    'attributes' => [
        'nama_kategori' => 'nama kategori',
        'kategori_id' => 'kategori',
        'nama_produk' => 'nama produk',
        'merek' => 'merek',
        'spesifikasi' => 'spesifikasi',
        'satuan' => 'satuan',
        'harga' => 'harga',
        'detail' => 'deskripsi',
        'foto' => 'foto',
        'foto_tambahan' => 'foto tambahan',
        'foto_tambahan.*' => 'foto tambahan',
        'status' => 'status',
        'unggulan' => 'unggulan',
        'email' => 'email',
        'password' => 'password',
    ],

];
