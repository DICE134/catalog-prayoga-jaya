<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Alamat "/" harus dialihkan ke halaman beranda (Tahap 2).
     */
    public function test_alamat_utama_dialihkan_ke_beranda(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('beranda'));
    }
}
