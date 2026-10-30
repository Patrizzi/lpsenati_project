<?php

// Create required temporary directories for serverless environment in Vercel
$directories = [
    '/tmp/views',
    '/tmp/sessions',
    '/tmp/cache',
    '/tmp/logs',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Forward Vercel request to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
