<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UiSmokeTest extends DuskTestCase
{
    /**
     * Navigasi dari beranda ke Daftar Bab lewat navbar, verifikasi header.
     * Source konten: resources/views/layouts/app.blade.php (link "Bab") dan resources/views/chapters/index.blade.php (h1 "Kitab {{ env('APP_NAME') }}").
     */
    public function testNavigateToChaptersFromNavbar(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitForText('Beranda', 5)
                ->clickLink('Bab')
                ->waitForLocation('/chapters', 10)
                ->waitFor('@page-title', 10)
                ->assertSeeIn('@page-title', 'Kitab')
                ->assertSeeIn('title', 'Daftar Bab')
                ->screenshot('chapters-index');
        });
    }

    /**
     * Akses halaman form pencarian dan verifikasi heading.
     * Source konten: resources/views/search/form.blade.php (h1 "Pencarian Hadits").
     */
    public function testSearchFormAccessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/search/form')
                ->waitForLocation('/search/form', 10)
                ->waitFor('@page-title', 10)
                ->assertSeeIn('@page-title', 'Pencarian Hadits')
                ->assertSeeIn('title', 'Pencarian Hadits')
                ->screenshot('search-form');
        });
    }

    /**
     * Akses halaman login dan verifikasi heading.
     * Source konten: resources/views/auth/login.blade.php (h1 "Masuk ke Akun Anda").
     */
    public function testLoginFormAccessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitForText('Masuk ke Akun Anda', 5)
                ->screenshot('login-form')
                ->assertSee('Masuk ke Akun Anda');
        });
    }
}