<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Login dan logout admin (backend).
 * Nama method mengikuti pola Modul II: loginBackend, authenticateBackend, logoutBackend.
 */
class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     * GET /backend/login
     */
    public function loginBackend(): View
    {
        return view('backend.v_login.login', [
            'judul' => 'Login Admin',
        ]);
    }

    /**
     * Memproses form login.
     * POST /backend/login
     */
    public function authenticateBackend(Request $request): RedirectResponse
    {
        // Pesan error otomatis Bahasa Indonesia dari lang/id/validation.php
        $kredensial = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt():
        // 1. mencari user dengan email tersebut DAN status = 1 (aktif);
        // 2. meng-hash password yang diketik lalu membandingkannya dengan hash di database;
        // 3. jika cocok, menyimpan id user di session (user dianggap sudah login).
        // Parameter kedua: true jika kotak "Ingat saya" dicentang (login bertahan lebih lama).
        $berhasil = Auth::attempt(
            [...$kredensial, 'status' => 1],
            $request->boolean('ingat'),
        );

        if (! $berhasil) {
            // Pesan sengaja umum (tidak menyebut email atau password yang salah)
            // supaya orang iseng tidak bisa menebak email mana yang terdaftar.
            return back()
                ->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])
                ->onlyInput('email');
        }

        // Buat ulang id session setelah login (mencegah serangan "session fixation").
        $request->session()->regenerate();

        // intended(): kembali ke halaman admin yang tadi ingin dibuka sebelum diminta login;
        // jika tidak ada, ke dashboard.
        return redirect()->intended(route('backend.beranda'));
    }

    /**
     * Logout admin.
     * POST /backend/logout
     */
    public function logoutBackend(Request $request): RedirectResponse
    {
        Auth::logout();

        // Hapus semua data session dan buat token CSRF baru.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('backend.login')
            ->with('success', 'Anda berhasil logout.');
    }
}
