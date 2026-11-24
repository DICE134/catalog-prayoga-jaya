<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji login, logout, dan perlindungan halaman admin (Tahap 5).
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_login_terbuka(): void
    {
        $this->get(route('backend.login'))
            ->assertOk()
            ->assertSee('Login Admin');
    }

    public function test_admin_bisa_login_dengan_data_benar(): void
    {
        $admin = User::factory()->create(['email' => 'admin@contoh.test', 'password' => 'rahasia123']);

        $this->post(route('backend.login.proses'), ['email' => 'admin@contoh.test', 'password' => 'rahasia123'])
            ->assertRedirect(route('backend.beranda'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        User::factory()->create(['email' => 'admin@contoh.test', 'password' => 'rahasia123']);

        $this->from(route('backend.login'))
            ->post(route('backend.login.proses'), ['email' => 'admin@contoh.test', 'password' => 'salah'])
            ->assertRedirect(route('backend.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        User::factory()->nonaktif()->create(['email' => 'admin@contoh.test', 'password' => 'rahasia123']);

        $this->post(route('backend.login.proses'), ['email' => 'admin@contoh.test', 'password' => 'rahasia123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_email_dan_password_wajib_diisi(): void
    {
        $this->post(route('backend.login.proses'), [])
            ->assertSessionHasErrors([
                'email' => 'Email wajib diisi.',
                'password' => 'Password wajib diisi.',
            ]);
    }

    public function test_tamu_dialihkan_ke_login_saat_membuka_halaman_admin(): void
    {
        $this->get(route('backend.beranda'))->assertRedirect(route('backend.login'));
        $this->get(route('backend.kategori.index'))->assertRedirect(route('backend.login'));
        $this->get('/backend')->assertRedirect(route('backend.login'));
    }

    public function test_tamu_tidak_bisa_menghapus_kategori(): void
    {
        $this->delete('/backend/kategori/1')->assertRedirect(route('backend.login'));
    }

    public function test_admin_yang_sudah_login_dialihkan_dari_form_login_ke_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('backend.login'))
            ->assertRedirect(route('backend.beranda'));
    }

    public function test_dashboard_menampilkan_nama_admin(): void
    {
        $this->actingAs(User::factory()->create(['nama' => 'Pak Prayoga']))
            ->get(route('backend.beranda'))
            ->assertOk()
            ->assertSee('Selamat datang, Pak Prayoga');
    }

    public function test_admin_bisa_logout(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('backend.logout'))
            ->assertRedirect(route('backend.login'));

        $this->assertGuest();
    }
}
