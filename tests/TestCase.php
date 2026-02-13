<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * This implementation boots the Laravel application for feature tests.
     */
    public function createApplication()
    {
        // Ensure an application key exists for testing environments
        if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'] ?? null) && empty($_SERVER['APP_KEY'] ?? null)) {
            try {
                $key = 'base64:' . base64_encode(random_bytes(32));
            } catch (\Exception $e) {
                $key = 'base64:' . base64_encode(openssl_random_pseudo_bytes(32));
            }
            putenv('APP_KEY=' . $key);
            $_ENV['APP_KEY'] = $key;
            $_SERVER['APP_KEY'] = $key;
        }

        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
