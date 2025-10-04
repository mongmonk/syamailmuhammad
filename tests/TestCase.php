<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;

/**
 * Bootstrap aplikasi untuk testing agar migrasi dijalankan (Fix: "no such table")
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Create the application instance for tests.
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
