<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model akun admin. Terhubung ke tabel `users`.
 * Extends Authenticatable agar bisa dipakai untuk login (Auth::attempt) di Tahap 5.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Kolom yang TIDAK boleh diisi massal (lewat User::create([...])).
     * Selain 'id', semua kolom boleh diisi. Pola ini sama dengan modul.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * Kolom yang disembunyikan saat data diubah ke array/JSON,
     * supaya hash password tidak ikut tampil.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Konversi tipe data otomatis.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // password otomatis di-hash (bcrypt) saat disimpan
            'role' => 'integer',
            'status' => 'integer',
        ];
    }
}
