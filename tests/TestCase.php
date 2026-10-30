<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\View;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $fallbackDir = storage_path('framework/testing/views');
        if (!is_dir($fallbackDir)) {
            mkdir($fallbackDir, 0777, true);
        }

        $views = [
            'auth.login',
            'dashboard',
            'products.index',
            'categories.index',
            'suppliers.index',
            'movements.index',
            'alerts.index',
            'reports.index',
            'profile.edit',
        ];

        foreach ($views as $view) {
            $viewPath = $fallbackDir . '/' . str_replace('.', '/', $view) . '.blade.php';
            if (!file_exists($viewPath)) {
                @mkdir(dirname($viewPath), 0777, true);
                file_put_contents($viewPath, "<!-- test fallback placeholder for {$view} -->\n");
            }
        }

        View::addLocation($fallbackDir);
    }
}
