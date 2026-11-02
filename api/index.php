<?php

// Suppress deprecation warnings on PHP 8.4/8.5
error_reporting(E_ALL & ~E_DEPRECATED);

// Fix Symfony / Laravel determining BaseUrl as '/api' due to SCRIPT_NAME = '/api/index.php'
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Prepare writable /tmp storage for serverless runtime
$storagePath = '/tmp/storage';
if (!is_dir($storagePath . '/framework/views')) {
    @mkdir($storagePath . '/framework/views', 0777, true);
    @mkdir($storagePath . '/framework/sessions', 0777, true);
    @mkdir($storagePath . '/framework/cache/data', 0777, true);
    @mkdir($storagePath . '/logs', 0777, true);
    @mkdir($storagePath . '/app/public', 0777, true);
}

require __DIR__ . "/../public/index.php";
